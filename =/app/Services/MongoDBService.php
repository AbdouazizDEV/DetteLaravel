<?php

namespace App\Services;

use MongoDB\Client;
use App\Services\Contracts\DatabaseServiceInterface;

class MongoDBService implements DatabaseServiceInterface
{
    protected $client;
    protected $database;

    public function __construct()
    {
        $mongoUri = config('services.mongodb.uri'); // Utilise la chaîne de connexion complète incluant la base de données
        $this->client = new Client($mongoUri);

        // MongoDB sélectionnera automatiquement la base de données à partir de l'URI
        $this->database = $this->client->selectDatabase($this->getDatabaseNameFromUri($mongoUri));
    }

    // Extraire le nom de la base de données de l'URI
    protected function getDatabaseNameFromUri(string $mongoUri): string
    {
        $parsedUrl = parse_url($mongoUri);
        return ltrim($parsedUrl['path'], '/');
    }

    public function getCollection(string $collectionName)
    {
        return $this->database->selectCollection($collectionName)->find()->toArray();
    }

    public function getDocument(string $collectionName, string $documentId)
    {
        return $this->database->selectCollection($collectionName)->findOne(['_id' => $documentId]);
    }

    
    public function saveDocument(string $collectionName, string $documentId, array $data)
    {
        //dd($data, $collectionName, $documentId);
        $this->database->selectCollection($collectionName)->updateOne(
            filter: ['_id' => $documentId],
            update: ['$set' => $data],
            options: ['upsert' => true],
        );
    }


}