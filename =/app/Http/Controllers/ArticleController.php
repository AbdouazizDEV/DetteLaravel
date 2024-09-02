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
class ArticleController extends Controller
{
    use ApiResponse;
    /**
     * @OA\Get(
     *     path="/v1/articles",
     *     summary="Récupérer tous les articles",
     *     description="Permet de récupérer la liste de tous les articles",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des articles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="libelle", type="string", example="Article 1"),
     *                 @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *                 @OA\Property(property="stock", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     )
     * )
     */
    // GET /api/v1/articles : Récupérer tous les articles
    public function index(Request $request)
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
    }


    /**
     * @OA\Get(
     *     path="/v1/articles/{id}",
     *     summary="Récupérer un article spécifique",
     *     description="Permet de récupérer un article par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="libelle", type="string", example="Article 1"),
     *             @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/v1/articles",
     *     summary="Ajouter un nouvel article",
     *     description="Permet d'ajouter un nouvel article",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="libelle", type="string", example="Article 1"),
     *             @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article créé",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="libelle", type="string", example="Article 1"),
     *             @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Échec de la création de l'article"
     *     )
     * )
     */
    // POST /api/v1/articles : Ajouter un nouvel article ou mettre à jour la quantité en stock
    public function store(StoreArticleRequest $request)
    {
        
        // Vérifier si un article avec le même libelle existe déjà
        $existingArticle = Article::where('libelle', $request->libelle)->first();

        if ($existingArticle) {
            // Si l'article existe, mettre à jour la quantité en stock
            $existingArticle->qteStock += $request->qteStock;
            $existingArticle->save();

            return $this->successResponse($existingArticle, 'Article existant mis à jour avec succès. Quantité en stock augmentée.');
        } else {
            // Si l'article n'existe pas, créer un nouvel article
            $article = Article::create($request->validated());

            return $this->successResponse($article, 'Article ajouté avec succès.', 201);
        }
    }
    /**
     * @OA\Post(
     *     path="/v1/articles/libelle",
     *     summary="Récupérer un article par son libelle",
     *     description="Permet de récupérer un article par son libelle",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="libelle", type="string", example="Article 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="libelle", type="string", example="Article 1"),
     *             @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */
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
    /**
     * @OA\Put(
     *     path="/v1/articles/{id}",
     *     summary="Mettre à jour un article spécifique",
     *     description="Permet de mettre à jour un article par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="libelle", type="string", example="Article 1"),
     *             @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="libelle", type="string", example="Article 1"),
     *             @OA\Property(property="description", type="string", example="Description de l'article 1"),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */
    // PUT|PATCH /api/v1/articles/{id} : Mettre à jour un article spécifique
    public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);

        $article->update($request->validated());

        return $this->successResponse($article, 'Article mis à jour avec succès.');
    }
    /**
     * @OA\Delete(
     *     path="/v1/articles/{id}",
     *     summary="Supprimer un article spécifique",
     *     description="Permet de supprimer un article par son ID (soft delete)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article supprimé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */

    // DELETE /api/v1/articles/{id} : Supprimer un article (Soft Delete)
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return $this->successResponse(null, 'Article supprimé avec succès.');
    }
    /**
     * @OA\Post(
     *     path="/v1/articles/updateStock",
     *     summary="Mettre à jour le stock des articles",
     *     description="Permet de mettre à jour le stock des articles",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="articles", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="stock", type="integer", example=100)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock des articles mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Échec de la mise à jour du stock"
     *     )
     * )
     */
    public function updateStock(Request $request)
    {
        $articleS = $request->article_S;
        $successfulUpdates = [];
        $failedUpdates = [];
    
        foreach ($articleS as $item) {
            $article = Article::find($item['article_id']);
    
            if ($article) {
                // Vérifier que la quantité à soustraire est valide (par exemple, non négative)
                if ($item['qteS'] > 0) {
                    // Vérifier si la quantité en stock est suffisante
                    if ($article->qteStock >= $item['qteS']) {
                        $article->qteStock -= $item['qteS'];
                        $article->save();
    
                        // Ajouter à la liste des succès
                        $successfulUpdates[] = [
                            'article_id' => $article->id,
                            'new_qteStock' => $article->qteStock,
                            'qteS' => $item['qteS']
                        ];
    
                        // Créer une nouvelle entrée dans la table `ArticleS`
                        Article_S::create([
                            'article_id' => $item['article_id'],
                            'qteS' => $item['qteS'],
                        ]);
                    } else {
                        // Ajouter à la liste des échecs en cas de quantité insuffisante
                        $failedUpdates[] = [
                            'article_id' => $item['article_id'],
                            'qteS' => $item['qteS'],
                            'message' => 'Quantité insuffisante en stock'
                        ];
                    }
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
