<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class PublicMedia
{
    public static function url(?string $path): ?string
    {
        $path = ltrim((string) $path, '/');

        if ($path === '') {
            return null;
        }

        if (preg_match('#^(https?:)?//#i', $path) === 1) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        self::publish($path);

        return asset('storage/'.$path);
    }

    public static function publish(string $path): void
    {
        $path = ltrim($path, '/');
        $publicFile = public_path('storage/'.$path);
        $storageFile = storage_path('app/public/'.$path);

        if (is_file($publicFile) || ! is_file($storageFile)) {
            return;
        }

        File::ensureDirectoryExists(dirname($publicFile));
        File::copy($storageFile, $publicFile);
    }
}
