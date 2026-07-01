<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('role', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $perPage = isset($_COOKIE['per_page']) ? min(25, max(5, (int) $_COOKIE['per_page'])) : 10;
        $users = $query->orderBy('code')->paginate($perPage)->onEachSide(1)->appends($request->except('per_page'));
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();

        if (request()->ajax() || request()->wantsJson()) {
            return view('admin.users.partials.form', ['user' => null, 'roles' => $roles]);
        }

        return view('admin.users.create', compact('roles'));
    }

    public function show(User $user)
    {
        $user->load(['role', 'orders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User created successfully.']);
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'user' => [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'role_id' => $user->role_id,
                ],
                'roles' => $roles,
            ]);
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'role_id'  => 'required|exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $validated['password'] = $request->password;
        }

        $user->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully.']);
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete admin users.'], 403);
            }
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete admin users.');
        }

        $user->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleBlock(User $user)
    {
        if ($user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot block admin users.'], 403);
        }

        $user->update(['is_blocked' => !$user->is_blocked]);

        $status = $user->is_blocked ? 'blocked' : 'unblocked';

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User {$status} successfully.",
                'is_blocked' => $user->is_blocked,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$status} successfully.");
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }
        $users = User::whereIn('id', $ids)->get();
        foreach ($users as $user) {
            if ($user->isAdmin()) {
                continue;
            }
            $user->delete();
        }
        return response()->json(['success' => true, 'message' => 'Users deleted (admins skipped).']);
    }
}
