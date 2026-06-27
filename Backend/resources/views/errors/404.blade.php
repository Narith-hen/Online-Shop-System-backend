<!-- resources/views/errors/404.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            
            <!-- Animated 404 -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                    404
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 blur-3xl opacity-20 -z-10"></div>
            </div>

            <!-- Icons -->
            <div class="mb-8 flex justify-center gap-4">
                <i class="fas fa-exclamation-triangle text-6xl text-yellow-400 animate-bounce"></i>
                <i class="fas fa-search text-6xl text-gray-400"></i>
            </div>

            <!-- Message -->
            <h1 class="text-4xl font-bold text-white mb-3">
                Page Not Found
            </h1>
            
            <p class="text-lg text-gray-300 mb-2">
                Oops! The page you're looking for has disappeared.
            </p>

            <p class="text-sm text-gray-400 mb-8">
                The URL might be incorrect or the page has been moved to a new location.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ url('/') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-home"></i>
                    Go to Home
                </a>
                
                <button onclick="history.back()" 
                        class="inline-flex items-center justify-center gap-2 bg-gray-700 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition font-medium">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </button>
            </div>

            <!-- Status Code -->
            <div class="bg-gray-800 bg-opacity-50 border border-gray-700 rounded-lg p-4">
                <p class="text-gray-400 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Error Code: <span class="text-red-400 font-mono">404</span>
                </p>
                <p class="text-gray-500 text-xs mt-2">
                    If you think this is a mistake, please contact support.
                </p>
            </div>

            <!-- Footer -->
            <div class="mt-12 pt-8 border-t border-gray-700">
                <p class="text-gray-500 text-sm">
                    &copy; 2024 Online Shop. All rights reserved.
                </p>
            </div>

        </div>
    </div>

</body>
</html>