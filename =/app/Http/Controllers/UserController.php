<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(){
        return User::all();
        // Retourne tous les utilisateurs
        //
    }
    public function show($id){
    
        
        // Récupère un utilisateur par son identifiant
        return User::find($id);
    
        // Retourne un utilisateur par son identifiant

    }
   
    public function store(Request $request)
    {
        
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'password' => [
                'required',
                'string',
                'min:8',             // Minimum 8 caractères
                'regex:/[a-z]/',      // Doit contenir au moins une minuscule
                'regex:/[A-Z]/',      // Doit contenir au moins une majuscule
                'regex:/[0-9]/',      // Doit contenir au moins un chiffre
                'regex:/[@$!%*#?&]/', // Doit contenir au moins un caractère spécial
                'confirmed'           // Confirmer le mot de passe
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'boutiquier']),
            ],
        ], [
            // Messages d'erreur personnalisés en français
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être soit admin, soit boutiquier.',
        ]);

        // Vérification des erreurs de validation
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Création du nouvel utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role' => $request->role,

        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user' => $user
        ], 201);
    }
    public function update(Request $request, $id)
    {
        // Validation des champs mis à jour
        $validator = Validator::make($request->all(), [
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255|unique:users,login,' . $id,
        ], [
            'login.unique' => 'Ce login est déjà utilisé.',
        ]);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

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
