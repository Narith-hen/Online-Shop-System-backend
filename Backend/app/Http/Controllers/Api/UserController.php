<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->latest()->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data'    => $users
        ], Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $user = User::with('role')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $user
        ], Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $id,
            'role_id'  => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user->update($validator->validated());

        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data'    => $user
        ], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ], Response::HTTP_OK);
    }
}
