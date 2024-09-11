<?php

namespace App\Services\Contracts;

interface DatabaseServiceInterface
{
    public function getCollection(string $collectionName);
    public function getDocument(string $collectionName, string $documentId);
    public function saveDocument(string $collectionName, string $documentId, array $data);
}