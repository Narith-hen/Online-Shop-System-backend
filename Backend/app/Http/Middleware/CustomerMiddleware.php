<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user has the customer role.
     * Prevents admins from accessing customer-specific API endpoints.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isCustomer()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.',
                ], 403);
            }

            // Web fallback (unlikely to be hit since these are API/Sanctum routes)
            abort(403, 'Customer access required.');
        }

        return $next($request);
    }
}
