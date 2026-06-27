@if ($errors->any())
    <div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Password
            @if(isset($user))
                <span class="text-gray-400 font-normal">(leave empty to keep current)</span>
            @else
                <span class="text-red-500">*</span>
            @endif
        </label>
        <input type="password" name="password"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            {{ isset($user) ? '' : 'required' }}>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
        <select name="role_id"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
            <option value="">Select a role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>

</div>
