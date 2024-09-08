<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Dette;
use App\Repositories\Contracts\DetteRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DetteService
{
    protected $detteRepository;

    public function __construct(DetteRepositoryInterface $detteRepository)
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
                }else if ($article->quantite_stock == 0) {
                    $errors[] = "La quantité demandée pour l'article {$article->libelle} est supérieure au stock disponible.";
                    continue; // Skip this article
                }else{
                    //calculer le montant de la dette : on miltiplie la quantité de vente par le prix de vente de chaque article et on additionne les montants, c'est cette dérniére qu'on doit enregistrer dans la table dette
                    $validatedData['montant'] += $articleData['qteVente'] * $articleData['prixVente'];
                    //$article->quantite_stock -= $articleData['qteVente'];//on soustrait la quantité de vente de la quantité de stock pour pouvoir enregistrer la dette
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
}
