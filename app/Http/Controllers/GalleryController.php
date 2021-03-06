<?php

namespace App\Http\Controllers;

class GalleryController extends Controller
{
    public function __construct()
    {
        if (config('gallery.auth')) {
            $this->middleware('auth');
        }
    }

    public function index(?string $dir = null)
    {
        $path = config('gallery.path') . $dir;
        if (!is_dir($path)) {
            if ($dir === null) {
                return response()->view('errors.config', [
                    'message' => 'Your GALLERY_PATH setting is invalid, check your .env file!',
                ], 200);
            }
            return abort(404);
        }
        $items = $this->readDir($path);
        return view('gallery', [
            'dir' => $dir,
            'items' => $items,
            'title' => $dir ? basename($dir) . ' - ' . config('app.name', 'Gallery') : null,
        ]);
    }

    protected function readDir(string $dir)
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
                    'size' => filesize($absolute),
                    'mimeType' => $mime,
                    'type' => explode('/', $mime, 2)[0],
                ];
            }
        }

        // Sort contents
        usort($files, fn($a, $b) => strnatcmp($a['name'], $b['name']));
        usort($directories, fn($a, $b) => strnatcmp($a['name'], $b['name']));

        return [
            'files' => $files,
            'directories' => $directories,
        ];
    }
}
