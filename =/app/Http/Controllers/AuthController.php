<?php

namespace App\Http\Controllers;

use App\Services\Contracts\AuthentificationServiceInterface;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        return $this->authService->authenticate($credentials);
    }

    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
    //fair la méthode showLoginForm
    
 
    // Méthode pour gérer l'enregistrement
    public function register(Request $request){
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

        $token = $user->createToken('MyAppToken')->accessToken;

        return response()->json([
            'token' => $token,
            'data' => $user,
            'message' => 'Utilisateur enregistré avec succès.'
        ], 201);
    }
}
