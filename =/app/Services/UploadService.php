<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Upload an image and store it in the specified directory.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string The path to the stored image
     */
    public function uploadImage(UploadedFile $file, string $directory = 'photos'): string
    {
        return $file->store($directory, 'public');
    }

    /**
     * Retrieve the image as a base64 encoded string.
     *
     * @param string $path
     * @return string
     */
    public function getImageAsBase64(string $path): string
    {
        $image = Storage::disk('public')->get($path);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $base64 = base64_encode($image);
        // dd($base64);
        
        return 'data:image/' . $type . ';base64,' . $base64;
    }
}