<?php
// app/Services/DetteService.php

namespace App\Services;

use App\Models\Article;
use App\Models\Dette;
use App\Repositories\Contracts\DetteRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Repositories\DetteRepository;

class DetteService
{
    protected $detteRepository;

    public function __construct(DetteRepository $detteRepository)
    {
        $this->detteRepository = $detteRepository;
    }

    public function createDette(array $validatedData)
    {
        $articles = $validatedData['articles'];
        $errors = [];
        $validArticles = [];

        DB::beginTransaction();

        try {
            foreach ($articles as $index => $articleData) {
                $article = Article::find($articleData['articleId']);

                if (!$article) {
                    $errors[] = "L'article avec l'ID {$articleData['articleId']} n'existe pas.";
                    continue; // Skip this article
                }

                if ($article->quantite_stock < $articleData['qteVente']) {
                    $errors[] = "La quantité demandée pour l'article {$article->libelle} dépasse le stock disponible.";
                    continue; // Skip this article
                } else if ($article->quantite_stock == 0) {
                    $errors[] = "La quantité demandée pour l'article {$article->libelle} est supérieure au stock disponible.";
                    continue; // Skip this article
                } else {
                    // Calculer le montant de la dette : on multiplie la quantité de vente par le prix de vente de chaque article et on additionne les montants, c'est cette dernière qu'on doit enregistrer dans la table dette
                    $validatedData['montant'] += $articleData['qteVente'] * $articleData['prixVente'];
                }

                // Décrémenter le stock et ajouter à la liste des articles valides
                $article->decrement('quantite_stock', $articleData['qteVente']);
                $validArticles[] = $articleData;
            }

            if (empty($validArticles)) {
                throw ValidationException::withMessages($errors);
            }

            // Créer la dette avec les articles valides
            $validatedData['articles'] = $validArticles;
            $dette = $this->detteRepository->create($validatedData);

            DB::commit();

            $response = [
                'status' => 201,
                'message' => 'Dette enregistrée avec succès',
                'data' => $dette
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
                $response['message'] = 'Dette enregistrée avec des avertissements';
            }

            return $response;

        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof ValidationException) {
                throw $e;
            }

            throw new \Exception("Erreur lors de l'enregistrement de la dette : " . $e->getMessage());
        }
    }

    /**
     * Effectue un paiement pour une dette donnée
     */
    public function effectuerPaiement($detteId, $montant)
    {
        return DB::transaction(function () use ($detteId, $montant) {
            $dette = $this->detteRepository->find($detteId);

            if (!$dette) {
                return ['message' => 'Dette inexistante'];
            }

            if ($dette->montant_restant < $montant) {
                return ['message' => 'Le montant versé ne doit pas dépasser le montant restant de la dette'];
            }

            // Ajouter un paiement à la dette
            $this->detteRepository->ajouterPaiement($detteId, $montant);

            // Mise à jour du statut si la dette est complètement payée
            if ($dette->montant_restant - $montant <= 0) {
                $this->detteRepository->marquerCommeSoldee($detteId);
            }

            return true;
        });
    }
}
