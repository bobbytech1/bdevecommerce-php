<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user || $user->role !== $role) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
