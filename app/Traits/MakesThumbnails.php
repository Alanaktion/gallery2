<?php

namespace App\Traits;

trait MakesThumbnails
{
    protected function getFilename(string $hash, int $scale = 1)
    {
        if ($scale > 1) {
            return "$hash@{$scale}x.jpg";
        }
        return "$hash.jpg";
    }

    protected function getSize(int $scale = 1)
    {
        return 192 * $scale;
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

    protected function videoImage(string $path)
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
}
