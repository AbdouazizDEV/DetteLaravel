<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!$request->user() || !$request->user()->role) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        foreach ($roles as $role) {
            if ($request->user()->can($role)) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Non autorisé'], 403);
    }
}
