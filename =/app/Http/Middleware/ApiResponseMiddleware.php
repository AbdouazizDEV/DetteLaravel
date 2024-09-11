<?php
// app/Http/Middleware/ApiResponseMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse && !isset($response->getData(true)['formatted'])) {
            $data = $response->getData(true);
            return response()->json([
                'status' => $response->getStatusCode(),
                'data' => $data,
                'message' => 'Success',
                'formatted' => true // Ajouter un indicateur pour éviter la répétition
            ], $response->getStatusCode());
        }

        return $response;
    }
}
