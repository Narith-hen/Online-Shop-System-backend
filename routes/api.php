<?php

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ========================================
// PUBLIC ROUTES (No Authentication)
// ========================================

// Authentication Routes
Route::post('/register', [AuthController::class, 'register'])
    ->name('api.register');
Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

// Social Login Routes
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Api\SocialAuthController::class, 'redirectToProvider'])
    ->name('api.auth.social.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Api\SocialAuthController::class, 'handleProviderCallback'])
    ->name('api.auth.social.callback');

// Public Product Routes (Customers can view products)
Route::get('/products', [ProductController::class, 'index'])
    ->name('api.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('api.products.show');

// Public Category Routes (Customers can view categories with products)
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->name('api.categories.show');

// Public Review Routes (Anyone can view product reviews)
Route::get('/products/{product}/reviews', [ReviewController::class, 'index'])
    ->name('api.products.reviews.index');

// Public Payment Methods (QR info)
Route::get('/payment-methods', [PaymentController::class, 'methods'])
    ->name('api.payment-methods');


// ========================================
// SHARED AUTH ROUTES (Any Authenticated User)
// ========================================

Route::middleware('auth:sanctum')->group(function () {

    // Profile & Logout (available to both admin and customer)
    Route::get('/profile', [AuthController::class, 'profile'])
        ->name('api.profile.show');
    Route::post('/profile', [AuthController::class, 'updateProfile'])
        ->name('api.profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');

});


// ========================================
// CUSTOMER-ONLY ROUTES (Auth + Customer Role Required)
// ========================================

Route::middleware(['auth:sanctum', 'customer'])->group(function () {

    // Customer Orders
    Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index'])
        ->name('api.orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Api\OrderController::class, 'show'])
        ->name('api.orders.show');
    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Api\OrderController::class, 'cancel'])
        ->name('api.orders.cancel');

    // Shopping Cart
    Route::get('/cart', [CartController::class, 'index'])
        ->name('api.cart.index');
    Route::post('/cart', [CartController::class, 'store'])
        ->name('api.cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])
        ->name('api.cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])
        ->name('api.cart.destroy');
    Route::post('/cart/clear', [CartController::class, 'clear'])
        ->name('api.cart.clear');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])
        ->name('api.wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])
        ->name('api.wishlist.store');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])
        ->name('api.wishlist.toggle');
    Route::delete('/wishlist/{wishlistItem}', [WishlistController::class, 'destroy'])
        ->name('api.wishlist.destroy');

    // Order Item Actions
    Route::post('/orders/{order}/items/{item}/cancel', [\App\Http\Controllers\Api\OrderItemController::class, 'cancel'])
        ->name('api.orders.items.cancel');
    Route::post('/orders/{order}/items/{item}/return', [\App\Http\Controllers\Api\OrderItemController::class, 'returnItem'])
        ->name('api.orders.items.return');
    Route::post('/orders/{order}/items/{item}/reorder', [\App\Http\Controllers\Api\OrderItemController::class, 'reorder'])
        ->name('api.orders.items.reorder');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index'])
        ->name('api.notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead'])
        ->name('api.notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead'])
        ->name('api.notifications.read-all');
    Route::post('/notifications/toggle', [\App\Http\Controllers\Api\NotificationController::class, 'toggleSubscription'])
        ->name('api.notifications.toggle');

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'checkout'])
        ->name('api.checkout');
    Route::post('/orders/{order}/payment-proof', [CheckoutController::class, 'uploadProof'])
        ->name('api.orders.payment-proof');

    // Reviews (submit)
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])
        ->name('api.products.reviews.store');

});


// ========================================
// ADMIN ROUTES (Auth + Admin Role Required)
// ========================================

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // Admin Categories Management
    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('api.admin.categories.store');
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('api.admin.categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->name('api.admin.categories.show');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->name('api.admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('api.admin.categories.destroy');

    // Admin Products Management
    Route::post('/products', [ProductController::class, 'store'])
        ->name('api.admin.products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('api.admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('api.admin.products.destroy');


    // Settings page
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('admin.settings');
    
    // Update settings (fullname, email, password)
    Route::post('/settings', [SettingsController::class, 'update'])
        ->name('admin.settings.update');
});