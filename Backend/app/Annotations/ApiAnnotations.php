<?php

namespace App\Annotations;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Online Shop System API',
    description: 'API documentation for the Online Shop System. Supports public, customer, and admin endpoints.',
    contact: new OA\Contact(email: 'support@onlineshop.com'),
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Enter your Bearer token obtained from login/register'
)]
#[OA\Tag(name: 'Authentication', description: 'Register, login, logout, profile management')]
#[OA\Tag(name: 'Products', description: 'Public product listing and details')]
#[OA\Tag(name: 'Categories', description: 'Public category listing and details')]
#[OA\Tag(name: 'Cart', description: 'Customer shopping cart management')]
#[OA\Tag(name: 'Wishlist', description: 'Customer wishlist management')]
#[OA\Tag(name: 'Orders', description: 'Customer order history and management')]
#[OA\Tag(name: 'Order Items', description: 'Cancel, return, reorder individual items')]
#[OA\Tag(name: 'Checkout', description: 'Place orders and upload payment proof')]
#[OA\Tag(name: 'Reviews', description: 'Product reviews')]
#[OA\Tag(name: 'Notifications', description: 'Customer notification management')]
#[OA\Tag(name: 'Payment Methods', description: 'Public payment method listing')]
#[OA\Tag(name: 'Admin', description: 'Admin-only management endpoints')]
#[OA\Tag(name: 'Social Auth', description: 'Social login via Google/GitHub')]

class ApiAnnotations
{
}

// ==================== Shared Response Schemas ====================

#[OA\Schema(
    schema: 'SuccessResponse',
    description: 'Generic success response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Operation successful'),
    ]
)]
class SuccessResponse {}

#[OA\Schema(
    schema: 'ErrorResponse',
    description: 'Generic error response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Error message'),
    ]
)]
class ErrorResponse {}

#[OA\Schema(
    schema: 'ValidationErrorResponse',
    description: 'Validation error response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Validation Error'),
        new OA\Property(property: 'errors', type: 'object', example: '{"field": ["Error message"]}'),
    ]
)]
class ValidationErrorResponse {}

#[OA\Schema(
    schema: 'PaginationMeta',
    description: 'Pagination metadata',
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 5),
        new OA\Property(property: 'per_page', type: 'integer', example: 20),
        new OA\Property(property: 'total', type: 'integer', example: 100),
    ]
)]
class PaginationMeta {}

// ==================== Request Bodies ====================

#[OA\Schema(
    schema: 'RegisterRequest',
    required: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new OA\Property(property: 'name', type: 'string', description: 'Full name'),
        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Email address'),
        new OA\Property(property: 'password', type: 'string', format: 'password', description: 'Password (min 8 characters)'),
        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', description: 'Must match password'),
    ]
)]
class RegisterRequest {}

#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
    ]
)]
class LoginRequest {}

#[OA\Schema(
    schema: 'UpdateProfileRequest',
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'phone', type: 'string'),
    ]
)]
class UpdateProfileRequest {}

#[OA\Schema(
    schema: 'ChangePasswordRequest',
    required: ['current_password', 'password', 'password_confirmation'],
    properties: [
        new OA\Property(property: 'current_password', type: 'string', format: 'password'),
        new OA\Property(property: 'password', type: 'string', format: 'password', description: 'New password (min 8 characters)'),
        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
    ]
)]
class ChangePasswordRequest {}

#[OA\Schema(
    schema: 'AuthResponse',
    description: 'Login/Register success response',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Login Successfully'),
        new OA\Property(property: 'token', type: 'string', example: '1|abc123def456...'),
        new OA\Property(property: 'role', type: 'string', example: 'customer'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]
)]
class AuthResponse {}

#[OA\Schema(
    schema: 'User',
    description: 'User model',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true),
        new OA\Property(property: 'role', ref: '#/components/schemas/Role'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class User {}

#[OA\Schema(
    schema: 'Role',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string', example: 'customer'),
    ]
)]
class Role {}

#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Wireless Headphones'),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 49.99),
        new OA\Property(property: 'stock', type: 'integer', example: 50),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Product {}

#[OA\Schema(
    schema: 'Category',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'parent_id', type: 'integer', nullable: true),
        new OA\Property(property: 'products_count', type: 'integer', example: 5),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Category {}

#[OA\Schema(
    schema: 'CartItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'product', ref: '#/components/schemas/Product'),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'product_id', type: 'integer'),
    ]
)]
class CartItem {}

#[OA\Schema(
    schema: 'CartResponse',
    properties: [
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/CartItem')),
        new OA\Property(property: 'count', type: 'integer', example: 3),
        new OA\Property(property: 'subtotal', type: 'number', format: 'float', example: 149.97),
    ]
)]
class CartResponse {}

#[OA\Schema(
    schema: 'AddToCartRequest',
    required: ['product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', example: 1, minimum: 1),
    ]
)]
class AddToCartRequest {}

#[OA\Schema(
    schema: 'WishlistItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'product', ref: '#/components/schemas/Product'),
        new OA\Property(property: 'product_id', type: 'integer'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class WishlistItem {}

#[OA\Schema(
    schema: 'ToggleWishlistRequest',
    required: ['product_id'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
    ]
)]
class ToggleWishlistRequest {}

#[OA\Schema(
    schema: 'Order',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer'),
        new OA\Property(property: 'status', type: 'string', example: 'pending'),
        new OA\Property(property: 'total', type: 'number', format: 'float', example: 149.97),
        new OA\Property(property: 'payment_method', type: 'string', nullable: true),
        new OA\Property(property: 'payment_status', type: 'string', nullable: true),
        new OA\Property(property: 'payment_proof', type: 'string', nullable: true),
        new OA\Property(property: 'customer_name', type: 'string', nullable: true),
        new OA\Property(property: 'customer_email', type: 'string', nullable: true),
        new OA\Property(property: 'shipping_name', type: 'string', nullable: true),
        new OA\Property(property: 'shipping_address', type: 'string', nullable: true),
        new OA\Property(property: 'shipping_city', type: 'string', nullable: true),
        new OA\Property(property: 'shipping_zip', type: 'string', nullable: true),
        new OA\Property(property: 'shipping_phone', type: 'string', nullable: true),
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItem')),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Order {}

#[OA\Schema(
    schema: 'OrderItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'product_id', type: 'integer'),
        new OA\Property(property: 'product', ref: '#/components/schemas/Product'),
        new OA\Property(property: 'quantity', type: 'integer', example: 1),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 49.99),
        new OA\Property(property: 'status', type: 'string', example: 'pending'),
    ]
)]
class OrderItem {}

#[OA\Schema(
    schema: 'Review',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'rating', type: 'integer', example: 5, minimum: 1, maximum: 5),
        new OA\Property(property: 'comment', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Review {}

#[OA\Schema(
    schema: 'SubmitReviewRequest',
    required: ['rating'],
    properties: [
        new OA\Property(property: 'rating', type: 'integer', example: 5, minimum: 1, maximum: 5),
        new OA\Property(property: 'comment', type: 'string', nullable: true),
    ]
)]
class SubmitReviewRequest {}

#[OA\Schema(
    schema: 'Notification',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'message', type: 'string', nullable: true),
        new OA\Property(property: 'type', type: 'string', example: 'news'),
        new OA\Property(property: 'read', type: 'boolean', example: false),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Notification {}

#[OA\Schema(
    schema: 'PaymentMethod',
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'aba'),
        new OA\Property(property: 'name', type: 'string', example: 'ABA Bank'),
        new OA\Property(property: 'qr_url', type: 'string', nullable: true),
        new OA\Property(property: 'instructions', type: 'string', nullable: true),
    ]
)]
class PaymentMethod {}

#[OA\Schema(
    schema: 'CheckoutRequest',
    properties: [
        new OA\Property(property: 'payment_method', type: 'string', example: 'aba'),
        new OA\Property(property: 'shipping_name', type: 'string'),
        new OA\Property(property: 'shipping_phone', type: 'string'),
        new OA\Property(property: 'shipping_address', type: 'string'),
        new OA\Property(property: 'shipping_city', type: 'string'),
        new OA\Property(property: 'shipping_zip', type: 'string'),
    ]
)]
class CheckoutRequest {}

#[OA\Schema(
    schema: 'StoreCategoryRequest',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'parent_id', type: 'integer', nullable: true),
    ]
)]
class StoreCategoryRequest {}

#[OA\Schema(
    schema: 'UpdateCategoryRequest',
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'parent_id', type: 'integer', nullable: true),
    ]
)]
class UpdateCategoryRequest {}

#[OA\Schema(
    schema: 'StoreProductRequest',
    required: ['name', 'price', 'stock', 'category_id'],
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'price', type: 'number', format: 'float'),
        new OA\Property(property: 'stock', type: 'integer'),
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'image', type: 'string', format: 'binary', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', nullable: true),
    ]
)]
class StoreProductRequest {}

#[OA\Schema(
    schema: 'UpdateProductRequest',
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'price', type: 'number', format: 'float'),
        new OA\Property(property: 'stock', type: 'integer'),
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'image', type: 'string', format: 'binary', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', nullable: true),
    ]
)]
class UpdateProductRequest {}
