<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

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

        $type = substr($mime, 0, strpos($mime, '/'));
        if ($type == 'image') {
            readfile($path);
            return;
        }

        // https://gist.github.com/codler/3906826
        $fp = @fopen($path, 'rb');
        $size = filesize($path);
        $length = $size;
        $start = 0;
        $end = $size - 1;

        header('Accept-Range: bytes');

        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end = $end;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }

        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        $buffer = 1024 * 8;
        while (!feof($fp) && ($p = ftell($fp)) <= $end) {
            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($fp, $buffer);
            flush();
        }

        fclose($fp);
    }

    public function thumbnail(string $scale, string $file)
    {
        $scale = (int)$scale;
        if ($scale > 3) {
            return abort(404);
        }
        $path = config('gallery.path') . $file;
        $hash = sha1($path);

        $thumbFile = "$hash.jpg";
        if ($scale > 1) {
            $thumbFile = "$hash@{$scale}x.jpg";
        }

        if (Storage::exists("thumbs/$thumbFile")) {
            return response(Storage::get("thumbs/$thumbFile"), 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'max-age=31536000',
            ]);
        }

        $type = explode('/', mime_content_type($path), 2)[0];
        $size = 192 * $scale;
        if ($type == 'video') {
            $framePath = $this->videoImage($path);
            $data = $this->makeThumb($framePath, $size);
            unlink($framePath);
        } else {
            $data = $this->makeThumb($path, $size);
        }

        Storage::put("thumbs/$thumbFile", $data);
        return response($data, 201, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'max-age=31536000',
        ]);
    }

    protected function makeThumb(string $filePath, int $size): string
    {
        $img = @imagecreatefromstring(file_get_contents($filePath));
        if (!$img) {
            throw new \Exception('Unable to load source image for thumbnail creation.');
        }

        $res = imagecreatetruecolor($size, $size);
        $w = imagecolorallocate($res, 64, 64, 64);
        imagefill($res, 0, 0, $w);

        // Get smaller of image's dimensions
        $ix = imagesx($img);
        $iy = imagesy($img);
        $d = ($ix > $iy) ? $iy : $ix;

        // Crop, resize, and copy from source image
        imagecopyresampled(
            $res,
            $img,
            0,
            0,
            floor(($ix - $d) / 2),
            floor(($iy - $d) / 2),
            $size,
            $size,
            $d,
            $d
        );
        imagedestroy($img);

        ob_start();
        imagejpeg($res);
        $data = ob_get_clean();
        imagedestroy($res);
        return $data;
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

        return $framePath;
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
