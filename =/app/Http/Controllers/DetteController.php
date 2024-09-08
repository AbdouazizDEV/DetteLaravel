<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Services\DetteService;
use Illuminate\Http\JsonResponse;
use App\Models\Dette;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function store(StoreDetteRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $response = $this->detteService->createDette($validatedData);

            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    // Méthode pour lister les dettes avec ou sans filtre de statut
    public function index(): JsonResponse
    {
        $dettes = Dette::with('client')  // Charger les clients liés aux dettes
            ->get();

        if ($dettes->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas de dettes trouvées',
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $dettes,
            'message' => 'Liste des dettes',
        ], 200);
    }
}
