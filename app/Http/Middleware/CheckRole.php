<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || !$request->user()->getRoleNames()->contains($role)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return $next($request);
    }
} 