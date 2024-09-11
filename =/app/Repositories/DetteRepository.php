<?php
// app/Repositories/DetteRepository.php

namespace App\Repositories;

use App\Models\Dette;
use App\Models\Paiement;
use App\Repositories\Contracts\DetteRepositoryInterface;

class DetteRepository implements DetteRepositoryInterface
{
    public function create(array $data): Dette
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

    /**
     * Trouve une dette par son ID
     */
    public function find($detteId)
    {
        return Dette::find($detteId);
    }

    /**
     * Ajoute un paiement pour une dette
     */
    public function ajouterPaiement($detteId, $montant)
    {
        return Paiement::create([
            'dette_id' => $detteId,
            'montant' => $montant,
            'date' => now(),
        ]);
    }

    /**
     * Marque une dette comme soldée
     */
    public function marquerCommeSoldee($detteId)
    {
        $dette = $this->find($detteId);
        $dette->montant = 0; // Mise à jour du montant
        $dette->save();
    }
}
