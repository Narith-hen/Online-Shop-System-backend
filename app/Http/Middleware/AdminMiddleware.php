<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user has the admin role.
     * Works for both session-based (web) and token-based (Sanctum/API) auth.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            return redirect()->route('admin.login')->withErrors([
                'email' => 'You do not have admin access.',
            ]);
        }

        return $next($request);
    }
}
