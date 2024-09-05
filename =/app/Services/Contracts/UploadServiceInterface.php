<?php

namespace App\Services;
use Illuminate\Http\UploadedFile;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface UploadServiceInterface {
    public function uploadImage(UploadedFile $file, string $directory = 'photos'): string;
    public function getImageAsBase64(string $path): string;
}