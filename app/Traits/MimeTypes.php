<?php

namespace App\Traits;

trait MimeTypes
{
    /**
     * Guess a MIME type based on the extension of a file.
     *
     * This is a much faster alternative to using mime_content_type()
     */
    protected function getMimeTypeFromExtension(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($ext) {
            '3gp' => 'video/3gpp',
            '3g2' => 'video/3gpp2',
            'avi' => 'video/x-msvideo',
            'bmp' => 'image/bmp',
            'dib' => 'image/bmp',
            'flv' => 'video/x-flv',
            'gif' => 'image/gif',
            'htm' => 'text/html',
            'html' => 'text/html',
            'ico' => 'image/vnd.microsoft.icon',
            'jfif' => 'image/pjpeg',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'm4v' => 'video/mp4',
            'mov' => 'video/quicktime',
            'mp2' => 'video/mpeg',
            'mp4' => 'video/mp4',
            'mpa' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpv2' => 'video/mpeg',
            'ogv' => 'video/ogg',
            'png' => 'image/png',
            'qt' => 'video/quicktime',
            // 'svg' => 'image/svg+xml',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'ts' => 'video/mp2t',
            'txt' => 'text/plain',
            'webm' => 'video/webm',
            'webp' => 'image/webp',
            'wmv' => 'video/x-ms-wmv',
            default => 'application/octet-stream',
        };
    }
}
