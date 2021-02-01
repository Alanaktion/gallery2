<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    public function __construct()
    {
        if (config('gallery.auth')) {
            $this->middleware('auth');
        }
    }

    public function proxy(string $file)
    {
        $path = config('gallery.path') . $file;
        if (!is_file($path)) {
            return abort(404);
        }
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
    }

    public function thumbnail(string $scale, string $file)
    {
        $scale = (int)$scale;
        if ($scale > 3) {
            return abort(404);
        }
        $path = config('gallery.path') . $file;
        $hash = sha1($file);

        $file = "$hash.jpg";
        if ($scale > 1) {
            $file = "$hash@{$scale}x.jpg";
        }

        if (Storage::exists("thumbs/$file")) {
            return response(Storage::get("thumbs/$file"), 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'max-age=31536000',
            ]);
        }

        $type = explode('/', mime_content_type($path), 2)[0];
        if ($type == 'video') {
            $path = $this->videoImage($path);
        }

        $size = 192 * $scale;
        $src = Image::make($path);
        $w = $src->width();
        $h = $src->height();
        if ($w > $h) {
            $width = null;
            $height = $size;
        } else {
            $width = $size;
            $height = null;
        }
        $thumb = $src->resize($width, $height, function (Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas($size, $size, 'center', false, '#000');
        $data = $thumb->encode('jpg', 80);
        Storage::put("thumbs/$file", $data);
        return response($data, 201, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'max-age=31536000',
        ]);
    }

    public function videoImage(string $path)
    {
        $hash = sha1($path);
        $framePath = storage_path("app/{$hash}.png");

        // Determine video duration
        $path = escapeshellarg($path);
        $duration = trim(shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path"));

        // Seek to 30% of video duration, or 10 seconds if duration is unknown.
        $seconds = $duration ? floor($duration * 0.30) : 10;
        exec("ffmpeg -ss $seconds -i $path -vframes 1 -vcodec png -an -y " . escapeshellarg($framePath));

        $src = Image::make($framePath);
        unlink($framePath);
        return $src;
    }

    public function video(string $file)
    {
        $path = config('gallery.path') . $file;
        if (!is_file($path)) {
            return abort(404);
        }
        return view('video.show', [
            'path' => $file,
            'mime' => mime_content_type($path),
        ]);
    }
}
