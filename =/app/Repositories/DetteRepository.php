<?php

namespace App\Repositories;

use App\Models\Dette;
use App\Repositories\Contracts\DetteRepositoryInterface;
class DetteRepository implements DetteRepositoryInterface
{
    public function create(array $data):Dette
    {
        $dette = new Dette();
        $dette->montant = $data['montant'];
        $dette->clientId = $data['clientId'];
        $dette->date = now();
        $dette->save();

        foreach ($data['articles'] as $articleData) {
            $dette->articles()->attach($articleData['articleId'], [
                'qteVente' => $articleData['qteVente'],
                'prixVente' => $articleData['prixVente'],
            ]);
        }

        if (isset($data['paiement']['montant'])) {
            $dette->paiements()->create([
                'montant' => $data['paiement']['montant'],
                'date' => now(),
            ]);
        }

        return $dette->load(['client', 'articles', 'paiements']);
    }
}
