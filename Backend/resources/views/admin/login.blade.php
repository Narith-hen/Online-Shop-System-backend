<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Online Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-12 h-12 object-contain rounded-xl">
                    <div class="text-left">
                        <h1 class="text-xl font-bold text-gray-900">Online Shop</h1>
                        <p class="text-xs text-gray-500">Admin Panel</p>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Sign In</h2>
                <p class="text-sm text-gray-500 mt-1">Enter your credentials to access the admin panel</p>
            </div>

            <!-- Error Message -->
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="admin@example.com"
                               autocomplete="email"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required autofocus>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-500 mt-6">&copy; {{ date('Y') }} Online Shop. All rights reserved.</p>
    </div>
</body>
</html>
