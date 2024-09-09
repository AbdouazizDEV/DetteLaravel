<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        //dd($user);
        //dd($request->user());
        if (!$request->user()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        

        foreach ($roles as $role) {
            //dd($request->user()->$role);
            if ($request->user()) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Non autorisé'], 403);
    }
}
