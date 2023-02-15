<?php

namespace App\Console\Commands;

use App\Traits\MakesThumbnails;
use App\Traits\MimeTypes;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GenerateThumbnails extends Command
{
    use MakesThumbnails;
    use MimeTypes;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbs:generate
        {path?* : Directories to generate thumbnails for, relative to base directory}
        {--s|scale=* : The scale to generate thumbnails at (1-3)}
        {--d|video : Generate video thumbnails with ffmpeg}
        {--r|recursive : Read directories recursively}
        {--c|concurrent : Generate thumbnails concurrently}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate thumbnail images for directories in the gallery.';

    /**
     * The number of allowed concurrent processes.
     */
    protected $processLimit = 1;

    /**
     * The current popen() resources.
     */
    protected $processes = [];

    /**
     * The number of files allowed in the queue before starting a new process.
     */
    protected $queueLimit = 10;

    /**
     * The files in the queue to be added to the next process.
     */
    protected $queue = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $paths = $this->argument('path');
        if ($paths) {
            $paths = array_map(fn($p) => realpath(config('gallery.path') . Str::start($p, '/')), $paths);
        } else {
            $paths = [realpath(config('gallery.path'))];
        }

        Validator::make(['scale' => $this->option('scale')], [
            'scale' => 'nullable|array',
            'scale.*' => 'int|in:1,2,3',
        ])->validate();
        $scales = $this->option('scale') ?: [1, 2];

        $concurrent = $this->option('concurrent');
        if ($concurrent) {
            if (PHP_OS == 'Darwin') {
                $this->processLimit = max(1, (int)shell_exec('sysctl -n hw.ncpu'));
            } elseif (PHP_OS == 'WINNT') {
                $this->processLimit = max(1, (int)shell_exec('wmic cpu get NumberOfCores | FINDSTR /V "NumberOfCores"'));
            } else {
                $this->processLimit = max(1, (int)shell_exec('nproc'));
            }
        }

        /** @var Exception[] */
        $errors = [];
        foreach ($paths as $path) {
            $this->info($path);
            $files = $this->getDirFiles($path, $this->option('recursive'));
            $this->withProgressBar($files, function ($file) use ($scales, $concurrent, &$errors) {
                try {
                    if ($concurrent) {
                        $this->queueFile($file['path']);
                    } else {
                        if ($file['type'] == 'video' && $this->option('video')) {
                            $framePath = $this->videoImage($file['path']);
                            foreach ($scales as $scale) {
                                $this->makeFileThumb($file['path'], $scale, $framePath);
                            }
                            unlink($framePath);
                        } elseif ($file['type'] == 'image') {
                            foreach ($scales as $scale) {
                                $this->makeFileThumb($file['path'], $scale);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = $e;
                }
            });
        }

        // Start any remaining processes, and wait for them to finish.
        if ($concurrent) {
            if ($this->queue) {
                $this->startProcess();
            }
            foreach ($this->processes as $process) {
                pclose($process);
            }
        }

        foreach ($errors as $e) {
            $this->error($e->getMessage());
        }
        if ($errors) {
            return 1;
        }
        return 0;
    }

    protected function getDirFiles(string $dir, bool $recursive = false): array
    {
        $paths = [];
        if ($recursive) {
            $rii = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $dir,
                    RecursiveDirectoryIterator::FOLLOW_SYMLINKS
                ),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ($rii as $file) {
                /** @var \SplFileInfo $file */
                if ($file->isDir()) {
                    continue;
                }
                $paths[] = $file->getRealPath();
            }
        } else {
            $dh = dir($dir);
            while (($item = $dh->read()) !== false) {
                if (!is_dir($dh->path . '/' . $item)) {
                    $paths[] = realpath($dh->path . '/' . $item);
                }
            }
            $dh->close();
        }

        $files = [];
        foreach ($paths as $path) {
            if ($path === false) {
                continue;
            }
            $mime = $this->getMimeTypeFromExtension($path);
            $files[] = [
                'path' => $path,
                'type' => explode('/', $mime, 2)[0],
            ];
        }

        return $files;
    }

    /**
     * @param string|null $imgPath The path to the original file, if different from the path to the source image/frame.
     */
    protected function makeFileThumb(string $filePath, int $scale, ?string $imgPath = null): void
    {
        $hash = sha1($filePath);
        $thumbFile = $this->getFilename($hash, $scale);
        if (Storage::exists("thumbs/$thumbFile")) {
            return;
        }
        $size = $this->getSize($scale);
        $data = $this->makeThumb($imgPath ?? $filePath, $size);
        Storage::put("thumbs/$thumbFile", $data);
    }

    /**
     * Add a file to the queue, starting a job if the queue limit is reached.
     */
    protected function queueFile(string $filePath)
    {
        $this->queue[] = $filePath;
        if (count($this->queue) >= $this->queueLimit) {
            $this->startProcess();
        }
    }

    /**
     * Start an asynchronous process for the currently queued files.
     */
    protected function startProcess()
    {
        // Build path and scale args
        $pathArgs = [];
        foreach ($this->queue as $file) {
            $pathArgs[] = escapeshellarg($file['path']);
        }
        $scaleArgs = [];
        $scales = $this->option('scale') ?: [1, 2];
        foreach ($scales as $scale) {
            $scaleArgs[] = '--scale=' . $scale;
        }

        // Start process
        $this->processes[] = popen(
            "php artisan thumbs:generate-file " . implode(' ', $scaleArgs) . ' ' . implode(' ', $pathArgs),
            'r'
        );

        // Clear the queue
        $this->queue = [];

        // Stop the first job in the list if there are too many running jobs
        if (count($this->processes) > $this->processLimit) {
            pclose($this->processes[0]);
            $this->processes = array_slice($this->processes, 1);
        }
    }
}
