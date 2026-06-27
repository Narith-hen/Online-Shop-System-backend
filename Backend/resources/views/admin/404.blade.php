<!-- resources/views/admin/404.blade.php -->

@extends('MainLayout')

@section('title', '404 - Page Not Found')
@section('page_title', '404 Error')
@section('page_subtitle', 'The page you are looking for does not exist')

@section('content')

<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center max-w-md mx-auto">
        
        <!-- 404 Icon -->
        <div class="mb-8">
            <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4">
                404
            </div>
            <i class="fas fa-search text-6xl text-gray-400 mb-6 block"></i>
        </div>

        <!-- Error Message -->
        <h1 class="text-4xl font-bold text-gray-900 mb-3">
            Page Not Found
        </h1>
        
        <p class="text-lg text-gray-600 mb-2">
            Oops! The page you're looking for doesn't exist or has been moved.
        </p>

        <p class="text-sm text-gray-500 mb-8">
            The URL you entered might be incorrect or this page has been removed.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                <i class="fas fa-home"></i>
                Go to Dashboard
            </a>
            
            <button onclick="history.back()" 
                    class="inline-flex items-center justify-center gap-2 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-medium">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </button>
        </div>

        <!-- Helpful Links -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-4">Quick Links:</p>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition text-sm font-medium text-gray-700">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" 
                   class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition text-sm font-medium text-gray-700">
                    <i class="fas fa-box mr-2"></i>Products
                </a>
                <a href="{{ route('admin.categories.index') }}" 
                   class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition text-sm font-medium text-gray-700">
                    <i class="fas fa-tag mr-2"></i>Categories
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition text-sm font-medium text-gray-700">
                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                </a>
            </div>
        </div>

    </div>
</div>

@endsection