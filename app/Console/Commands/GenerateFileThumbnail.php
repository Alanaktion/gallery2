<?php

namespace App\Console\Commands;

use App\Traits\MakesThumbnails;
use App\Traits\MimeTypes;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GenerateFileThumbnail extends Command
{
    use MakesThumbnails;
    use MimeTypes;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbs:generate-file
        {path?* : Files to generate thumbnails for, relative to base directory}
        {--s|scale=* : The scale to generate thumbnails at (1-3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate thumbnail images for individual files.';

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
            $type = explode('/', $this->getMimeTypeFromExtension($path), 2)[0];
            try {
                if ($type == 'video' && $this->option('video')) {
                    $framePath = $this->videoImage($path);
                    foreach ($scales as $scale) {
                        $this->makeFileThumb($path, $scale, $framePath);
                    }
                    unlink($framePath);
                } elseif ($type == 'image') {
                    foreach ($scales as $scale) {
                        $this->makeFileThumb($path, $scale);
                    }
                }
            } catch (Exception $e) {
                $errors[] = $e;
            }
        }
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
}
