<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']); // Login
    Route::post('logout', [AuthController::class, 'logout']); // Logout
    // Route register
    Route::post('register', [AuthController::class,'register']); // Register
});

Route::middleware(['auth:api','role:admin'])->prefix('v1')->group(function () {
    Route::get('users', [UserController::class, 'index']);// récupération de tous les users
    Route::get('users/{id}', [UserController::class, 'show']);// récupération d'un user
    Route::post('users', [UserController::class, 'store']);// Ajouter une user
    Route::put('users/{id}', [UserController::class, 'update']);// Mettre à jour un user
    Route::patch('users/{id}', [UserController::class, 'update']);// Mettre à jour un user
    Route::delete('users/{id}', [UserController::class, 'destroy']);// Supprimer un user
});

Route::middleware(['auth:api','role:boutiquier'])->prefix('v1')->group(function () {
    Route::get('clients', [ClientController::class, 'index']);  // Récupérer tous les clients avec filtrage, tri et pagination
    Route::get('clients/{id}', [ClientController::class, 'show']);  // Récupérer un client spécifique
    Route::post('clients/{id}/dettes', [ClientController::class, 'listDettes']);  // Récupérer les dettes d'un client
    Route::post('clients/{id}/user', [ClientController::class, 'showWithUser']);  // Récupérer un client avec son user
    Route::get('clients/telephone', [ClientController::class,'searchByTelephone']);  // Rechercher un client par numéro de téléphone (partial search)
    Route::post('clients', [ClientController::class, 'store']);  // Ajouter un nouveau client
    Route::put('clients/{id}', [ClientController::class, 'update']);  // Mettre à jour un client spécifique
    Route::patch('clients/{id}', [ClientController::class, 'update']);  // Mettre à jour partiellement un client spécifique
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);  // Supprimer un client spécifique
});
//les routes pour Article

Route::middleware(['auth:api','role:boutiquier'])->prefix('v1')->group(function () {
    Route::get('articles', [ArticleController::class, 'index']);  // Récupérer tous les articles
    Route::get('articles/{id}', [ArticleController::class, 'show']);  // Récupérer un article spécifique
    Route::post('articles/libelle', [ArticleController::class, 'findByLibelle']);  // Récupérer un article par son libelle
    Route::post('articles', [ArticleController::class, 'store']);  // Ajouter un nouvel article
    Route::put('articles/{id}', [ArticleController::class, 'update']);  // Mettre à jour un article spécifique
    Route::patch('articles/{id}', [ArticleController::class, 'update']);  // Mettre à jour partiellement un article spécifique
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);  // Supprimer un article (soft delete)
    Route::post('articles/updateStock', [ArticleController::class, 'updateStock']);  // Mettre à jour le stock des articles
});
