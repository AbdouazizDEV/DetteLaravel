<?php
// app/Http/Controllers/DetteController.php
// app/Http/Controllers/DetteController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Http\Requests\PaiementRequest;
use App\Services\DetteService;
use App\Models\Dette;
use Illuminate\Http\Request;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
        $this->middleware('api.response');
    }

    public function store(StoreDetteRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $response = $this->detteService->createDette($validatedData);
            return response($response, $response['status']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $dettes = Dette::with('client')->get();

        if ($dettes->isEmpty()) {
            return response(['message' => 'Pas de dettes trouvées'], 200);
        }

        return response($dettes, 200);
    }

    /**
     * Effectue le paiement d'une dette
     */
    public function paiement(PaiementRequest $request)
    {
        $validated = $request->validated();

        // Appel au service pour traiter le paiement
        $result = $this->detteService->effectuerPaiement($validated['dette_id'], $validated['montant']);

        if ($result === true) {
            $dette = Dette::find($validated['dette_id']);
            return response()->json([
                'status' => 200,
                'data' => [
                    'message' => 'Paiement effectué avec succès',
                    'montant' => $dette->montant,
                    'montant_restant' => $dette->montant_restant,
                    'montantDU' => $dette->montantDU,
                    'date' => $dette->date
                ],
                'message' => 'Success'
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'data' => $result,
                'message' => 'Success'
            ]);
        }
    }
}
