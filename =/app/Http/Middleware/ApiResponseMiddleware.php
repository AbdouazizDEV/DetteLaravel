<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            return response()->json([
                'status' => $response->getStatusCode(),
                'data' => $data,
                'message' => 'Success'
            ], $response->getStatusCode());
        }

        return $response;
    }
}
