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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Prevent sidebar flash on load + responsive per_page -->
    <script>
        (function(){
            if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-start-collapsed');
            }
            // Set per_page based on screen width via cookie (no URL param)
            try { document.cookie = 'per_page=' + (window.innerWidth > 1199 ? 10 : 8) + ';path=/'; } catch(e) {};
        })();
    </script>
    <style>
        .sidebar-start-collapsed .sidebar { width: 74px !important; }
        .sidebar-start-collapsed .sidebar .nav-link span,
        .sidebar-start-collapsed .sidebar .logo-text,
        .sidebar-start-collapsed .sidebar .logo-sub { display: none !important; }
        .sidebar-start-collapsed .sidebar .logo-section { justify-content: center !important; padding: 1.5rem 0.5rem !important; gap: 0 !important; }
        .sidebar-start-collapsed .sidebar .nav-link { justify-content: center !important; padding: 0.75rem 0 !important; margin: 0 0.75rem !important; gap: 0 !important; }
    </style>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        .rounded-lg, .rounded-xl, .rounded-2xl { border-radius: 4px !important; }
        nav[aria-label="Pagination"] { background: transparent !important; }
        nav[aria-label="Pagination"] a,
        nav[aria-label="Pagination"] span {
            background: #fff !important;
            color: #6b7280 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 4px !important;
            margin: 0 2px !important;
            transition: all 0.15s ease !important;
            font-weight: 500 !important;
            padding: 6px 14px !important;
        }
        nav[aria-label="Pagination"] a:hover {
            background: #f9fafb !important;
            border-color: #d1d5db !important;
        }
        nav[aria-label="Pagination"] span[aria-current="page"] {
            background: #f3f4f6 !important;
            color: #374151 !important;
            border-color: #d1d5db !important;
            font-weight: 600 !important;
        }
        nav[aria-label="Pagination"] span[aria-disabled="true"] {
            opacity: 0.4 !important;
            cursor: default !important;
        }
        nav[aria-label="Pagination"] svg { display: none !important; }
        nav[aria-label="Pagination"] a:first-child,
        nav[aria-label="Pagination"] span:first-child {
            margin-left: 0 !important;
        }

        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; }

        .sidebar {
            transition: width 0.3s ease-in-out, transform 0.3s ease;
            overflow: hidden;
            width: 256px;
        }

        .sidebar.sidebar-collapsed {
            width: 74px !important;
        }

        /* Main content margin matches sidebar width */
        #main-content {
            margin-left: 256px;
            transition: margin-left 0.3s ease-in-out;
        }
        #sidebar.sidebar-collapsed + #main-content,
        #sidebar.sidebar-collapsed ~ #main-content {
            margin-left: 74px !important;
        }
        @media (max-width: 1199px) and (min-width: 768px) {
            #sidebar { width: 80px !important; }
            #sidebar .nav-link { justify-content: center !important; padding: 0.75rem 0 !important; margin: 0 0.75rem !important; gap: 0 !important; }
            #sidebar .nav-link span, #sidebar .logo-text, #sidebar .logo-sub { max-width: 0 !important; opacity: 0 !important; padding: 0 !important; overflow: hidden !important; }
            #sidebar .logo-section { justify-content: center; padding: 1.5rem 0.5rem !important; gap: 0 !important; }
            #main-content { margin-left: 80px !important; }
            #sidebar.sidebar-collapsed + #main-content,
            #sidebar.sidebar-collapsed ~ #main-content {
                margin-left: 80px !important;
            }
        }
        @media (max-width: 767px) {
            #sidebar { width: 256px !important; }
            #main-content { margin-left: 0 !important; }
        }

        .logo-section { transition: padding 0.35s cubic-bezier(0.4, 0, 0.2, 1), gap 0.35s ease; }

        .nav-link {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            margin: 0 0.5rem !important;
            border-radius: 0.5rem !important;
            color: #d1d5db !important;
            background: transparent !important;
            transition: background 0.2s ease, color 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-link:hover { background-color: rgba(255,255,255,0.1) !important; color: #fff !important; }

        .nav-link.active { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; color: #fff !important; }

        .nav-link.active i { color: #93c5fd !important; }

        .nav-link i { width: 1.25rem !important; text-align: center !important; flex-shrink: 0 !important; font-size: 1.1rem !important; color: inherit !important; }

        .sidebar-collapsed .nav-link {
            justify-content: center !important;
            padding: 0.75rem 0 !important;
            margin: 0 0.75rem !important;
            gap: 0 !important;
        }

        .sidebar-collapsed .nav-link:hover { background-color: rgba(255,255,255,0.15) !important; }

        .sidebar-collapsed .nav-link.active { background: transparent !important; }

        .nav-link span {
            transition: max-width 0.3s ease-in-out, opacity 0.3s ease-in-out, padding 0.3s ease-in-out;
            max-width: 200px;
            opacity: 1;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-collapsed .nav-link span {
            max-width: 0 !important;
            opacity: 0 !important;
            padding: 0 !important;
        }
        .logo-text, .logo-sub {
            transition: max-width 0.3s ease-in-out, opacity 0.3s ease-in-out, padding 0.3s ease-in-out;
            max-width: 200px;
            opacity: 1;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-collapsed .logo-text,
        .sidebar-collapsed .logo-sub {
            max-width: 0 !important;
            opacity: 0 !important;
            padding: 0 !important;
        }

        .sidebar-collapsed .logo-section {
            justify-content: center;
            padding: 1.5rem 0.5rem !important;
            gap: 0 !important;
        }

        .dropdown-menu { display: none; transition: all 0.3s ease; }
        .dropdown-menu.show { display: block; }

        .card { border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: box-shadow 0.3s ease; }
        .card:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        .page-content {
            padding: 32px;
        }

        /* ===== Responsive: Tablet (768px - 1199px) ===== */
        @media (max-width: 1199px) and (min-width: 768px) {
            .page-content { padding: 16px !important; }
            .sidebar { width: 80px !important; }
            .sidebar .nav-link { justify-content: center !important; padding: 0.75rem 0 !important; margin: 0 0.75rem !important; gap: 0 !important; }
            .sidebar .nav-link span { display: none; }
            .sidebar .logo-text,
            .sidebar .logo-sub { display: none; }
            .sidebar .logo-section { justify-content: center; padding: 1.5rem 0.5rem !important; gap: 0 !important; }
            header .px-6 { padding-left: 16px !important; padding-right: 16px !important; }
            .hide-tablet { display: none !important; }
        }

        /* ===== Responsive: Mobile (< 768px) ===== */
        @media (max-width: 767px) {
            .desktop-tablet-only { display: none !important; }
            .mobile-unsupported { display: flex !important; }
        }

        .mobile-unsupported { display: none; position: fixed; inset: 0; z-index: 9999; background: #fff; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 32px; }
        /* Content fade wrapper - only main content animates, sidebar and header stay static */
        .content-fade { transition: opacity 0.7s ease, transform 0.7s ease; }
        .content-fade.fade-out { opacity: 0; transform: translateY(8px); }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100">

    <!-- Mobile Unsupported Banner -->
    <div class="mobile-unsupported">
        <i class="fas fa-laptop text-6xl text-gray-300 mb-6"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Desktop &amp; Tablet Only</h2>
        <p class="text-gray-500 max-w-sm">The admin dashboard is optimized for desktop and tablet. Please access from a larger screen.</p>
    </div>

    <div id="app" class="flex h-screen bg-gray-100 desktop-tablet-only">

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-gray-900 text-gray-100 fixed h-full z-40 w-64 -translate-x-full md:translate-x-0">
            <!-- Logo Section (click to toggle sidebar) -->
            <div onclick="window.innerWidth < 768 ? toggleSidebar() : toggleSidebarDesktop()" class="logo-section flex items-center gap-3 p-6 border-b border-gray-700 cursor-pointer">
                <img src="{{ asset('images/logo.png') }}" alt="Online Shop Logo"
                    class="w-10 h-10 object-contain rounded-3xl shrink-0">
                <div class="logo-text">
                    <h1 class="text-xl font-bold text-white">Online Shop</h1>
                    <p class="logo-sub text-xs text-gray-400">Admin Panel</p>
                </div>

            </div>

            <!-- Navigation Menu -->
            <nav class="mt-4 px-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link @if (request()->routeIs('admin.dashboard')) active @endif"
                    title="Dashboard">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Categories -->
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link @if (request()->routeIs('admin.categories*')) active @endif"
                    title="Categories">
                    <i class="fas fa-tag"></i>
                    <span>Categories</span>
                </a>

                <!-- Products -->
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link @if (request()->routeIs('admin.products*')) active @endif"
                    title="Products">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>

                <!-- Orders -->
                <a href="{{ route('admin.orders.index') }}"
                    class="nav-link @if (request()->routeIs('admin.orders*')) active @endif"
                    title="Orders">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link @if (request()->routeIs('admin.users*')) active @endif"
                    title="Users">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>

                <!-- Divider -->
                <div class="border-t border-gray-700 my-4"></div>

                <!-- Settings -->
                <a href="{{ route('admin.settings.index') }}"
                    class="nav-link @if (request()->routeIs('admin.settings')) active @endif"
                    title="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div id="main-content" class="flex-1 flex flex-col overflow-hidden" style="transition: margin-left 0.3s ease-in-out;">

            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4 flex justify-between items-center">
                    <!-- Page Title -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-500 mt-1">@yield('page_subtitle', 'Welcome to your dashboard')</p>
                    </div>

                    <!-- User Menu -->
                    <div id="admin-user-menu" class="flex items-center gap-4">
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
                <div id="content-fade" class="content-fade">
                <div class="p-6 page-content">

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
        const SIDEBAR_STORAGE_KEY = 'admin_sidebar_collapsed';

        // Initialize sidebar state from localStorage
        function initSidebar() {
            var sidebar = document.getElementById('sidebar');
            if (!sidebar) return;

            var stored = localStorage.getItem(SIDEBAR_STORAGE_KEY);
            if (stored === 'true') {
                sidebar.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
            }
            document.documentElement.classList.remove('sidebar-start-collapsed');

            // Update active nav link based on current URL
            var navLinks = sidebar.querySelectorAll('.nav-link');
            var currentPath = window.location.pathname;
            navLinks.forEach(function(link) {
                link.classList.remove('active');
                var href = link.getAttribute('href');
                if (!href) return;

                var linkPath = href.replace(/https?:\/\/[^\/]+/, '');

                // Dashboard: only exact match to avoid highlighting on all /admin/* pages
                if (linkPath === '/admin' || linkPath === '/admin/') {
                    if (currentPath === '/admin' || currentPath === '/admin/' || currentPath === '/admin/dashboard') {
                        link.classList.add('active');
                    }
                    return;
                }

                // Other pages: exact or prefix match (e.g., /admin/products matches /admin/products/5)
                if (currentPath === linkPath || currentPath.indexOf(linkPath + '/') === 0) {
                    link.classList.add('active');
                }
            });
        }

        // Toggle Sidebar for Desktop (collapse/expand)
        function toggleSidebarDesktop() {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;

            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            if (isCollapsed) {
                sidebar.classList.remove('sidebar-collapsed');
                localStorage.setItem(SIDEBAR_STORAGE_KEY, 'false');
            } else {
                sidebar.classList.add('sidebar-collapsed');
                localStorage.setItem(SIDEBAR_STORAGE_KEY, 'true');
            }
        }

        // Toggle Sidebar for Mobile (show/hide)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;

            sidebar.classList.toggle('-translate-x-full');
        }

        // ===== AJAX Navigation (smooth page switching without full reloads) =====
        function adminNavigate(href, replace) {
            if (!href || href === '#' || href.startsWith('mailto:') || href.startsWith('tel:')) return;

            var contentFade = document.getElementById('content-fade');
            var navStart = Date.now();

            // Fade out current content only
            if (contentFade) contentFade.classList.add('fade-out');

            fetch(href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(function(r) {
                if (!r.ok) throw new Error('Fetch failed');
                return r.text();
            })
            .then(function(html) {
                if (!contentFade) throw new Error('No content-fade');

                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newContent = doc.getElementById('content-fade');

                if (!newContent) { window.location.href = href; return; }

                // Ensure minimum transition time so fade-out is always visible
                var elapsed = Date.now() - navStart;
                var minFadeOut = 600;
                var remaining = Math.max(0, minFadeOut - elapsed);

                return new Promise(function(resolve) {
                    setTimeout(function() {
                        try {
                            document.querySelectorAll('canvas').forEach(function(c) {
                                if (typeof Chart !== 'undefined') {
                                    var chart = Chart.getChart(c);
                                    if (chart) chart.destroy();
                                }
                            });

                            contentFade.innerHTML = newContent.innerHTML;

                            contentFade.querySelectorAll('script').forEach(function(oldScript) {
                                var newScript = document.createElement('script');
                                Array.from(oldScript.attributes).forEach(function(attr) {
                                    newScript.setAttribute(attr.name, attr.value);
                                });
                                newScript.textContent = oldScript.textContent;
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });

                            var newPageScripts = doc.getElementById('page-scripts');
                            var curPageScripts = document.getElementById('page-scripts');
                            if (newPageScripts && curPageScripts) {
                                curPageScripts.innerHTML = newPageScripts.innerHTML;
                                curPageScripts.querySelectorAll('script').forEach(function(oldScript) {
                                    var newScript = document.createElement('script');
                                    Array.from(oldScript.attributes).forEach(function(attr) {
                                        newScript.setAttribute(attr.name, attr.value);
                                    });
                                    newScript.textContent = oldScript.textContent;
                                    oldScript.parentNode.replaceChild(newScript, oldScript);
                                });
                            }

                            var title = doc.querySelector('title');
                            if (title) document.title = title.textContent;

                            if (replace) {
                                history.replaceState({ url: href }, '', href);
                            } else {
                                history.pushState({ url: href }, '', href);
                            }
                        } catch (e) {
                            console.error('AJAX nav error:', e);
                        }

                        void contentFade.offsetWidth;
                        contentFade.classList.remove('fade-out');

                        initSidebar();
                        resolve();
                    }, remaining);
                });
            })
            .catch(function() {
                window.location.href = href;
            })
            .finally(function() {});

            return true;
        }

        function initAjaxNav() {
            var sidebar = document.getElementById('sidebar');

            // Event delegation on sidebar for nav-links
            if (sidebar) {
                sidebar.addEventListener('click', function(e) {
                    var link = e.target.closest('.nav-link');
                    if (!link) return;

                    if (window.innerWidth < 768) {
                        sidebar.classList.add('-translate-x-full');
                    }

                    e.preventDefault();
                    adminNavigate(link.getAttribute('href'));
                });
            }

            // Intercept all same-origin link clicks inside content area (pagination, sort, quick links, etc.)
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a[href]');
                if (!link) return;
                if (link.closest('#sidebar')) return;
                if (link.closest('#admin-user-menu')) return;
                if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
                if (link.getAttribute('href').indexOf('://') !== -1 && link.getAttribute('href').indexOf(window.location.host) === -1) return;
                e.preventDefault();
                adminNavigate(link.getAttribute('href'));
            });

            // Intercept GET filter/search forms inside content area
            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form.closest('#content-fade')) return;
                var method = (form.getAttribute('method') || 'GET').toUpperCase();
                if (method !== 'GET') return;

                e.preventDefault();
                var url = new URL(form.action, window.location.origin);
                var params = new URLSearchParams(new FormData(form));
                params.forEach(function(value, key) {
                    if (value) url.searchParams.set(key, value);
                });
                adminNavigate(url.pathname + url.search);
            });

            // Handle browser back/forward via AJAX (replace to avoid duplicating history)
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) {
                    adminNavigate(e.state.url, true);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initSidebar();
            initAjaxNav();
        });

        // Toggle Dropdown Menus
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        function dismissBanner() {
            const success = document.getElementById('success-banner');
            const error = document.getElementById('error-banner');

            if (success) success.remove();
            if (error) error.remove();
        }

        // Auto dismiss after 5 seconds
        var bannerTimer = setTimeout(function() {
            dismissBanner();
        }, 5000);
        document.addEventListener('turbo:before-cache', function() {
            clearTimeout(bannerTimer);
        });
    </script>

    <div id="page-scripts">
        @stack('scripts')
    </div>

    {{-- Socket.IO — real-time admin alerts --}}
    <script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
    <script>
        let adminSocket = null;

        function connectSocket() {
            if (adminSocket?.connected) return;
            if (adminSocket) { adminSocket.disconnect(); }

            adminSocket = io('http://127.0.0.1:3001', {
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
        }

        document.addEventListener('DOMContentLoaded', connectSocket);
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
