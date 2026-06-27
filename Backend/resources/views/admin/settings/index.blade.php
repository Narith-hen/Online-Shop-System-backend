<!-- resources/views/admin/settings/index.blade.php -->

@extends('MainLayout')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_subtitle', 'Manage your admin account')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Avatar Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-camera mr-2 text-blue-600"></i>Profile Picture
        </h2>

        <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
                @if(Auth::user()->avatar)
                    <img id="avatarPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div id="avatarPreview" class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-200">
                        <span class="text-3xl font-bold text-blue-600">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                @endif
            </div>

            <div class="flex-1">
                <form id="avatarForm" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label for="avatar" class="block text-sm font-semibold text-gray-700 mb-1">Upload new picture</label>
                        <input type="file"
                               id="avatar"
                               name="avatar"
                               accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF or WEBP. Max 2MB.</p>
                        <p id="avatar-error" class="text-xs text-red-600 mt-1 hidden"></p>
                    </div>
                    <div id="avatarAlert" class="hidden p-3 rounded-lg border-l-4"></div>
                    <div class="flex gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                            <i class="fas fa-upload mr-1"></i> Upload
                        </button>
                        @if(Auth::user()->avatar)
                            <button type="button"
                                    id="removeAvatarBtn"
                                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium text-sm">
                                <i class="fas fa-trash mr-1"></i> Remove
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Settings Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-cog mr-2 text-blue-600"></i>Admin Settings
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
            <div>
                <p class="text-gray-600 text-sm font-medium mb-1">Full Name</p>
                <p id="displayName" class="text-lg font-semibold text-gray-900">{{ Auth::user()->name ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-medium mb-1">Email Address</p>
                <p id="displayEmail" class="text-lg font-semibold text-gray-900">{{ Auth::user()->email ?? 'Not set' }}</p>
            </div>
        </div>

        <form id="settingsForm" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ Auth::user()->name ?? '' }}"
                       placeholder="Enter your full name"
                       autocomplete="name"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p id="name-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ Auth::user()->email ?? '' }}"
                       placeholder="Enter your email"
                       autocomplete="email"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p id="email-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            <div id="alertMessage" class="hidden p-3 rounded-lg border-l-4"></div>

            <div class="flex gap-3 pt-4">
                <button type="button"
                        onclick="location.reload()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Reset
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Password Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">
            <i class="fas fa-lock mr-2 text-blue-600"></i>Change Password
        </h2>

        <form id="passwordForm" class="space-y-4">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1">Current Password</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       placeholder="Enter your current password"
                       autocomplete="current-password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p id="current_password-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Enter new password"
                       autocomplete="new-password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p id="password-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       placeholder="Confirm new password"
                       autocomplete="new-password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div id="passwordAlertMessage" class="hidden p-3 rounded-lg border-l-4"></div>

            <div class="pt-4">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-save mr-1"></i> Update Password
                </button>
            </div>
        </form>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showToast(msg, type) {
        const existing = document.getElementById('inline-toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.id = 'inline-toast';
    toast.className = 'fixed bottom-4 right-4 z-[9999] px-5 py-3 rounded-lg shadow-lg text-white font-medium transition-all '
        + (type === 'success' ? 'bg-emerald-500' : 'bg-red-500');
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function clearFieldErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    function showFieldError(field, message) {
        const el = document.getElementById(field + '-error');
        if (el) {
            el.classList.remove('hidden');
            el.textContent = Array.isArray(message) ? message[0] : message;
        }
    }

    // =================== Avatar Upload ===================
    document.getElementById('avatarForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearFieldErrors();

        const fileInput = document.getElementById('avatar');
        if (!fileInput.files.length) {
            document.getElementById('avatar-error').classList.remove('hidden');
            document.getElementById('avatar-error').textContent = 'Please select an image.';
            return;
        }

        const formData = new FormData();
        formData.append('avatar', fileInput.files[0]);
        formData.append('_token', csrfToken);

        try {
            const response = await fetch('{{ route("admin.settings.avatar") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                // Update preview
                const preview = document.getElementById('avatarPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = data.avatar_url;
                } else {
                    const img = document.createElement('img');
                    img.id = 'avatarPreview';
                    img.src = data.avatar_url;
                    img.alt = 'Avatar';
                    img.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200';
                    preview.parentNode.replaceChild(img, preview);
                }
                // Also update sidebar avatar in the layout
                fileInput.value = '';
            } else {
                showToast(data.message || 'Upload failed.', 'error');
                if (data.errors) {
                    for (const [field, msg] of Object.entries(data.errors)) {
                        showFieldError(field, msg);
                    }
                }
            }
        } catch (err) {
            showToast('An error occurred. Please try again.', 'error');
        }
    });

    // =================== Remove Avatar ===================
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to remove your profile picture?')) return;

            try {
                const response = await fetch('{{ route("admin.settings.avatar.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    // Replace img with initial placeholder
                    const preview = document.getElementById('avatarPreview');
                    const div = document.createElement('div');
                    div.id = 'avatarPreview';
                    div.className = 'w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-200';
                    div.innerHTML = '<span class="text-3xl font-bold text-blue-600">{{ substr(Auth::user()->name ?? "A", 0, 1) }}</span>';
                    preview.parentNode.replaceChild(div, preview);
                    // Hide remove button
                    removeAvatarBtn.style.display = 'none';
                } else {
                    showToast(data.message || 'Failed to remove avatar.', 'error');
                }
            } catch (err) {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }

    // =================== Settings Update ===================
    document.getElementById('settingsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearFieldErrors();

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();

        try {
            const response = await fetch('{{ route("admin.settings.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ name, email }),
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                // Update the display info
                document.getElementById('displayName').textContent = name;
                document.getElementById('displayEmail').textContent = email;
                // Update stored user data
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                user.name = name;
                user.email = email;
                localStorage.setItem('user', JSON.stringify(user));
            } else {
                showToast(data.message || 'Update failed.', 'error');
                if (data.errors) {
                    for (const [field, msg] of Object.entries(data.errors)) {
                        showFieldError(field, msg);
                    }
                }
            }
        } catch (err) {
            showToast('An error occurred. Please try again.', 'error');
        }
    });

    // =================== Password Update ===================
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearFieldErrors();

        const current_password = document.getElementById('current_password').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;

        try {
            const response = await fetch('{{ route("admin.settings.password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ current_password, password, password_confirmation }),
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById('passwordForm').reset();
            } else {
                showToast(data.message || 'Password update failed.', 'error');
                if (data.errors) {
                    for (const [field, msg] of Object.entries(data.errors)) {
                        showFieldError(field, msg);
                    }
                }
            }
        } catch (err) {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
</script>
@endpush
