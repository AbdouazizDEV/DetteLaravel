<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Article_S;
use App\Http\Requests\UpdateStockRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Services\Contracts\ArticleServiceInterface;
use App\Services\ArticleService; // Assuming you have an ArticleService class

class ArticleController extends Controller
{
    use ApiResponse;
   
    // GET /api/v1/articles : Récupérer tous les articles
    /* public function index(Request $request)
    {
        // Récupération du paramètre 'disponible' depuis la requête
        $disponible = $request->query('disponible');
        
        // Récupération du paramètre 'per_page' pour la pagination
        $perPage = $request->query('per_page', 10); // par défaut 10 articles par page

        // Filtrer et paginer les articles en fonction de la disponibilité
        if ($disponible === 'oui') {
            $articles = Article::where('qteStock', '>', 0)->paginate($perPage);
        } elseif ($disponible === 'non') {
            $articles = Article::where('qteStock', '=', 0)->paginate($perPage);
        } else {
            // Si aucun paramètre de filtre n'est fourni, paginer tous les articles
            $articles = Article::paginate($perPage);
        }

        // Retourner la réponse paginée
        return $this->successResponse($articles, 'Liste des articles récupérée avec succès.');
    } */
    private $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index()
    {
        return $this->articleService->all();
    }

    
    // GET /api/v1/articles/{id} : Récupérer un article spécifique
    public function show($id)
    {
        $article = Article::find($id);

        if ($article) {
            return response()->json([
                'status' => 200,
                'data' => $article,
                'message' => 'Article trouvé',
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé',
            ], 411);
        }
    }
    
    // POST /api/v1/articles : Ajouter un nouvel article ou mettre à jour la quantité en stock
    public function store(StoreArticleRequest $request)
    {
        // Vérifier si un article avec le même libellé existe déjà
        $existingArticle = Article::where('libelle', $request->libelle)->first();

        if ($existingArticle) {
            // Si l'article existe, mettre à jour la quantité en stock
            $existingArticle->quantite_stock += $request->quantite_stock;
            $existingArticle->save();

            return $this->successResponse($existingArticle, 'Article existant mis à jour avec succès. Quantité en stock augmentée.');
        } else {
            // Si l'article n'existe pas, créer un nouvel article
            $article = Article::create($request->validated());

            return $this->successResponse($article, 'Article ajouté avec succès.', 201);
        }
    }

    protected function successResponse($data, $message, $code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $code);
    }
     public function findByLibelle(Request $request)
    {
        $libelle = $request->input('libelle');
        $article = Article::where('libelle', $libelle)->first();
    
        if ($article) {
            return response()->json([
                'status' => 200,
                'data' => $article,
                'message' => 'Article trouvé',
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé',
            ], 411);
        }
    }
    
    // PUT|PATCH /api/v1/articles/{id} : Mettre à jour un article spécifique
    public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);

        $article->update($request->validated());

        return $this->successResponse($article, 'Article mis à jour avec succès.');
    }
    
    // DELETE /api/v1/articles/{id} : Supprimer un article (Soft Delete)
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return $this->successResponse(null, 'Article supprimé avec succès.');
    }
    public function updateStock(UpdateStockRequest $request)
    {
        $articleS = $request->article_S;
        $successfulUpdates = [];
        $failedUpdates = [];

        foreach ($articleS as $item) {
            $article = Article::find($item['article_id']);

            if ($article) {
                // Vérifier que la quantité à ajouter est valide (par exemple, non négative)
                if ($item['qteS'] > 0) {
                    // Ajouter la quantité en stock
                    $article->quantite_stock += $item['qteS'];
                    $article->save();

                    // Ajouter à la liste des succès
                    $successfulUpdates[] = [
                        'article_id' => $article->id,
                        'new_qteStock' => $article->quantite_stock,
                        'qteS' => $item['qteS']
                    ];

                    // Créer une nouvelle entrée dans la table `ArticleS`
                    Article_S::create([
                        'article_id' => $item['article_id'],
                        'qteS' => $item['qteS'],
                    ]);
                } else {
                    // Ajouter à la liste des échecs en cas de quantité invalide
                    $failedUpdates[] = [
                        'article_id' => $item['article_id'],
                        'qteS' => $item['qteS'],
                        'message' => 'Quantité invalide'
                    ];
                }
            } else {
                // Ajouter à la liste des échecs si l'article n'existe pas
                $failedUpdates[] = [
                    'article_id' => $item['article_id'],
                    'qteS' => $item['qteS'],
                    'message' => 'Article introuvable'
                ];
            }
        }

        // Retourner la réponse JSON formatée avec les succès et échecs
        return response()->json([
            'status' => 200,
            'data' => [
                'successful_updates' => $successfulUpdates,
                'failed_updates' => $failedUpdates
            ],
            'message' => 'Quantité de stock mise à jour avec succès.'
        ]);
    }

}