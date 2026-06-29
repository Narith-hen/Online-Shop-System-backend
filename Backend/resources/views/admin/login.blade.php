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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-bg { position: fixed; inset: 0; background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e293b 100%); z-index: 0; }
        .login-bg::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.15) 0%, transparent 60%), radial-gradient(ellipse at 80% 20%, rgba(99,102,241,0.1) 0%, transparent 50%), radial-gradient(ellipse at 50% 80%, rgba(16,185,129,0.08) 0%, transparent 50%); }
        .grid-pattern { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px; }
        .login-container { position: relative; z-index: 1; width: 100%; max-width: 420px; margin: 1rem; perspective: 1000px; }
        .login-card { background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; transform-style: preserve-3d; animation: cardEnter 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes cardEnter { from { opacity: 0; transform: translateY(24px) scale(0.96) rotateX(-4deg); } to { opacity: 1; transform: translateY(0) scale(1) rotateX(0); } }
        .login-header { padding: 2rem 2rem 0; text-align: center; }
        .logo-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(0,0,0,0.08); overflow: hidden; background: #fff; padding: 6px; border: 1px solid #e5e7eb; }
        .logo-icon img { width: 100%; height: 100%; object-fit: contain; }
        .login-body { padding: 1.5rem 2rem 2rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem; }
        .input-wrapper { position: relative; }
        .input-wrapper .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.875rem; pointer-events: none; transition: color 0.2s; }
        .input-wrapper input { width: 100%; padding: 0.75rem 0.75rem 0.75rem 2.5rem; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 0.9rem; color: #1f2937; background: #f9fafb; transition: all 0.2s; outline: none; }
        .input-wrapper input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .input-wrapper input:focus ~ .icon { color: #3b82f6; }
        .input-wrapper input::placeholder { color: #9ca3af; }
        .input-wrapper.error input { border-color: #ef4444; background: #fef2f2; }
        .error-message { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: none; }
        .error-message.show { display: block; }
        .alert-box { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; border-radius: 10px; font-size: 0.875rem; margin-bottom: 1.25rem; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-error i { color: #ef4444; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-success i { color: #22c55e; }
        .btn-primary { width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; position: relative; overflow: hidden; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(59,130,246,0.3); }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary.loading { pointer-events: none; opacity: 0.85; }
        .btn-primary .spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; }
        .btn-primary.loading .spinner { display: block; }
        .btn-primary.loading .btn-text { opacity: 0.7; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; }
        .checkbox-group input[type="checkbox"] { width: 1rem; height: 1rem; border-radius: 4px; border: 1.5px solid #d1d5db; accent-color: #3b82f6; cursor: pointer; }
        .checkbox-group label { font-size: 0.85rem; color: #6b7280; cursor: pointer; user-select: none; }
        .divider { display: flex; align-items: center; gap: 1rem; margin: 1.25rem 0; color: #9ca3af; font-size: 0.8rem; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }
        .social-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .social-btn { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem; border-radius: 10px; font-size: 0.85rem; font-weight: 500; border: 1.5px solid #e5e7eb; background: #fff; color: #374151; cursor: pointer; transition: all 0.2s; }
        .social-btn:hover { background: #f9fafb; border-color: #d1d5db; }
        .social-btn i { font-size: 1rem; }
        .footer-text { text-align: center; margin-top: 1.25rem; font-size: 0.8rem; color: #9ca3af; }
        .footer-text a { color: #3b82f6; text-decoration: none; font-weight: 500; }
        .footer-text a:hover { text-decoration: underline; }
        .shapes { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
        .shape { position: absolute; border-radius: 50%; opacity: 0.1; }
        .shape-1 { width: 300px; height: 300px; background: #3b82f6; top: -100px; right: -80px; animation: float 20s ease-in-out infinite; }
        .shape-2 { width: 200px; height: 200px; background: #8b5cf6; bottom: -60px; left: -60px; animation: float 25s ease-in-out infinite reverse; }
        .shape-3 { width: 150px; height: 150px; background: #10b981; top: 40%; right: -40px; animation: float 18s ease-in-out infinite 5s; }
        @keyframes float { 0%, 100% { transform: translate(0, 0) rotate(0deg); } 33% { transform: translate(20px, -20px) rotate(5deg); } 66% { transform: translate(-10px, 15px) rotate(-3deg); } }
    </style>
</head>
<body>
    <div class="login-bg"><div class="grid-pattern"></div></div>
    <div class="shapes"><div class="shape shape-1"></div><div class="shape shape-2"></div><div class="shape shape-3"></div></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <img src="{{ asset('images/logo.png') }}" alt="Online Shop">
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-sm text-gray-500 mt-1">Sign in to your admin account</p>
            </div>

            <div class="login-body">
                @if($errors->any())
                    <div class="alert-box alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert-box alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" autocomplete="email" required autofocus>
                            <i class="icon fas fa-envelope"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                            <i class="icon fas fa-lock"></i>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn-primary" id="loginBtn">
                        <span class="spinner"></span>
                        <span class="btn-text"><i class="fas fa-sign-in-alt"></i> Sign In</span>
                    </button>
                </form>

                <div class="footer-text">
                    &copy; {{ date('Y') }} Online Shop. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
        });
    </script>
</body>
</html>
