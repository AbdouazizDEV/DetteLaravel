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

     public function show($id){
    
        
        // Récupère un utilisateur par son identifiant
        return User::find($id);
    
        // Retourne un utilisateur par son identifiant

    }
     public function store(StoreUserRequest $request)
    {
        // Les données sont déjà validées à ce stade
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => $request->active === 1
        ]);
    
        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'data' => $user
        ], 201);
    }
    
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
    
     public function destroy($id)
    {
        // Rechercher l'utilisateur dans la base de données
        $user = User::findOrFail($id);

        // Supprimer l'utilisateur
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.'], 200);
    }

}
