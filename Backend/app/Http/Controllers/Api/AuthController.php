<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')),
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 201, description: 'User registered successfully', content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customerRole = Role::where('name', 'customer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $customerRole?->id,
        ]);

        $user->load('role');
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Register Successfully',
            'token' => $token,
            'role' => $user->role?->name,
            'user' => $user,
        ], 201);
    }
    #[OA\Post(
        path: '/api/login',
        summary: 'Login with email and password',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')),
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Login successful', content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse')),
            new OA\Response(response: 401, description: 'Invalid credentials', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Account blocked', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($user->is_blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked. Please contact support.',
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Successfully',
            'token' => $token,
            'role' => $user->role?->name,
            'user' => $user,
        ]);
    }

    #[OA\Get(
        path: '/api/profile',
        summary: 'Get current authenticated user profile',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Profile retrieved', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
            ])),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile Retrieved Successfully',
            'user' => $request->user(),
        ]);
    }

    #[OA\Post(
        path: '/api/profile',
        summary: 'Update current user profile',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/UpdateProfileRequest')),
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Profile updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Profile Updated Successfully'),
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
            ])),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile Updated Successfully',
            'user' => $user,
        ]);
    }

    #[OA\Post(
        path: '/api/change-password',
        summary: 'Change current user password',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ChangePasswordRequest')),
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Password changed', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 403, description: 'Current password incorrect', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 403);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout and revoke current token',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Logged out', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Successfully',
        ]);
    }
}
