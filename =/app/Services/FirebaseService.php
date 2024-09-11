<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use App\Services\Contracts\DatabaseServiceInterface;

class FirebaseService implements DatabaseServiceInterface
{
    protected $database;

    public function __construct()
    {
        $serviceAccountPath = storage_path('app/gestion-dette-firebase-adminsdk-d2655-79b76b9866.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri('https://gestion-dette-default-rtdb.firebaseio.com/');

        $this->database = $firebase->createDatabase();
    }

    // Méthode pour récupérer une collection (ici une référence dans Firebase Realtime Database)
    public function getCollection(string $collectionName)
    {
        $reference = $this->database->getReference($collectionName);
        return $reference->getValue();
    }

    // Méthode pour récupérer un document spécifique (ici un chemin spécifique dans Firebase Realtime Database)
    public function getDocument(string $collectionName, string $documentId)
    {
        $reference = $this->database->getReference("{$collectionName}/{$documentId}");
        return $reference->getValue();
    }

    // Méthode pour sauvegarder un document (ici pour insérer des données dans Firebase Realtime Database)
    public function saveDocument(string $collectionName, string $documentId, array $data)
    {
        $reference = $this->database->getReference("{$collectionName}/{$documentId}");
        $reference->set($data);
    }
}