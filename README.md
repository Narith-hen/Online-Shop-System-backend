<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"/>
  <br/><br/>
  <h1>🛍️ Online Shop System</h1>
  <p><strong>A Full-Stack Multi-Vendor E-Commerce Platform</strong></p>
  <p>
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white" alt="PHP 8.2+"/>
    <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12"/>
    <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vue.js&logoColor=white" alt="Vue.js 3"/>
    <img src="https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white" alt="MySQL"/>
    <img src="https://img.shields.io/badge/Tailwind_CSS-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS"/>
    <img src="https://img.shields.io/badge/Sanctum-FF2D20?logo=laravel&logoColor=white" alt="Laravel Sanctum"/>
    <img src="https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=white" alt="Docker"/>
  </p>
</div>

---

## 📋 Table of Contents

1. [Project Overview](#-project-overview)
2. [Features](#-features)
3. [Tech Stack](#-tech-stack)
4. [Prerequisites](#-prerequisites)
5. [Installation](#-installation)
6. [Configuration](#-configuration)
7. [Database Setup](#-database-setup)
8. [Running the Project](#-running-the-project)
9. [Project Structure](#-project-structure)
10. [API Endpoints](#-api-endpoints)
11. [Swagger Documentation](#-swagger-documentation)
12. [Database Schema](#-database-schema)
13. [Real-Time Notifications](#-real-time-notifications)
14. [Contributing](#-contributing)
15. [License](#-license)
16. [Author & Contact](#-author--contact)

---

## 📖 Project Overview

The **Online Shop System** is a full-stack e-commerce platform designed for multi-vendor operations. It provides a complete online shopping experience with a **Laravel 12 backend** powering a RESTful API and a Blade-based admin panel, paired with a **Vue.js 3 frontend** for customers.

The platform supports:

- **Admin Panel** — Comprehensive management dashboard for administrators to manage products, categories, orders, users, and notifications.
- **Customer Website** — A modern Vue.js SPA for browsing products, managing carts and wishlists, placing orders, and tracking purchases.
- **RESTful API** — Fully documented API with public, customer-protected, and admin-protected endpoints, secured via Laravel Sanctum.
- **Real-Time Notifications** — Socket.IO integration for live updates on new products, order status changes, and admin broadcasts.

---

## ✨ Features

### 🔐 Admin Panel (Blade Templates)

| Feature | Description |
|---|---|
| **Dashboard** | Analytics with revenue charts (daily/weekly/monthly), product distribution by category, and recent orders list |
| **Product Management** | Full CRUD with image upload, stock tracking, active/inactive status, and bulk delete |
| **Category Management** | CRUD with active/inactive status, hierarchical support, and bulk delete |
| **Order Management** | View, update status, verify payment proof uploads, and bulk delete |
| **User Management** | View all users, toggle block/unblock accounts, and bulk delete |
| **Notification Management** | Create and broadcast notifications to customers via Socket.IO |
| **Settings** | Profile update, avatar upload/remove, password change, and admin login |

### 🛒 Customer Website (Vue.js 3)

| Feature | Description |
|---|---|
| **Product Browsing** | Browse products with category filtering and search |
| **Product Detail** | Detailed view with reviews and ratings |
| **Shopping Cart** | Add, update quantity, remove items, clear cart |
| **Wishlist** | Add/remove/toggle favorite products |
| **User Authentication** | Register, login, logout, social login (Google & GitHub) |
| **Profile Management** | Update name, email, phone, change password |
| **Order Checkout** | Place orders with shipping address and payment proof upload |
| **Order Tracking** | View order history, individual order details, cancel orders/items |
| **Product Reviews** | Rate and review purchased products |
| **Real-Time Notifications** | Receive live notifications via Socket.IO |

### 🌐 Backend API

| Category | Endpoints |
|---|---|
| **Public APIs** | Products, categories, reviews (read), payment methods |
| **Customer APIs** | Cart, wishlist, orders, order items (cancel/return/reorder), checkout, notifications, profile |
| **Admin APIs** | Dashboard stats, earnings, products/categories/orders CRUD, settings |

---

## 🛠️ Tech Stack

### Backend

| Technology | Version | Purpose |
|---|---|---|
| **PHP** | ^8.2 | Server-side scripting language |
| **Laravel** | ^12.0 | PHP web application framework |
| **MySQL / SQLite** | — | Database engine |
| **Laravel Sanctum** | ^4.3 | API token authentication |
| **Laravel Socialite** | ^5.28 | Social login (Google, GitHub) |
| **Laravel UI** | * | Admin scaffolding |
| **L5-Swagger** | ^11.1 | OpenAPI/Swagger documentation generation |
| **Barryvdh/Dompdf** | * | PDF receipt generation |
| **Socket.IO (PHP client)** | — | Real-time event broadcasting |
| **Laravel Sail** | ^1.41 | Docker development environment |

### Frontend

| Technology | Version | Purpose |
|---|---|---|
| **Vue.js** | ^3.5 | Progressive JavaScript framework |
| **Vite** | ^8.0 | Build tool and dev server |
| **Tailwind CSS** | ^4.3 | Utility-first CSS framework |
| **Pinia** | ^3.0 | State management |
| **Vue Router** | ^5.1 | Client-side routing |
| **Socket.IO Client** | ^4.8 | Real-time WebSocket communication |
| **Font Awesome** | ^7.2 | Icon library |
| **Prettier** | ^3.8 | Code formatting |
| **Concurrently** | ^10.0 | Run multiple dev scripts |

---

## 📋 Prerequisites

Before installation, ensure you have the following installed:

- **PHP** >= 8.2 ([download](https://www.php.net/downloads))
- **Composer** >= 2.x ([download](https://getcomposer.org/download/))
- **Node.js** >= 22.18 ([download](https://nodejs.org/))
- **npm** >= 10.x (ships with Node.js)
- **MySQL** >= 8.0 or **SQLite** (for development)
- **Git** ([download](https://git-scm.com/downloads))
- **Docker** (optional, for Laravel Sail)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/online-shop-system.git
cd online-shop-system
```

The project has two main directories:

```
online-shop-system/
├── Backend/          # Laravel 12 application
└── Frontend/         # Vue.js 3 application
```

### 2. Install Backend Dependencies

```bash
cd Backend
composer install
```

### 3. Install Frontend Dependencies

```bash
cd ../Frontend
npm install
```

---

## ⚙️ Configuration

### Backend (.env)

Copy the environment file and generate an application key:

```bash
cd Backend
cp .env.example .env
php artisan key:generate
```

Update the `.env` file with your database and application settings:

```env
APP_NAME=OnlineShop
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=online_shop
DB_USERNAME=root
DB_PASSWORD=

# Laravel Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000

# Frontend URL (for OAuth callbacks)
FRONTEND_URL=http://localhost:5173

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

# Social Login (Google)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Social Login (GitHub)
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI=http://localhost:8000/api/auth/github/callback

# Socket.IO Server URL
SOCKET_SERVER_URL=http://127.0.0.1:3001
```

### Frontend (.env)

Create or update `Frontend/.env`:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000
VITE_SOCKET_URL=http://localhost:3001
```

---

## 🗄️ Database Setup

### 1. Create the Database

```sql
CREATE DATABASE online_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Run Migrations

```bash
cd Backend
php artisan migrate
```

This will create the following tables:

| Table | Description |
|---|---|
| `users` | User accounts (admin & customer) with social auth fields |
| `roles` | Role definitions (admin, customer) |
| `role_user` | Pivot table for user-role associations |
| `categories` | Product categories with hierarchical support |
| `products` | Products with pricing, stock, and active status |
| `cart_items` | Customer shopping cart items |
| `wishlist_items` | Customer wishlist items |
| `orders` | Customer orders with shipping and payment info |
| `order_items` | Individual line items within orders |
| `reviews` | Product reviews and ratings |
| `notifications` | System and admin notifications |
| `notification_reads` | Pivot table tracking read status |
| `personal_access_tokens` | Sanctum API tokens |
| `cache` | Cache store (database driver) |
| `jobs` | Queue jobs table |
| `sessions` | Session storage |

### 3. Seed Initial Data (Optional)

```bash
php artisan db:seed
```

> **Note:** The seeder creates initial roles (`admin`, `customer`) and an admin user. Check `database/seeders/` for details.

---

## 🏃 Running the Project

### Development Environment

#### Terminal 1 — Backend API Server

```bash
cd Backend
php artisan serve
```

The API will be available at `http://localhost:8000`.

#### Terminal 2 — Queue Worker (for notifications)

```bash
cd Backend
php artisan queue:listen --tries=1 --timeout=0
```

#### Terminal 3 — Frontend Dev Server

```bash
cd Frontend
npm run dev
```

The Vite dev server will start at `http://localhost:5173`.

#### Terminal 4 — Socket.IO Server (real-time)

```bash
cd Frontend
node server/socket.js
```

The Socket.IO server runs on `http://localhost:3001`.

### One-Command Dev Startup (Backend)

```bash
cd Backend
composer run dev
```

This runs the API server, queue worker, logs, and Vite dev server concurrently.

### Production Build

```bash
# Build frontend assets
cd Frontend
npm run build

# Backend is ready via PHP-FPM + Nginx
```

---

## 📁 Project Structure

### Backend Structure

```
Backend/
├── app/
│   ├── Annotations/
│   │   └── ApiAnnotations.php          # OpenAPI/Swagger schema definitions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── NotificationController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── CartController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── NotificationController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── OrderItemController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── SocialAuthController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── WishlistController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php      # Admin role gate
│   │   │   ├── CheckBlocked.php         # Blocked user check
│   │   │   └── CustomerMiddleware.php   # Customer role gate
│   │   ├── Requests/                    # Form request validation
│   │   └── Resources/                   # API resource transformers
│   ├── Mail/                            # Mailables
│   ├── Models/
│   │   ├── CartItem.php
│   │   ├── Category.php
│   │   ├── Notification.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Product.php
│   │   ├── Review.php
│   │   ├── Role.php
│   │   ├── User.php
│   │   └── WishlistItem.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       └── SocketService.php            # Socket.IO push service
├── bootstrap/
├── config/
│   ├── l5-swagger.php                   # Swagger UI configuration
│   ├── sanctum.php                      # Sanctum config
│   └── ...
├── database/
│   ├── factories/
│   ├── migrations/                      # 26 migration files
│   └── seeders/
├── public/
├── resources/
│   └── views/
│       ├── admin/                       # Blade admin panel views
│       │   ├── categories/
│       │   ├── notifications/
│       │   ├── orders/
│       │   ├── products/
│       │   ├── settings/
│       │   ├── users/
│       │   ├── dashboard.blade.php
│       │   └── login.blade.php
│       ├── emails/
│       ├── errors/
│       ├── vendor/
│       └── MainLayout.blade.php         # Admin layout with sidebar
├── routes/
│   ├── api.php                          # API routes (public, customer, admin)
│   └── web.php                          # Web routes (admin panel, SPA catch-all)
├── storage/
├── tests/
├── composer.json
├── vite.config.js
└── phpunit.xml
```

### Frontend Structure

```
Frontend/
├── public/
│   ├── images/
│   │   └── logo.png
│   └── favicon.ico
├── server/
│   └── socket.js                        # Socket.IO server for real-time
├── src/
│   ├── assets/
│   │   ├── images/
│   │   │   └── welcome.png
│   │   └── styles/
│   │       └── main.css                 # Global styles
│   ├── components/
│   │   ├── Auth/
│   │   │   ├── AuthCallback.vue         # OAuth callback handler
│   │   │   ├── Login.vue
│   │   │   └── Register.vue
│   │   ├── layouts/
│   │   │   └── MainLayout.vue           # Authenticated layout
│   │   ├── Navbar.vue
│   │   ├── SuccessModal.vue
│   │   └── Toast.vue
│   ├── composables/
│   │   └── useSocket.js                 # Socket.IO composable
│   ├── router/
│   │   └── index.js                     # Vue Router with auth guards
│   ├── services/
│   │   └── api.js                       # HTTP client (fetch-based)
│   ├── stores/
│   │   └── auth.js                      # Pinia auth store
│   └── views/
│       ├── AccountBlocked.vue
│       ├── NotFound.vue
│       ├── WelcomePage.vue
│       └── customer/
│           ├── AboutPage.vue
│           ├── CartPage.vue
│           ├── CategoryPage.vue
│           ├── CheckoutPage.vue
│           ├── ContactPage.vue
│           ├── HomePage.vue
│           ├── OrderCard.vue
│           ├── ProductDetail.vue
│           ├── ProductPage.vue
│           ├── ReceiptPage.vue
│           ├── UserProfile.vue
│           └── WishlistPage.vue
├── .env                                 # Environment variables
├── index.html
├── package.json
├── vite.config.js
├── tailwind.config.js
└── postcss.config.js
```

---

## 🌐 API Endpoints

### Public Endpoints (No Authentication)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/register` | Register a new account |
| `POST` | `/api/login` | Login and receive a Sanctum token |
| `GET` | `/api/auth/{provider}/redirect` | Redirect to social login provider |
| `GET` | `/api/auth/{provider}/callback` | Handle social login callback |
| `GET` | `/api/products` | List all active products |
| `GET` | `/api/products/{id}` | Get a single product with details |
| `GET` | `/api/categories` | List all active categories |
| `GET` | `/api/categories/{id}` | Get a single category with products |
| `GET` | `/api/products/{product}/reviews` | Get reviews for a product |
| `GET` | `/api/payment-methods` | List available payment methods |

### Authenticated Endpoints (Any Role)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/profile` | Get authenticated user profile |
| `POST` | `/api/profile` | Update profile (name, email, phone) |
| `POST` | `/api/change-password` | Change password |
| `POST` | `/api/logout` | Logout and revoke token |

### Customer Endpoints (Role: `customer`)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/orders` | List customer orders |
| `GET` | `/api/orders/{id}` | Get order details with items |
| `POST` | `/api/orders/{id}/cancel` | Cancel a pending order |
| `GET` | `/api/cart` | Get cart contents with subtotal |
| `POST` | `/api/cart` | Add item to cart |
| `PUT` | `/api/cart/{id}` | Update cart item quantity |
| `DELETE` | `/api/cart/{id}` | Remove item from cart |
| `POST` | `/api/cart/clear` | Clear entire cart |
| `GET` | `/api/wishlist` | List wishlist items |
| `POST` | `/api/wishlist` | Add item to wishlist |
| `POST` | `/api/wishlist/toggle` | Toggle wishlist item |
| `DELETE` | `/api/wishlist/{id}` | Remove from wishlist |
| `POST` | `/api/orders/{order}/items/{item}/cancel` | Cancel an order item |
| `POST` | `/api/orders/{order}/items/{item}/return` | Request return |
| `POST` | `/api/orders/{order}/items/{item}/reorder` | Reorder an item |
| `GET` | `/api/notifications` | List notifications |
| `POST` | `/api/notifications/{id}/read` | Mark notification as read |
| `POST` | `/api/notifications/read-all` | Mark all as read |
| `POST` | `/api/notifications/toggle` | Toggle notification subscription |
| `POST` | `/api/checkout` | Place an order |
| `POST` | `/api/orders/{order}/payment-proof` | Upload payment proof image |
| `POST` | `/api/products/{product}/reviews` | Submit a product review |

### Admin Endpoints (Role: `admin`, prefix: `/api/admin`)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/admin/categories` | List all categories |
| `POST` | `/api/admin/categories` | Create a category |
| `GET` | `/api/admin/categories/{id}` | Get category details |
| `PUT` | `/api/admin/categories/{id}` | Update a category |
| `DELETE` | `/api/admin/categories/{id}` | Delete a category |
| `POST` | `/api/admin/products` | Create a product |
| `PUT` | `/api/admin/products/{id}` | Update a product |
| `DELETE` | `/api/admin/products/{id}` | Delete a product |
| `GET` | `/api/admin/orders/stats` | Dashboard order statistics |
| `GET` | `/api/admin/orders/earnings` | Earnings chart data |
| `GET` | `/api/admin/products/stats` | Product statistics |
| `GET` | `/api/admin/settings` | Get admin settings |
| `POST` | `/api/admin/settings` | Update admin settings |

### Admin Web Routes

| Method | Route | Description |
|---|---|---|
| `GET` | `/admin/login` | Admin login page |
| `POST` | `/admin/login` | Admin login action |
| `GET` | `/admin` | Dashboard |
| Resource | `/admin/products` | Product CRUD |
| Resource | `/admin/categories` | Category CRUD |
| Resource | `/admin/orders` | Order management |
| Resource | `/admin/users` | User management |
| Resource | `/admin/notifications` | Notification management |
| `GET` | `/admin/settings` | Settings page |
| `POST` | `/admin/settings` | Update settings |
| `POST` | `/admin/logout` | Admin logout |
| `DELETE` | `/admin/*/bulk-destroy` | Bulk delete (products, categories, orders, users, notifications) |

---

## 📖 Swagger Documentation

The API is fully documented using **OpenAPI 3.0** specification via the `l5-swagger` package.

### Access Swagger UI

Once the backend server is running, visit:

```
http://localhost:8000/api/documentation
```

### Generate Documentation

```bash
php artisan l5-swagger:generate
```

### Swagger Configuration

Swagger settings are found in `Backend/config/l5-swagger.php`. Key configuration options:

```php
// Security scheme (Sanctum Bearer token)
'sanctum' => [
    'type' => 'apiKey',
    'description' => 'Enter your Bearer token obtained from login/register.',
    'name' => 'Authorization',
    'in' => 'header',
],

// API metadata (defined in Annotations/ApiAnnotations.php)
#[OA\Info(
    version: '1.0.0',
    title: 'Online Shop System API',
    description: 'Full API documentation',
)]
```

### Schema Definitions

The Swagger annotations define comprehensive schemas for all data models:

- `User`, `Role`, `Product`, `Category`
- `CartItem`, `WishlistItem`, `Order`, `OrderItem`
- `Review`, `Notification`, `PaymentMethod`
- Request DTOs: `RegisterRequest`, `LoginRequest`, `CheckoutRequest`, `AddToCartRequest`, etc.
- Response wrappers: `SuccessResponse`, `ErrorResponse`, `ValidationErrorResponse`, `PaginationMeta`

---

## 🗺️ Database Schema

### Entity Relationship Overview

```
┌──────────┐       ┌──────────────┐       ┌──────────────┐
│   Role   │───────│     User     │───────│   CartItem   │
└──────────┘       │              │       └──────────────┘
                   │  (polymorphic│
                   │   role via   │       ┌──────────────────┐
                   │   role_id)   │───────│  WishlistItem    │
                   └──────────────┘       └──────────────────┘
                          │
                          │ 1:N               ┌──────────┐
                          ├──────────────────│  Review  │
                          │                  └──────────┘
                          │ 1:N
                    ┌─────┴─────┐
                    │   Order   │──────┐
                    └───────────┘      │ 1:N
                                       │
                                 ┌─────┴──────────┐
                                 │   OrderItem     │──────┐
                                 └─────────────────┘     │
                                                          │ N:1
                                                  ┌───────┴──────┐
                                                  │   Product     │──┐
                                                  └──────────────┘  │
                                                          │         │ 1:N
                                                          │ 1:N    │
                                                    ┌─────┴──┐     │
                                                    │Category│─────┘
                                                    └────────┘

┌──────────────┐       ┌──────────────────────┐
│ Notification │───────│  notification_reads   │───────User
└──────────────┘       └──────────────────────┘
```

### Key Table Details

#### `users`
| Column | Type | Description |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `code` | string (unique) | Auto-generated user code (SPU001) |
| `name` | string | Full name |
| `email` | string (unique) | Email address |
| `password` | string (hashed) | Password |
| `role_id` | bigint (FK) | References `roles.id` |
| `avatar` | string (nullable) | Avatar file path |
| `phone` | string (nullable) | Phone number |
| `is_blocked` | boolean | Account blocked status |
| `provider` | string (nullable) | Social auth provider (google, github) |
| `provider_id` | string (nullable) | Social auth provider ID |
| `provider_avatar` | string (nullable) | Social auth avatar URL |
| `notifications_enabled` | boolean | Notification subscription |

#### `products`
| Column | Type | Description |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `name` | string | Product name |
| `image` | string (nullable) | Product image path/URL |
| `price` | decimal/float | Product price |
| `stock` | integer | Available stock |
| `category_id` | bigint (FK) | References `categories.id` |
| `is_active` | boolean | Product visibility |

#### `orders`
| Column | Type | Description |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `user_id` | bigint (FK) | References `users.id` |
| `total` | decimal/float | Order total |
| `status` | enum | `pending`, `completed`, `cancelled` |
| `payment_method` | string (nullable) | Payment method code |
| `payment_status` | string (nullable) | `pending`, `verified` |
| `payment_proof` | string (nullable) | Payment proof image path |
| `shipping_name` | string | Recipient name |
| `shipping_phone` | string | Recipient phone |
| `shipping_address` | text | Shipping address |
| `shipping_city` | string | City |
| `shipping_zip` | string | ZIP/Postal code |

---

## 🔔 Real-Time Notifications

The system uses **Socket.IO** for real-time push notifications.

### Architecture

```
Backend (Laravel)                  Socket.IO Server (Node.js)           Frontend (Vue.js)
     │                                     │                                │
     │── POST /push (channel, data) ──────▶│                                │
     │                                     │── broadcast ─────────────────▶│
     │                                     │   (WebSocket)                  │
     │◀── HTTP 200 ────────────────────────│                                │
```

### How It Works

1. **Backend triggers** an event (e.g., new product created)
2. **SocketService** makes an HTTP POST to the Socket.IO server
3. **Socket.IO server** (`server/socket.js`) broadcasts to connected clients
4. **Frontend** receives the event via the `useSocket` composable

### Events

| Event | Trigger | Data |
|---|---|---|
| `notification` | New product, admin broadcast | `{id, title, message, type, link, created_at}` |
| `cart-update` | Cart modification | `{}` |

---

## 🤝 Contributing

Contributions are welcome and appreciated! Here's how you can help:

1. **Fork** the repository
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Commit your changes**: `git commit -m 'Add amazing feature'`
4. **Push to the branch**: `git push origin feature/amazing-feature`
5. **Open a Pull Request**

### Guidelines

- Follow the existing code style (Laravel PSR-4, Vue 3 Composition API)
- Write meaningful commit messages
- Update Swagger annotations when changing API endpoints
- Add/update tests for new features
- Run `composer run test` and `npm run format` before submitting

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👤 Author & Contact

**Your Name**
- GitHub: [@narith-hen](https://github.com/narith-hen)
- Email: narithhen2026@gmail.com

---

<div align="center">
  <sub>Built with ❤️ using Laravel 12, Vue.js 3, and Tailwind CSS</sub>
  <br/>
  <sub>&copy; 2026 Online Shop System. All rights reserved.</sub>
</div>
