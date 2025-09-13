<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: ->middleware('role:admin') or ->middleware('role:admin,dispatcher')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // If not authenticated, return 401 (middleware typically sits on auth:sanctum group)
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // If no roles specified, allow (or you may choose to deny)
        if (empty($roles)) {
            return $next($request);
        }

        // Normalize roles to lower-case strings
        $allowed = array_map('strtolower', $roles);
        $userRole = strtolower((string) ($user->role ?? ''));

        if (! in_array($userRole, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Insufficient role permissions.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
