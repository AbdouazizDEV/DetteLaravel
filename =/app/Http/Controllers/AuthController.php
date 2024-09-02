<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Passport\HasApiTokens;
use App\Models\Client;
//Hash
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use HasApiTokens;
    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="Login de l'utilisateur",
     *     description="Permet à un utilisateur de se connecter",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="login", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_access_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec de la connexion"
     *     )
     * )
     */

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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $tokenResult = $user->createToken('MyAppToken');
            $token = $tokenResult->token;

            // Ajoutez d'autres claims ou scopes si nécessaire ici

            $token->save();

            return response()->json([
                'status' => 200,
                'data' => [
                    'token' => $tokenResult->accessToken,
                    'user' => $user->withAccessToken($token),
                ],
                'message' => 'Login Successful'
            ]);
        } else {
            return response()->json(['status' => 401, 'data' => null, 'message' => 'Invalid Credentials'], 401);
        }
    }

     /**
     * @OA\Post(
     *     path="/v1/logout",
     *     summary="Déconnexion de l'utilisateur",
     *     description="Permet à un utilisateur de se déconnecter",
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec de la déconnexion"
     *     )
     * )
     */

    // Methode pour gérer la deconnexion
    public function logout()
    {
        //auth()->user()->token()->revoke();
        return response()->json(['message' => 'Deconnexion spécie.'], 200);
    }
         /**
         * @OA\Post(
         *     path="/v1/register",
         *     summary="Inscription de l'utilisateur",
         *     description="Permet à un utilisateur de s'inscrire",
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
         *         description="Inscription réussie",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="User registered successfully")
         *         )
         *     ),
         *     @OA\Response(
         *         response=400,
         *         description="Échec de l'inscription"
         *     )
         * )
         */

        
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
