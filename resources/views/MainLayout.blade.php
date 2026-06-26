<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">

    <title>@yield('title', 'Admin Dashboard') - Online Shop Admin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> --}}

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }

        .sidebar {
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .nav-link {
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: #1f2937;
            padding-left: 1.5rem;
        }

        .nav-link.active {
            background-color: #3b82f6;
            border-left: 4px solid #1e40af;
        }

        .dropdown-menu {
            display: none;
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            display: block;
        }

        .card {
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-100">

        <!-- Sidebar -->
        <aside
            class="w-64 bg-gray-900 text-gray-100 sidebar fixed md:relative h-full z-40 translate-x-full md:translate-x-0 transition-transform duration-300">
            <!-- Logo Section -->
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <!-- Logo Image -->
                    <img src="{{ asset('images/logo.png') }}" alt="Online Shop Logo"
                        class="w-10 h-10 object-contain rounded-3xl">
                    <div>
                        <h1 class="text-xl font-bold text-white">Online Shop</h1>
                        <p class="text-xs text-gray-400">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="mt-8 px-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white @if (request()->routeIs('admin.dashboard')) active @endif">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Categories -->
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white @if (request()->routeIs('admin.categories*')) active @endif">
                    <i class="fas fa-tag w-5"></i>
                    <span>Categories</span>
                </a>

                <!-- Products -->
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white @if (request()->routeIs('admin.products*')) active @endif">
                    <i class="fas fa-box w-5"></i>
                    <span>Products</span>
                </a>

                <!-- Orders -->
                <a href="{{ route('admin.orders.index') }}"
                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white @if (request()->routeIs('admin.orders*')) active @endif">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span>Orders</span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white @if (request()->routeIs('admin.users*')) active @endif">
                    <i class="fas fa-users w-5"></i>
                    <span>Users</span>
                </a>

                <!-- Divider -->
                <div class="border-t border-gray-700 my-4"></div>

                <!-- Settings -->
                <a href="{{ route('admin.settings.index') }}"
                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white @if (request()->routeIs('admin.settings')) active @endif">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4 flex justify-between items-center">
                    <!-- Mobile Menu Toggle -->
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Page Title -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-500 mt-1">@yield('page_subtitle', 'Welcome to your dashboard')</p>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="relative text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                                @if(optional(Auth::user())->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="User"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr(optional(Auth::user())->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="text-sm font-medium">{{ optional(Auth::user())->name ?? 'Admin' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-b-lg">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="p-6">

                    <!-- Success Popup Banner -->
                    @if ($message = Session::get('success'))
                        <div id="success-banner"
        class="fixed bottom-4 right-4 max-w-md bg-white rounded-lg p-4 shadow-2xl z-50 flex items-start gap-3">

                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            </div>

                            <div class="flex-1">
                                <p class="text-emerald-500 font-base">{{ $message }}</p>
                                <button onclick="dismissBanner()"
                                    class="mt-3 text-sm text-emerald-500 hover:text-white transition">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Error Popup Banner -->
                    @if ($message = Session::get('error'))
                        <div id="error-banner"
                            class="fixed bottom-6 right-6 max-w-md bg-red-800 border border-red-600 rounded-2xl p-5 shadow-2xl z-50 flex items-start gap-3">

                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation-circle text-white"></i>
                                </div>
                            </div>

                            <div class="flex-1">
                                <p class="text-red-100 font-medium">{{ $message }}</p>
                                <button onclick="dismissBanner()"
                                    class="mt-3 text-sm text-red-300 hover:text-white transition">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Page Content Section -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6 text-center text-gray-600 text-sm">
        <p>&copy; 2024 Online Shop Admin Panel. All rights reserved.</p>
    </footer> --}}

    <!-- Scripts -->
    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
        }

        // Toggle Dropdown Menus
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close sidebar when clicking nav link on mobile
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    const sidebar = document.querySelector('.sidebar');
                    if (sidebar) {
                        sidebar.classList.add('-translate-x-full');
                    }
                }
            });
        });

        // Logout Function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                document.querySelector('form[action="{{ route('admin.logout') }}"]').submit();
            }
        }

        function dismissBanner() {
            const success = document.getElementById('success-banner');
            const error = document.getElementById('error-banner');

            if (success) success.remove();
            if (error) error.remove();
        }

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            dismissBanner();
        }, 5000);
    </script>

    @stack('scripts')

    {{-- Socket.IO — real-time admin alerts --}}
    <script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
    <script>
        const adminSocket = io('http://127.0.0.1:3001', {
            transports: ['websocket', 'polling']
        });
        adminSocket.on('connect', () => console.log('[Admin] Socket connected'));
        adminSocket.on('connect_error', (err) => console.error('[Admin] Socket error:', err.message));
        adminSocket.on('admin-notification', (data) => {
            if (data && data.title) {
                const toast = document.createElement('div');
                toast.id = 'socket-toast-' + Date.now();
                toast.className = 'fixed bottom-4 right-4 z-[9999] bg-white rounded-xl shadow-2xl border-l-4 border-l-blue-500 px-5 py-4 min-w-[320px] max-w-[420px] animate-slide-in';
                toast.innerHTML =
                    '<div class="flex items-start gap-3">' +
                    '<div class="shrink-0 w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg">' +
                    '<i class="fas fa-shopping-cart"></i></div>' +
                    '<div class="flex-1 min-w-0">' +
                    '<p class="text-sm font-semibold text-gray-900 truncate">' + data.title + '</p>' +
                    '<p class="text-sm text-gray-500 leading-snug mt-0.5 line-clamp-2">' + data.message + '</p>' +
                    '</div>' +
                    '<button onclick="this.parentElement.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 ml-2">' +
                    '<i class="fas fa-times text-sm"></i></button>' +
                    '</div>';
                document.body.appendChild(toast);
                setTimeout(() => { const el = document.getElementById(toast.id); if (el) el.remove(); }, 8000);
            }
        });
    </script>
    <style>
        .animate-slide-in {
            animation: slideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(80px) scale(0.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
    </style>
</body>

</html>
