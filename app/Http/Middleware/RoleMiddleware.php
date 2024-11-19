<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = auth()->user();

        // Check if the user is authenticated and has the correct role
        if (!$user || $user->role !== $role) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
