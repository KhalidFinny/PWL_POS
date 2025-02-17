<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Home Care | warung.</title>
    <style>
        body {
            font-family: 'Urbanist', sans-serif;
            background: linear-gradient(135deg, rgba(219, 234, 254, 0.3) 0%, rgba(255, 255, 255, 1) 100%);
            min-height: 100vh;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease forwards;
        }

        .category-pill {
            transition: all 0.3s ease;
        }

        .category-pill:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="relative">
    <!-- Decorative Blobs -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-100 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-200 rounded-full opacity-30 blur-3xl"></div>
    </div>
    @include('products.baby-category.back-button')
    <main class="container mx-auto px-6 py-12 relative z-10">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-12 animate-fadeInUp transform -rotate-3 origin-left">
                <h1 class="text-6xl font-bold text-blue-900 mb-4 tracking-tight">
                    home
                    <br />
                    <span class="text-blue-600">care</span>
                </h1>
                <p class="text-xl text-blue-600 max-w-2xl pl-4 border-l-4 border-blue-600">
                    Discover our comprehensive collection of home cleaning and care products
                </p>
            </div>
            <div class="flex gap-4 mb-8 overflow-x-auto pb-4 animate-fadeInUp delay-100">
                <button class="category-pill px-6 py-2 rounded-full bg-blue-500 text-white font-medium">All Products</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Cleaning</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Laundry</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Disinfectant</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Bathroom</button>
            </div>
            @include('products.home.home-care')
        </div>
    </main>

    <footer class="border-t border-blue-100 backdrop-blur-sm bg-white/80 mt-12">
        <div class="container mx-auto px-6 py-8">
            <p class="text-center text-blue-900 text-sm">
                © 2025 warung. all rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
