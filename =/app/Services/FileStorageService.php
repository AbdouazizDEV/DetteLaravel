<?php

namespace App\Services;

use App\Services\Contracts\FileStorageServiceInterface;
use Illuminate\Support\Facades\Storage;
use App\Services\Contracts\FileStorageServiceException;
class FileStorageService implements FileStorageServiceInterface
{
    public function store($file, $path)
    {
        return Storage::disk('public')->put($path, $file);
    }

    public function getBase64($path)
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }
        $file = Storage::disk('public')->get($path);
        return base64_encode($file);
    }
}
