<?php

namespace App\Console\Commands;

use App\Traits\MakesThumbnails;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GenerateThumbnails extends Command
{
    use MakesThumbnails;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbs:generate
        {path* : Directories to generate thumbnails for}
        {--scale=* : The scale to generate thumbnails at (1-3)}
        {--video : Generate video thumbnails with ffmpeg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate thumbnail images for files in the gallery.';

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

        /** @var Exception[] */
        $errors = [];
        foreach ($paths as $path) {
            $this->info($path);
            $files = $this->getDirFiles($path);
            $this->withProgressBar($files, function ($file) use ($scales) {
                try {
                    if ($file['type'] == 'video' && $this->option('video')) {
                        $framePath = $this->videoImage($file['path']);
                        foreach ($scales as $scale) {
                            $this->makeFileThumb($framePath, $scale);
                        }
                        unlink($framePath);
                    } elseif ($file['type'] == 'image') {
                        foreach ($scales as $scale) {
                            $this->makeFileThumb($file['path'], $scale);
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = $e;
                }
            });
        }

        foreach ($errors as $e) {
            $this->error($e->getMessage());
        }
        if ($errors) {
            return 1;
        }
        return 0;
    }

    protected function getDirFiles(string $dir): array
    {
        $dh = dir($dir);
        $files = [];
        $directories = [];
        while (($item = $dh->read()) !== false) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (is_dir($dh->path . '/' . $item)) {
                $directories[] = [
                    'name' => $item,
                ];
            } else {
                $absolute = realpath($dh->path . '/' . $item);
                $mime = mime_content_type($absolute);
                $files[] = [
                    'name' => $item,
                    'path' => $absolute,
                    'type' => explode('/', $mime, 2)[0],
                ];
            }
        }
        return $files;
    }

    protected function makeFileThumb(string $file, int $scale): void
    {
        $hash = sha1($file);
        $thumbFile = $this->getFilename($hash, $scale);
        if (Storage::exists("thumbs/$thumbFile")) {
            return;
        }
        $size = $this->getSize($scale);
        $data = $this->makeThumb($file, $size);
        Storage::put("thumbs/$thumbFile", $data);
    }
}
