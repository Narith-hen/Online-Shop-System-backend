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

    <title>@yield('title', 'Dashboard') - Online Shop Admin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        (function(){
            if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-start-collapsed');
            }
        })();
    </script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; }

        /* ===== Sidebar ===== */
        .sidebar { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; width: 256px; z-index: 40; outline: none !important; }
        .sidebar * { outline: none !important; }
        .sidebar:focus, .sidebar *:focus { outline: none !important; box-shadow: none !important; }
        .sidebar-collapsed { width: 74px !important; }
        .sidebar-collapsed .nav-link { justify-content: center !important; padding: 0.75rem 0 !important; margin: 0 0.75rem !important; gap: 0 !important; }
        .sidebar-collapsed .nav-link span { max-width: 0 !important; opacity: 0 !important; padding: 0 !important; overflow: hidden; }
        .sidebar-collapsed .logo-text, .sidebar-collapsed .logo-sub { max-width: 0 !important; opacity: 0 !important; padding: 0 !important; overflow: hidden; }
        .sidebar-collapsed .logo-section { justify-content: center !important; padding: 1.5rem 0.5rem !important; gap: 0 !important; }
        .sidebar-collapsed .nav-link i { font-size: 1.25rem !important; }

        #main-content { margin-left: 0; transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        .nav-link { display: flex !important; align-items: center !important; gap: 0.75rem !important; padding: 0.75rem 1rem !important; margin: 0 0.5rem !important; border-radius: 0.5rem !important; color: #d1d5db !important; transition: all 0.2s ease; white-space: nowrap; overflow: hidden; font-size: 0.875rem !important; font-weight: 500 !important; }
        .nav-link:hover { background-color: rgba(255,255,255,0.1) !important; color: #fff !important; }
        .nav-link.active { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; color: #fff !important; box-shadow: 0 4px 12px rgba(59,130,246,0.3) !important; }
        .nav-link.active i { color: #93c5fd !important; }
        .nav-link i { width: 1.25rem !important; text-align: center !important; flex-shrink: 0 !important; font-size: 1.1rem !important; color: inherit !important; transition: font-size 0.3s; }
        .nav-link span { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); max-width: 200px; opacity: 1; overflow: hidden; }

        .logo-section { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
        .logo-text, .logo-sub { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); max-width: 200px; opacity: 1; overflow: hidden; white-space: nowrap; }

        /* ===== Header ===== */
        .header-bar { background: #fff; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 30; }
        .header-btn { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all 0.2s; cursor: pointer; border: none; background: transparent; position: relative; }
        .header-btn:hover { background: #f3f4f6; color: #374151; }
        .header-btn .badge { position: absolute; top: -2px; right: -2px; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; color: #fff; }

        /* ===== Notifications Dropdown ===== */
        .notif-dropdown { position: absolute; right: 0; top: calc(100% + 8px); width: 360px; background: #fff; border-radius: 12px; box-shadow: 0 20px 60px -12px rgba(0,0,0,0.2); opacity: 0; visibility: hidden; transform: translateY(-4px) scale(0.98); transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); z-index: 50; max-height: 420px; overflow: hidden; }
        .notif-trigger:hover .notif-dropdown { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
        .notif-dropdown-header { padding: 0.875rem 1rem; border-bottom: 1px solid #f3f4f6; font-weight: 600; font-size: 0.9rem; color: #1f2937; }
        .notif-item { display: block; padding: 0.75rem 1rem; border-bottom: 1px solid #f9fafb; transition: background 0.15s; }
        .notif-item:hover { background: #f9fafb; }
        .notif-item:last-child { border-bottom: none; }
        .notif-scroll { overflow-y: auto; max-height: 320px; }

        /* ===== User Dropdown ===== */
        .user-dropdown { position: absolute; right: 0; top: calc(100% + 8px); width: 200px; background: #fff; border-radius: 12px; box-shadow: 0 20px 60px -12px rgba(0,0,0,0.2); opacity: 0; visibility: hidden; transform: translateY(-4px); transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); z-index: 50; padding: 0.375rem; }
        .user-trigger:hover .user-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
        .user-dropdown-item { display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 0.75rem; border-radius: 8px; color: #374151; font-size: 0.85rem; font-weight: 500; transition: background 0.15s; cursor: pointer; text-decoration: none; }
        .user-dropdown-item:hover { background: #f3f4f6; color: #111827; }
        .user-dropdown-item.danger { color: #ef4444; }
        .user-dropdown-item.danger:hover { background: #fef2f2; }
        .user-dropdown-divider { height: 1px; background: #f3f4f6; margin: 0.25rem 0.5rem; }

        /* ===== Content ===== */
        .page-content { padding: 1.5rem; animation: fadeUp 0.35s ease; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .content-fade { transition: opacity 0.4s ease, transform 0.4s ease; }
        .content-fade.fade-out { opacity: 0; transform: translateY(6px); }

        /* ===== Cards ===== */
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: box-shadow 0.2s; }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

        /* ===== Pagination ===== */
        nav[aria-label="Pagination"] { background: transparent !important; display: flex !important; align-items: center !important; gap: 2px !important; }
        nav[aria-label="Pagination"] a, nav[aria-label="Pagination"] span {
            background: #fff !important; color: #6b7280 !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important;
            transition: all 0.15s !important; font-weight: 500 !important; padding: 6px 14px !important; font-size: 0.85rem !important;
            min-width: 36px; text-align: center; display: inline-flex; align-items: center; justify-content: center;
        }
        nav[aria-label="Pagination"] a:hover { background: #f9fafb !important; border-color: #d1d5db !important; }
        nav[aria-label="Pagination"] span[aria-current="page"] { background: #f3f4f6 !important; color: #374151 !important; border-color: #d1d5db !important; font-weight: 600 !important; }
        nav[aria-label="Pagination"] span[aria-disabled="true"] { opacity: 0.4 !important; cursor: default !important; }
        nav[aria-label="Pagination"] svg { display: none !important; }

        /* ===== Toast ===== */
        .toast { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; padding: 0.875rem 1.25rem; border-radius: 10px; color: #fff; font-weight: 500; font-size: 0.875rem; box-shadow: 0 10px 30px rgba(0,0,0,0.15); animation: toastIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); display: flex; align-items: center; gap: 0.5rem; max-width: 400px; }
        .toast-success { background: #059669; }
        .toast-error { background: #dc2626; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(40px) scale(0.95); } to { opacity: 1; transform: translateX(0) scale(1); } }

        /* ===== Modal ===== */
        .modal-overlay { position: fixed; inset: 0; z-index: 50; display: none; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); animation: fadeIn 0.2s ease; }
        .modal-content { position: relative; background: #fff; border-radius: 14px; box-shadow: 0 25px 60px rgba(0,0,0,0.2); width: 100%; max-width: 540px; max-height: 90vh; overflow-y: auto; margin: 1rem; animation: modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
        .modal-sm { max-width: 420px; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
        .modal-header h3 { font-size: 1.1rem; font-weight: 700; color: #1f2937; }
        .modal-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; cursor: pointer; transition: all 0.15s; border: none; background: transparent; font-size: 1.1rem; }
        .modal-close:hover { background: #f3f4f6; color: #374151; }
        .modal-body { padding: 1.5rem; }

        /* ===== Bulk Bar ===== */
        #bulk-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 40; transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        #bulk-bar.active { transform: translateY(0); }

        /* ===== Responsive ===== */
        @media (max-width: 1199px) and (min-width: 768px) {
            .sidebar { width: 80px !important; }
            .sidebar .nav-link { justify-content: center !important; padding: 0.75rem 0 !important; margin: 0 0.75rem !important; gap: 0 !important; }
            .sidebar .nav-link span { display: none; }
            .sidebar .logo-text, .sidebar .logo-sub { display: none; }
            .sidebar .logo-section { justify-content: center; padding: 1.5rem 0.5rem !important; gap: 0 !important; }
            .sidebar .nav-link i { font-size: 1.25rem !important; }
            .page-content { padding: 1rem !important; }
            .hide-tablet { display: none !important; }
        }
        @media (max-width: 767px) {
            .sidebar { position: fixed !important; transform: translateX(-100%); width: 280px !important; }
            .sidebar.open { transform: translateX(0); }
            .page-content { padding: 0.75rem !important; }
            .mobile-header { display: flex !important; }
            .hide-mobile { display: none !important; }
            .notif-dropdown { width: 300px; right: -60px; }
            .user-dropdown { right: 0; }
        }
        .mobile-header { display: none; }
        .mobile-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 35; }
        .mobile-overlay.active { display: block; }
        .mobile-unsupported { display: none; position: fixed; inset: 0; z-index: 9999; background: #fff; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 2rem; }
        @media (max-width: 767px) {
            .mobile-unsupported { display: flex !important; }
            #app { display: none !important; }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100">

    <!-- Mobile Unsupported Banner -->
    <div class="mobile-unsupported">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
            <i class="fas fa-store text-white text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Desktop &amp; Tablet Only</h2>
        <p class="text-gray-500 max-w-sm mb-6">The admin dashboard is optimized for larger screens. Please access from a desktop, laptop, or tablet.</p>
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <i class="fas fa-desktop"></i>
            <i class="fas fa-tablet-alt"></i>
            <i class="fas fa-laptop"></i>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="mobile-overlay" onclick="closeMobileSidebar()"></div>

    <div id="app" class="flex h-screen bg-gray-100 overflow-hidden">

        <!-- ===== Sidebar ===== -->
        <aside id="sidebar" class="sidebar bg-gray-900 text-gray-100 fixed md:relative h-full flex flex-col">
            <!-- Logo -->
            <div onclick="toggleSidebar()" class="logo-section flex items-center gap-3 px-5 py-5 border-b border-gray-800 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl shrink-0">
                <div class="logo-text">
                    <h1 class="text-lg font-bold text-white">Online Shop</h1>
                    <p class="logo-sub text-[11px] text-gray-400">Admin Panel</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" title="Dashboard">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link @if(request()->routeIs('admin.categories*')) active @endif" title="Categories">
                    <i class="fas fa-tag"></i>
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link @if(request()->routeIs('admin.products*')) active @endif" title="Products">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="nav-link @if(request()->routeIs('admin.orders*')) active @endif" title="Orders">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link @if(request()->routeIs('admin.users*')) active @endif" title="Users">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}"
                    class="nav-link @if(request()->routeIs('admin.notifications*')) active @endif" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>

                <div class="border-t border-gray-800 my-3"></div>

                <a href="{{ route('admin.settings.index') }}"
                    class="nav-link @if(request()->routeIs('admin.settings')) active @endif" title="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-3 border-t border-gray-800 shrink-0">
                <button onclick="toggleSidebar()" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition text-xs">
                    <i class="fas fa-chevron-left" id="collapseIcon"></i>
                    <span id="collapseText">Collapse</span>
                </button>
            </div>
        </aside>

        <!-- ===== Main Content ===== -->
        <div id="main-content" class="flex-1 flex flex-col overflow-hidden">

            <!-- ===== Header ===== -->
            <header class="header-bar">
                <div class="flex items-center justify-between px-4 lg:px-6 py-3">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleMobileSidebar()" class="header-btn md:hidden">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div>
                            <h2 class="text-xl lg:text-2xl font-bold text-gray-900">@yield('page_title', 'Dashboard')</h2>
                            <p class="text-xs lg:text-sm text-gray-500 mt-0.5">@yield('page_subtitle', 'Welcome to your dashboard')</p>
                        </div>
                    </div>

                    @php
                        $notifications = \App\Models\Notification::latest()->take(5)->get();
                        $unreadCount = \App\Models\Notification::whereDoesntHave('reads', function ($q) {
                            $q->where('user_id', auth()->id() ?? 0);
                        })->count();
                    @endphp

                    <div class="flex items-center gap-2">
                        <!-- Notifications -->
                        <div class="relative notif-trigger">
                            <button class="header-btn">
                                <i class="fas fa-bell text-lg"></i>
                                @if($unreadCount > 0)
                                    <span class="badge bg-red-500">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>
                            <div class="notif-dropdown">
                                <div class="notif-dropdown-header">
                                    <span>Notifications</span>
                                    @if($unreadCount > 0)
                                        <span class="ml-1 text-xs text-gray-400">({{ $unreadCount }} unread)</span>
                                    @endif
                                </div>
                                <div class="notif-scroll">
                                    @forelse($notifications as $notif)
                                        <a href="{{ route('admin.notifications.show', $notif->id) }}" class="notif-item">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $notif->title }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $notif->message ?? 'No message' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <div class="px-4 py-10 text-center text-gray-400">
                                            <i class="fas fa-bell text-2xl mb-2 block"></i>
                                            <p class="text-sm">No notifications</p>
                                        </div>
                                    @endforelse
                                </div>
                                <a href="{{ route('admin.notifications.index') }}" class="block text-center py-2.5 text-sm font-medium text-blue-600 hover:bg-blue-50 transition rounded-b-lg border-t border-gray-100">
                                    View All Notifications
                                </a>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative user-trigger">
                            <button class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-100 transition">
                                @if(optional(Auth::user())->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="User" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
                                        <span class="text-white font-bold text-xs">{{ substr(optional(Auth::user())->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-700 hide-mobile">{{ optional(Auth::user())->name ?? 'Admin' }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 hide-mobile"></i>
                            </button>

                            <div class="user-dropdown">
                                <a href="{{ route('admin.settings.index') }}" class="user-dropdown-item">
                                    <i class="fas fa-user text-gray-400"></i> Profile & Settings
                                </a>
                                <div class="user-dropdown-divider"></div>
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full user-dropdown-item danger">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ===== Page Content ===== -->
            <main class="flex-1 overflow-y-auto">
                <div id="content-fade" class="content-fade">
                    <div class="page-content">

                        <!-- Success Banner -->
                        @if ($message = Session::get('success'))
                            <div id="success-banner" class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 shadow-sm">
                                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-emerald-800 font-medium text-sm">{{ $message }}</p>
                                </div>
                                <button onclick="dismissBanner(this)" class="text-emerald-400 hover:text-emerald-600 transition shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif

                        <!-- Error Banner -->
                        @if ($message = Session::get('error'))
                            <div id="error-banner" class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4 mb-6 shadow-sm">
                                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fas fa-exclamation-circle text-white text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-red-800 font-medium text-sm">{{ $message }}</p>
                                </div>
                                <button onclick="dismissBanner(this)" class="text-red-400 hover:text-red-600 transition shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- ==================== DELETE CONFIRMATION MODAL ==================== -->
    <div id="delete-modal" class="modal-overlay">
        <div class="modal-backdrop" onclick="closeDeleteModal()"></div>
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3 id="delete-modal-title">Delete</h3>
                <button onclick="closeDeleteModal()" class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-trash text-red-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600" id="delete-modal-message">Are you sure? This cannot be undone.</p>
                </div>
                <input type="password" id="delete-password" placeholder="Enter your password to confirm"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm mb-2">
                <p id="delete-error" class="text-red-600 text-sm hidden">Incorrect password.</p>
                <div class="flex items-center justify-center gap-3 mt-4">
                    <button onclick="closeDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
                    <button onclick="confirmDelete()" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== BULK DELETE CONFIRMATION MODAL ==================== -->
    <div id="bulk-delete-modal" class="modal-overlay">
        <div class="modal-backdrop" onclick="closeBulkDeleteModal()"></div>
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3>Delete Selected Items</h3>
                <button onclick="closeBulkDeleteModal()" class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-trash text-red-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-1" id="bulk-delete-message">Delete the selected items?</p>
                    <p class="text-sm font-semibold text-red-600" id="bulk-delete-count"></p>
                </div>
                <input type="password" id="bulk-delete-password" placeholder="Enter your password to confirm"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm mb-2">
                <p id="bulk-delete-error" class="text-red-600 text-sm hidden">Incorrect password.</p>
                <div class="flex items-center justify-center gap-3 mt-4">
                    <button onclick="closeBulkDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
                    <button onclick="confirmBulkDelete()" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">Delete All</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== SIDEBAR ====================
        const SIDEBAR_STORAGE_KEY = 'admin_sidebar_collapsed';

        function initSidebar() {
            var sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            var stored = localStorage.getItem(SIDEBAR_STORAGE_KEY);
            if (stored === 'true') {
                sidebar.classList.add('sidebar-collapsed');
                var icon = document.getElementById('collapseIcon');
                var text = document.getElementById('collapseText');
                if (icon) { icon.className = 'fas fa-chevron-right'; }
                if (text) { text.textContent = 'Expand'; }
            }
            updateActiveNav();
        }

        function updateActiveNav() {
            var links = document.querySelectorAll('.nav-link');
            var path = window.location.pathname;
            links.forEach(function(link) {
                link.classList.remove('active');
                var href = link.getAttribute('href');
                if (!href) return;
                var clean = href.replace(/https?:\/\/[^\/]+/, '');
                if (clean === '/admin' || clean === '/admin/') {
                    if (path === '/admin' || path === '/admin/' || path === '/admin/dashboard') link.classList.add('active');
                } else if (path === clean || path.indexOf(clean + '/') === 0) {
                    link.classList.add('active');
                }
            });
        }

        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            var collapsed = sidebar.classList.contains('sidebar-collapsed');
            var wasMobile = window.innerWidth < 768;

            if (wasMobile) { closeMobileSidebar(); return; }

            if (collapsed) {
                sidebar.classList.remove('sidebar-collapsed');
                localStorage.setItem(SIDEBAR_STORAGE_KEY, 'false');
                document.getElementById('collapseIcon').className = 'fas fa-chevron-left';
                document.getElementById('collapseText').textContent = 'Collapse';
            } else {
                sidebar.classList.add('sidebar-collapsed');
                localStorage.setItem(SIDEBAR_STORAGE_KEY, 'true');
                document.getElementById('collapseIcon').className = 'fas fa-chevron-right';
                document.getElementById('collapseText').textContent = 'Expand';
            }
        }

        function toggleMobileSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('mobileOverlay');
            if (!sidebar) return;
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active');
        }

        function closeMobileSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
        }

        // ==================== AJAX NAVIGATION ====================
        function adminNavigate(href, replace) {
            if (!href || href === '#' || href.startsWith('mailto:') || href.startsWith('tel:')) return;

            var fade = document.getElementById('content-fade');
            var start = Date.now();
            if (fade) fade.classList.add('fade-out');

            fetch(href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(function(r) { if (!r.ok) throw new Error(); return r.text(); })
            .then(function(html) {
                if (!fade) { window.location.href = href; return; }
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newContent = doc.getElementById('content-fade');
                if (!newContent) { window.location.href = href; return; }

                var elapsed = Date.now() - start;
                var remaining = Math.max(0, 400 - elapsed);

                return new Promise(function(resolve) {
                    setTimeout(function() {
                        document.querySelectorAll('canvas').forEach(function(c) {
                            if (typeof Chart !== 'undefined') { var ch = Chart.getChart(c); if (ch) ch.destroy(); }
                        });
                        fade.innerHTML = newContent.innerHTML;

                        // Re-execute scripts
                        fade.querySelectorAll('script').forEach(function(s) {
                            var ns = document.createElement('script');
                            Array.from(s.attributes).forEach(function(a) { ns.setAttribute(a.name, a.value); });
                            ns.textContent = s.textContent;
                            s.parentNode.replaceChild(ns, s);
                        });

                        var title = doc.querySelector('title');
                        if (title) document.title = title.textContent;
                        if (replace) history.replaceState({ url: href }, '', href);
                        else history.pushState({ url: href }, '', href);

                        void fade.offsetWidth;
                        fade.classList.remove('fade-out');
                        initSidebar();
                        resolve();
                    }, remaining);
                });
            })
            .catch(function() { window.location.href = href; });
        }

        // ==================== EVENT DELEGATION ====================
        document.addEventListener('DOMContentLoaded', function() {
            initSidebar();

            // Sidebar nav links
            document.getElementById('sidebar').addEventListener('click', function(e) {
                var link = e.target.closest('.nav-link');
                if (!link) return;
                if (window.innerWidth < 768) closeMobileSidebar();
                e.preventDefault();
                adminNavigate(link.getAttribute('href'));
            });

            // Content links (pagination, etc)
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a[href]');
                if (!link) return;
                if (link.closest('#sidebar') || link.closest('#admin-user-menu')) return;
                if (link.closest('.user-trigger') || link.closest('.notif-trigger')) return;
                if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
                if (link.getAttribute('href').startsWith('http') && !link.getAttribute('href').includes(window.location.host)) return;
                e.preventDefault();
                adminNavigate(link.getAttribute('href'));
            });

            // GET forms
            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form.closest('#content-fade')) return;
                if (form.getAttribute('method')?.toUpperCase() !== 'GET') return;
                e.preventDefault();
                var url = new URL(form.action, window.location.origin);
                new FormData(form).forEach(function(v, k) { if (v) url.searchParams.set(k, v); });
                adminNavigate(url.pathname + url.search);
            });

            // Back/forward
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) adminNavigate(e.state.url, true);
            });
        });

        // ==================== TOAST ====================
        function showToast(msg, type) {
            var existing = document.getElementById('dynamic-toast');
            if (existing) existing.remove();
            var toast = document.createElement('div');
            toast.id = 'dynamic-toast';
            toast.className = 'toast ' + (type === 'success' ? 'toast-success' : 'toast-error');
            toast.innerHTML = (type === 'success'
                ? '<i class="fas fa-check-circle"></i>'
                : '<i class="fas fa-exclamation-circle"></i>') + ' ' + msg;
            document.body.appendChild(toast);
            setTimeout(function() { if (toast.parentNode) toast.remove(); }, 3000);
        }

        // ==================== BANNERS ====================
        function dismissBanner(el) {
            var banner = el.closest('[id$="-banner"]');
            if (banner) banner.remove();
        }
        setTimeout(function() {
            document.querySelectorAll('[id$="-banner"]').forEach(function(el) {
                el.style.transition = 'opacity 0.5s, transform 0.5s';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(function() { if (el.parentNode) el.remove(); }, 500);
            });
        }, 5000);

        // ==================== DELETE MODAL ====================
        var deleteState = null;

        function showDeleteModal(itemType, itemId, deleteUrl, options) {
            options = options || {};
            deleteState = {
                itemType: itemType, itemId: itemId, deleteUrl: deleteUrl,
                redirectUrl: options.redirectUrl || null,
                onSuccess: options.onSuccess || null,
                successText: options.successText || itemType + ' deleted successfully.'
            };
            document.getElementById('delete-modal-title').textContent = 'Delete ' + itemType;
            document.getElementById('delete-modal-message').textContent = 'Are you sure you want to delete this ' + itemType.toLowerCase() + '? This action cannot be undone.';
            document.getElementById('delete-password').value = '';
            document.getElementById('delete-error').classList.add('hidden');
            document.getElementById('delete-modal').classList.add('active');
            setTimeout(function() { document.getElementById('delete-password').focus(); }, 100);
        }

        function closeDeleteModal() {
            deleteState = null;
            document.getElementById('delete-password').value = '';
            document.getElementById('delete-error').classList.add('hidden');
            document.getElementById('delete-modal').classList.remove('active');
        }

        function showBulkDeleteModal(count) {
            document.getElementById('bulk-delete-count').textContent = count + ' item(s) will be deleted.';
            document.getElementById('bulk-delete-password').value = '';
            document.getElementById('bulk-delete-error').classList.add('hidden');
            document.getElementById('bulk-delete-modal').classList.add('active');
            setTimeout(function() { document.getElementById('bulk-delete-password').focus(); }, 100);
        }

        function closeBulkDeleteModal() {
            document.getElementById('bulk-delete-password').value = '';
            document.getElementById('bulk-delete-error').classList.add('hidden');
            document.getElementById('bulk-delete-modal').classList.remove('active');
        }

        async function confirmDelete() {
            if (!deleteState) return;
            var state = deleteState;
            var pw = document.getElementById('delete-password').value;
            if (!pw) {
                document.getElementById('delete-error').textContent = 'Please enter your password.';
                document.getElementById('delete-error').classList.remove('hidden');
                return;
            }
            try {
                var res = await fetch(state.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ password: pw })
                });
                if (res.status === 403) {
                    var d = await res.json();
                    document.getElementById('delete-error').textContent = d && d.message ? d.message : 'Incorrect password.';
                    document.getElementById('delete-error').classList.remove('hidden');
                    document.getElementById('delete-password').value = '';
                    document.getElementById('delete-password').focus();
                    return;
                }
                closeDeleteModal();
                if (res.ok) {
                    showToast(state.successText, 'success');
                    if (typeof state.onSuccess === 'function') { await state.onSuccess(); }
                    else if (state.redirectUrl) {
                        if (typeof adminNavigate === 'function') adminNavigate(state.redirectUrl);
                        else window.location.href = state.redirectUrl;
                    } else if (typeof refreshTable === 'function') { await refreshTable(); }
                } else {
                    var d2 = res.ok ? null : await res.json().catch(() => null);
                    showToast(d2?.message || 'Delete failed.', 'error');
                }
            } catch (e) { showToast('Network error.', 'error'); }
        }

        // ==================== BULK SYSTEM ====================
        let bulkState = { ids: new Set(), url: '', csrf: '', tableId: '' }

        function initBulk(tableId, bulkUrl, csrf) {
            bulkState.tableId = tableId;
            bulkState.ids = new Set();
            bulkState.url = bulkUrl;
            bulkState.csrf = csrf || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            var table = document.getElementById(tableId);
            if (!table) return;
            table.removeEventListener('change', bulkHandler);
            table.addEventListener('change', bulkHandler);
            syncSelectAll(table);
        }

        function getTable() { return document.getElementById(bulkState.tableId); }

        function bulkHandler(e) {
            var table = e.currentTarget;
            var cb = e.target;
            if (cb.classList.contains('bulk-select-all')) {
                table.querySelectorAll('.bulk-checkbox').forEach(function(c) {
                    c.checked = cb.checked;
                    var id = c.getAttribute('data-id');
                    if (cb.checked) bulkState.ids.add(id);
                    else bulkState.ids.delete(id);
                });
                updateBar(); return;
            }
            if (cb.classList.contains('bulk-checkbox')) {
                var id = cb.getAttribute('data-id');
                if (cb.checked) bulkState.ids.add(id);
                else bulkState.ids.delete(id);
                syncSelectAll(table);
                updateBar();
            }
        }

        function syncSelectAll(table) {
            var sa = table.querySelector('.bulk-select-all');
            if (!sa) return;
            var cbs = table.querySelectorAll('.bulk-checkbox');
            sa.checked = cbs.length > 0 && Array.from(cbs).every(function(c) { return c.checked; });
        }

        function updateBar() {
            var bar = document.getElementById('bulk-bar');
            var count = document.getElementById('bulk-count-display');
            var label = document.getElementById('bulk-label');
            var total = bulkState.ids.size;
            count.textContent = total;
            label.textContent = total === 1 ? 'item selected' : 'items selected';
            bar.classList.toggle('active', total > 0);
        }

        function bulkDeselectAll() {
            bulkState.ids.clear();
            var table = getTable();
            if (table) {
                table.querySelectorAll('.bulk-checkbox').forEach(function(cb) { cb.checked = false; });
                table.querySelectorAll('.bulk-select-all').forEach(function(cb) { cb.checked = false; });
            }
            updateBar();
        }

        function bulkDeleteSelected() {
            if (bulkState.ids.size === 0) return;
            showBulkDeleteModal(bulkState.ids.size);
        }

        async function confirmBulkDelete() {
            var ids = Array.from(bulkState.ids);
            if (ids.length === 0) return;
            var pw = document.getElementById('bulk-delete-password').value;
            if (!pw) {
                document.getElementById('bulk-delete-error').textContent = 'Please enter your password.';
                document.getElementById('bulk-delete-error').classList.remove('hidden');
                return;
            }
            try {
                var res = await fetch(bulkState.url, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json',
                        'Content-Type': 'application/json', 'X-CSRF-TOKEN': bulkState.csrf,
                    },
                    body: JSON.stringify({ ids: ids, password: pw }),
                });
                var data = await res.json();
                if (res.status === 403) {
                    document.getElementById('bulk-delete-error').textContent = data?.message || 'Incorrect password.';
                    document.getElementById('bulk-delete-error').classList.remove('hidden');
                    document.getElementById('bulk-delete-password').value = '';
                    document.getElementById('bulk-delete-password').focus();
                    return;
                }
                closeBulkDeleteModal();
                if (res.ok) {
                    bulkDeselectAll();
                    showToast(data.message || ids.length + ' item(s) deleted.', 'success');
                    if (typeof refreshTable === 'function') await refreshTable();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Delete failed.', 'error');
                }
            } catch (e) { showToast('Network error.', 'error'); }
        }
    </script>

    <!-- Bulk Action Bar -->
    <div id="bulk-bar">
        <div class="max-w-7xl mx-auto px-4 pb-4">
            <div class="bg-gray-900 text-white rounded-xl shadow-2xl px-5 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold" id="bulk-count-display">0</span>
                    <span class="text-sm font-medium" id="bulk-label">selected</span>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="bulkDeselectAll()" class="px-4 py-1.5 text-sm text-gray-300 hover:text-white transition font-medium">Deselect All</button>
                    <button onclick="bulkDeleteSelected()" class="px-5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition font-medium flex items-center gap-2">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="page-scripts">@stack('scripts')</div>

    <!-- Socket.IO -->
    <script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
    <script>
        let adminSocket = null;
        function connectSocket() {
            if (adminSocket?.connected) return;
            if (adminSocket) adminSocket.disconnect();
            adminSocket = io('http://127.0.0.1:3001', { transports: ['websocket', 'polling'] });
            adminSocket.on('connect', () => console.log('[Admin] Socket connected'));
            adminSocket.on('connect_error', (err) => console.error('[Admin] Socket error:', err.message));
            adminSocket.on('admin-notification', (data) => {
                if (data && data.title) {
                    var toast = document.createElement('div');
                    toast.className = 'toast toast-success';
                    toast.style.animation = 'none';
                    toast.innerHTML = '<i class="fas fa-bell"></i> ' + data.title;
                    document.body.appendChild(toast);
                    setTimeout(() => { if (toast.parentNode) toast.remove(); }, 6000);
                }
            });
        }
        document.addEventListener('DOMContentLoaded', connectSocket);
    </script>
</body>
</html>
