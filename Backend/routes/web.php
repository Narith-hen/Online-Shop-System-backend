<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Admin Login (Guest only)
Route::get('admin/login', [SettingsController::class, 'showLogin'])->name('admin.login');
Route::post('admin/login', [SettingsController::class, 'login'])->name('admin.login.post');

// Admin Panel (Auth required)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('orders/{order}/verify-payment', [\App\Http\Controllers\Admin\OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/avatar', [SettingsController::class, 'updateAvatar'])->name('settings.avatar');
    Route::delete('settings/avatar', [SettingsController::class, 'removeAvatar'])->name('settings.avatar.remove');
    Route::post('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    Route::post('logout', [SettingsController::class, 'logout'])->name('logout');
});

// // category route:
// Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
// Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
// Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
// Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

// Named login route for exception handler redirect
Route::get('/login', function () {
    return redirect()->away(env('FRONTEND_URL', 'http://localhost:5173') . '/login');
})->name('login');

// SPA catch-all: serve Vue frontend for non-API, non-admin routes
Route::get('/{any?}', function () {
    $frontendPath = public_path('dist/index.html');
    if (file_exists($frontendPath)) {
        return file_get_contents($frontendPath);
    }
    return redirect()->away(env('FRONTEND_URL', 'http://localhost:5173') . '/' . request()->path());
})->where('any', '^(?!api|admin|storage|_debugbar|vendor|images).*$');