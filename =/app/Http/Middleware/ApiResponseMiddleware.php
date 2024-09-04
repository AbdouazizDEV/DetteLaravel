<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->isSuccessful()) {
            $data = $response->getData(true);
            return response()->json([
                'status' => $response->getStatusCode(),
                'data' => $data,
                'message' => 'Success'
            ]);
        }

        return $response;
    }
}
