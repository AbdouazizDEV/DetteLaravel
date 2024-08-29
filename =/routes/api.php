<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
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
    Route::get('users', [UserController::class, 'index']);// récupération de tous les users
    Route::get('users/{id}', [UserController::class, 'show']);// récupération d'un user
    Route::post('users', [UserController::class, 'store']);// Ajouter une user
    Route::put('users/{id}', [UserController::class, 'update']);// Mettre à jour un user
    Route::patch('users/{id}', [UserController::class, 'update']);// Mettre à jour un user
    Route::delete('users/{id}', [UserController::class, 'destroy']);// Supprimer un user
});

Route::prefix('v1')->group(function () {
    Route::get('clients', [ClientController::class, 'index']);  // Récupérer tous les clients avec filtrage, tri et pagination
    Route::get('clients/{id}', [ClientController::class, 'show']);  // Récupérer un client spécifique
    Route::post('clients', [ClientController::class, 'store']);  // Ajouter un nouveau client
    Route::put('clients/{id}', [ClientController::class, 'update']);  // Mettre à jour un client spécifique
    Route::patch('clients/{id}', [ClientController::class, 'update']);  // Mettre à jour partiellement un client spécifique
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);  // Supprimer un client spécifique
});