<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Attributes as OA;

class SocialAuthController extends Controller
{
    /**
     * Supported providers.
     */
    private const SUPPORTED_PROVIDERS = ['google', 'github'];

    #[OA\Get(
        path: '/api/auth/{provider}/redirect',
        summary: 'Redirect to OAuth provider for authentication',
        tags: ['Social Auth'],
        parameters: [
            new OA\Parameter(name: 'provider', in: 'path', required: true, schema: new OA\Schema(type: 'string', enum: ['google', 'github'])),
        ],
        responses: [
            new OA\Response(response: 302, description: 'Redirect to provider'),
            new OA\Response(response: 400, description: 'Unsupported provider'),
        ]
    )]
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, self::SUPPORTED_PROVIDERS)) {
            return response()->json([
                'success' => false,
                'message' => "Provider '{$provider}' is not supported.",
            ], 400);
        }

        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    #[OA\Get(
        path: '/api/auth/{provider}/callback',
        summary: 'Handle OAuth provider callback',
        tags: ['Social Auth'],
        parameters: [
            new OA\Parameter(name: 'provider', in: 'path', required: true, schema: new OA\Schema(type: 'string', enum: ['google', 'github'])),
        ],
        responses: [
            new OA\Response(response: 302, description: 'Redirect to frontend with token'),
            new OA\Response(response: 400, description: 'Unsupported provider'),
        ]
    )]
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, self::SUPPORTED_PROVIDERS)) {
            return response()->json([
                'success' => false,
                'message' => "Provider '{$provider}' is not supported.",
            ], 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (Exception $e) {
            Log::error('Social login callback error: ' . $e->getMessage());

            $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
            return redirect("{$frontendUrl}/login?error=Unable to authenticate with {$provider}. Please try again.");
        }

        // Check if a user already exists with this provider + provider_id
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        // Also check by email to link accounts
        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link the social provider to the existing user
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_avatar' => $socialUser->getAvatar(),
                ]);
            }
        }

        // Create a new user if one doesn't exist
        if (!$user) {
            $customerRole = Role::where('name', 'customer')->first();

            $name = $socialUser->getName() ?? $socialUser->getNickname() ?? $socialUser->getEmail();

            $user = User::create([
                'name' => $name,
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(bin2hex(random_bytes(16))), // Random password (user logs in via social only)
                'role_id' => $customerRole?->id,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_avatar' => $socialUser->getAvatar(),
                'avatar' => $socialUser->getAvatar(), // Also store as the local avatar
            ]);
        }

        // Block blocked users from logging in via social auth
        if ($user->is_blocked) {
            $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
            return redirect("{$frontendUrl}/account-blocked");
        }

        // Revoke old tokens and create a new one
        $user->tokens()->delete();
        $token = $user->createToken('API Token')->plainTextToken;

        $user->load('role');

        // Redirect back to the frontend with the token and user info
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));

        $userData = json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->name,
            'avatar' => $user->avatar,
        ]);

        return redirect("{$frontendUrl}/auth/callback?token={$token}&user=" . urlencode($userData));
    }
}
