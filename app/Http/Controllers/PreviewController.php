<?php

namespace App\Http\Controllers;

class PreviewController extends Controller
{
    public function __construct()
    {
        if (config('gallery.auth')) {
            $this->middleware('auth');
        }
    }

    public function view(string $file)
    {
        $path = config('gallery.path') . $file;
        if (!is_file($path)) {
            return abort(404);
        }
        $mime = mime_content_type($path);
        $type = substr($mime, 0, strpos($mime, '/'));
        return view('viewer', [
            'path' => $file,
            'mime' => $mime,
            'type' => $type,
        ]);
    }

    public function video(string $file)
    {
        $path = config('gallery.path') . $file;
        if (!is_file($path)) {
            return abort(404);
        }
        return view('video', [
            'path' => $file,
            'mime' => mime_content_type($path),
        ]);
    }
}
