<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check()) {
            abort(403);
        }

        $role = auth()->user()->normalizedRole();
        $allowedRoles = array_map(fn ($role) => match ($role) {
            'atasan', 'Kepala Stasiun', 'kepsta' => 'kepala_stasiun',
            default => $role,
        }, $roles);

        if (! in_array($role, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
