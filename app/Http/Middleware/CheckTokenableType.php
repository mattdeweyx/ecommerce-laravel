<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenableType
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        $tokenableType = get_class($user);

        // Check if the tokenable type matches the expected role
        if ($tokenableType === $role) {
            // Tokenable type is valid, allow the request to proceed
            return $next($request);
        }

        // Tokenable type is invalid, return unauthorized response
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
