<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Apply the CheckTokenableType middleware to ensure valid tokenable type
        $checkTokenable = app()->make(CheckTokenableType::class);
        $checkTokenableResponse = $checkTokenable->handle($request, function ($request) use ($role, $next) {
            $user = Auth::user();

            if ($user && $user->role === $role) {
                return $next($request);
            }

            return response()->json(['message' => 'Not authorized!'], 403);
        });

        return $checkTokenableResponse;
    }
}
