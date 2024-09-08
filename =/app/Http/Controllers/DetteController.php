<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Services\DetteService;
use Illuminate\Http\JsonResponse;

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
            $dette = $this->detteService->createDette($validatedData);

            return response()->json([
                'status' => 201,
                'message' => 'Dette enregistrée avec succès',
                'data' => $dette
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
