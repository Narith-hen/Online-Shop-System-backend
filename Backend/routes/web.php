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

    Route::delete('products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
    Route::resource('products', AdminProductController::class);

    Route::delete('categories/bulk-destroy', [\App\Http\Controllers\Admin\CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    Route::delete('orders/bulk-destroy', [\App\Http\Controllers\Admin\OrderController::class, 'bulkDestroy'])->name('orders.bulk-destroy');
    Route::post('orders/bulk-update-status', [\App\Http\Controllers\Admin\OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-update-status');
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('orders/{order}/verify-payment', [\App\Http\Controllers\Admin\OrderController::class, 'verifyPayment'])->name('orders.verify-payment');

    Route::delete('users/bulk-destroy', [\App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::post('users/{user}/toggle-block', [\App\Http\Controllers\Admin\UserController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    Route::delete('notifications/bulk-destroy', [\App\Http\Controllers\Admin\NotificationController::class, 'bulkDestroy'])->name('notifications.bulk-destroy');
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
    Route::get('notifications/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('notifications/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

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