@extends('MainLayout')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_subtitle', 'Manage your admin account')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Avatar Card -->
    <div class="card p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-camera text-blue-500"></i> Profile Picture
        </h2>
        <div class="flex items-start gap-6">
            <div class="shrink-0">
                @if(Auth::user()->avatar)
                    <img id="avatarPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div id="avatarPreview" class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center border-2 border-gray-200 shadow-sm">
                        <span class="text-3xl font-bold text-white">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <form id="avatarForm" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label for="avatar" class="block text-sm font-semibold text-gray-700 mb-1">Upload new picture</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF or WEBP. Max 2MB.</p>
                        <p id="avatar-error" class="text-xs text-red-600 mt-1 hidden"></p>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                        @if(Auth::user()->avatar)
                            <button type="button" id="removeAvatarBtn" class="inline-flex items-center gap-2 px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 font-medium text-sm transition">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Settings Card -->
    <div class="card p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-cog text-blue-500"></i> Admin Settings
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-xl">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide mb-1">Full Name</p>
                <p id="displayName" class="text-lg font-semibold text-gray-900">{{ Auth::user()->name ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide mb-1">Email Address</p>
                <p id="displayEmail" class="text-lg font-semibold text-gray-900">{{ Auth::user()->email ?? 'Not set' }}</p>
            </div>
        </div>

        <form id="settingsForm" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" value="{{ Auth::user()->name ?? '' }}" placeholder="Enter your full name" autocomplete="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                <p id="name-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ Auth::user()->email ?? '' }}" placeholder="Enter your email" autocomplete="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                <p id="email-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="location.reload()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">Reset</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Password Card -->
    <div class="card p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-lock text-blue-500"></i> Change Password
        </h2>
        <form id="passwordForm" class="space-y-4">
            @csrf
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter current password" autocomplete="current-password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                <p id="current_password-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter new password" autocomplete="new-password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                <p id="password-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" autocomplete="new-password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
            </div>
            <div class="pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showFieldError(field, message) {
        var el = document.getElementById(field + '-error');
        if (el) { el.classList.remove('hidden'); el.textContent = Array.isArray(message) ? message[0] : message; }
    }
    function clearFieldErrors() { document.querySelectorAll('[id$="-error"]').forEach(function(el) { el.classList.add('hidden'); el.textContent = ''; }); }

    // Avatar
    document.getElementById('avatarForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearFieldErrors();
        var fi = document.getElementById('avatar');
        if (!fi.files.length) {
            document.getElementById('avatar-error').classList.remove('hidden');
            document.getElementById('avatar-error').textContent = 'Please select an image.';
            return;
        }
        var fd = new FormData(); fd.append('avatar', fi.files[0]); fd.append('_token', csrfToken);
        try {
            var res = await fetch('{{ route("admin.settings.avatar") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            var d = await res.json();
            if (d.success) {
                showToast(d.message, 'success');
                var preview = document.getElementById('avatarPreview');
                if (preview.tagName === 'IMG') { preview.src = d.avatar_url; }
                else {
                    var img = document.createElement('img');
                    img.id = 'avatarPreview'; img.src = d.avatar_url; img.alt = 'Avatar';
                    img.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200';
                    preview.parentNode.replaceChild(img, preview);
                }
                fi.value = '';
            } else {
                showToast(d.message || 'Upload failed.', 'error');
                if (d.errors) { for (var k in d.errors) showFieldError(k, d.errors[k]); }
            }
        } catch (err) { showToast('An error occurred.', 'error'); }
    });

    // Remove Avatar
    var ra = document.getElementById('removeAvatarBtn');
    if (ra) {
        ra.addEventListener('click', async function() {
            if (!confirm('Remove your profile picture?')) return;
            try {
                var res = await fetch('{{ route("admin.settings.avatar.remove") }}', { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' } });
                var d = await res.json();
                if (d.success) {
                    showToast(d.message, 'success');
                    var preview = document.getElementById('avatarPreview');
                    var div = document.createElement('div');
                    div.id = 'avatarPreview';
                    div.className = 'w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center border-2 border-gray-200 shadow-sm';
                    div.innerHTML = '<span class="text-3xl font-bold text-white">{{ substr(Auth::user()->name ?? "A", 0, 1) }}</span>';
                    preview.parentNode.replaceChild(div, preview);
                    ra.style.display = 'none';
                } else { showToast(d.message || 'Failed.', 'error'); }
            } catch (err) { showToast('An error occurred.', 'error'); }
        });
    }

    // Settings
    document.getElementById('settingsForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearFieldErrors();
        var name = document.getElementById('name').value.trim();
        var email = document.getElementById('email').value.trim();
        try {
            var res = await fetch('{{ route("admin.settings.update") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ name: name, email: email }) });
            var d = await res.json();
            if (d.success) {
                showToast(d.message, 'success');
                document.getElementById('displayName').textContent = name;
                document.getElementById('displayEmail').textContent = email;
                var user = JSON.parse(localStorage.getItem('user') || '{}');
                user.name = name; user.email = email; localStorage.setItem('user', JSON.stringify(user));
            } else {
                showToast(d.message || 'Update failed.', 'error');
                if (d.errors) { for (var k in d.errors) showFieldError(k, d.errors[k]); }
            }
        } catch (err) { showToast('An error occurred.', 'error'); }
    });

    // Password
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearFieldErrors();
        try {
            var res = await fetch('{{ route("admin.settings.password") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ current_password: document.getElementById('current_password').value, password: document.getElementById('password').value, password_confirmation: document.getElementById('password_confirmation').value }) });
            var d = await res.json();
            if (d.success) { showToast(d.message, 'success'); document.getElementById('passwordForm').reset(); }
            else {
                showToast(d.message || 'Password update failed.', 'error');
                if (d.errors) { for (var k in d.errors) showFieldError(k, d.errors[k]); }
            }
        } catch (err) { showToast('An error occurred.', 'error'); }
    });
</script>
@endpush
