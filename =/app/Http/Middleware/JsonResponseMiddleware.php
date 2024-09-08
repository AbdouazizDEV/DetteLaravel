<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }

        return response()->json([
            'status' => $response->getStatusCode(),
            'data' => $response->getOriginalContent(),
            //'message' => $response->getMessage()
        ]);
    }
}
