<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/users",
     *     summary="Récupérer tous les utilisateurs",
     *     description="Permet de récupérer la liste de tous les utilisateurs",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Utilisation de QueryBuilder pour gérer les filtres et la pagination
        $users = \App\Models\User::query();

        if ($request->has('role')) {
            $users->where('role', $request->role);
        }

        if ($request->has('active')) {
            $activeStatus = $request->active === 'oui' ? 1 : 0;
            $users->where('active', $activeStatus);
        }

        $result = $users->paginate(10);

        return response()->json([
            'message' => $result->isEmpty() ? 'Aucun utilisateur trouvé.' : 'Liste des utilisateurs.',
            'data' => $result->isEmpty() ? null : $result
        ]);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{id}",
     *     summary="Récupérer un utilisateur",
     *     description="Permet de récupérer un utilisateur par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function show($id){
    
        
        // Récupère un utilisateur par son identifiant
        return User::find($id);
    
        // Retourne un utilisateur par son identifiant

    }
   /**
     * @OA\Post(
     *     path="/v1/users",
     *     summary="Ajouter un utilisateur",
     *     description="Permet d'ajouter un nouvel utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Échec de la création de l'utilisateur"
     *     )
     * )
     */
    public function store(StoreUserRequest $request)
    {
        // Les données sont déjà validées à ce stade
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => $request->active
        ]);
    
        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'data' => $user
        ], 201);
    }
    
    /**
     * @OA\Put(
     *     path="/v1/users/{id}",
     *     summary="Mettre à jour un utilisateur",
     *     description="Permet de mettre à jour un utilisateur par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        // Rechercher l'utilisateur dans la base de données
        $user = User::findOrFail($id);
    
        // Mettre à jour les champs non-nuls
        $user->fill($request->only(['nom', 'prenom', 'login']));
    
        // Sauvegarder les modifications
        $user->save();
    
        return response()->json(['message' => 'Utilisateur mis à jour avec succès.', 'user' => $user], 200);
    }
    
    /**
     * @OA\Delete(
     *     path="/v1/users/{id}",
     *     summary="Supprimer un utilisateur",
     *     description="Permet de supprimer un utilisateur par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function destroy($id)
    {
        // Rechercher l'utilisateur dans la base de données
        $user = User::findOrFail($id);

        // Supprimer l'utilisateur
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.'], 200);
    }


}
