<?php

namespace App\Http\Controllers;

use App\Traits\MimeTypes;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use MimeTypes;

    public function __construct()
    {
        if (config('gallery.auth')) {
            $this->middleware('auth');
        }
    }

    public function app()
    {
        return view('app');
    }

    public function dir(Request $request)
    {
        $dir = $request->input('dir');
        $base = config('gallery.path');
        $path = $base . $dir;

        $real = realpath($path);
        if (strpos($real, rtrim($base, '/')) !== 0) {
            return abort(404, 'Invalid directory requested.');
        }

        if (!is_dir($path)) {
            if ($dir === null) {
                if ($request->expectsJson()) {
                    return abort(500, 'Your GALLERY_PATH setting is invalid, check your .env file!');
                }
                return response()->view('errors.config', [
                    'message' => 'Your GALLERY_PATH setting is invalid, check your .env file!',
                ], 200);
            }
            return abort(404);
        }
        $items = $this->readDir($path);
        return [
            'dir' => $dir,
            'items' => $items,
            'title' => $dir ? basename($dir) : null,
        ];
    }

    protected function readDir(string $dir, bool $includeHidden = false)
    {
        $dh = dir($dir);
        $files = [];
        $directories = [];
        while (($item = $dh->read()) !== false) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$includeHidden && $item[0] == '.') {
                continue;
            }
            $path = $dh->path . '/' . $item;
            if (is_dir($path)) {
                $directories[] = [
                    'name' => $item,
                ];
            } elseif (is_file($path)) {
                $mime = $this->getMimeTypeFromExtension($path);
                $files[] = [
                    'name' => $item,
                    'size' => filesize($path),
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
