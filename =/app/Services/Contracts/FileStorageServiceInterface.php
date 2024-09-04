<?php
// /app/Services/Contracts/FileStorageServiceInterface.php
namespace App\Services\Contracts;

interface FileStorageServiceInterface
{
    public function store($file, $path);
    public function getBase64($path);
}