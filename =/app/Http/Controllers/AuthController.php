<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Client;
//Hash
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // Méthode pour gérer le login
    public function login(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'data' => $validator->errors(), 'message' => 'Validation Error'], 422);
        }

        // Authentification
        if (Auth::attempt(['login' => $request->login, 'password' => $request->password])) {
            $user = User::find(Auth::user()->id);
            $token = $user->createToken('MyAppToken')->plainTextToken; 
            return response()->json(['status' => 200, 'data' => ['token' => $token], 'message' => 'Login Successful']);
        } else {
            return response()->json(['status' => 401, 'data' => null, 'message' => 'Invalid Credentials'], 401);
        }
    }

    // Méthode pour gérer l'enregistrement
    public function register(Request $request)
    {
        $request->validate([
            'login' => 'required|unique:users',
            'password' => 'required|min:5|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'nom' => 'required|string|max:255',
            'clientid' => 'required|exists:clients,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role' => 'boutiquier',
            'nom' => $request->nom,  // nom ajouté ici
            'clientid' => $request->clientid,
            'photo' => $request->photo ? $request->photo->store('photos') : null,
        ]);

        $token = $user->createToken('MyAppToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'data' => $user,
            'message' => 'Utilisateur enregistré avec succès.'
        ], 201);
    }
}
