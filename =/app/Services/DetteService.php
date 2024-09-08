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

        DB::beginTransaction();

        try {
            foreach ($articles as $index => $articleData) {
                $article = Article::find($articleData['articleId']);

                if (!$article) {
                    $errors[] = "L'article avec l'ID {$articleData['articleId']} n'existe pas.";
                    unset($articles[$index]);
                    continue;
                }

                if ($article->quantite_stock < $articleData['qteVente']) {
                    $errors[] = "La quantité demandée pour l'article {$article->libelle} dépasse le stock disponible.";
                    unset($articles[$index]);
                    continue;
                }

                // Décrémenter le stock
                $article->decrement('quantite_stock', $articleData['qteVente']);
            }

            if (empty($articles)) {
                throw ValidationException::withMessages($errors);
            }

            $validatedData['articles'] = $articles;

            // Utiliser le repository pour créer la dette
            $dette = $this->detteRepository->create($validatedData);

            DB::commit();

            return $dette;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof ValidationException) {
                throw $e;
            }

            throw new \Exception("Erreur lors de l'enregistrement de la dette : " . $e->getMessage());
        }
    }
}
