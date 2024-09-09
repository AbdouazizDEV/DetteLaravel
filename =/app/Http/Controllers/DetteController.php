<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Services\DetteService;
use App\Models\Dette;

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
}
