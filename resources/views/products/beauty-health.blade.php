<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Beauty & Health | warung.</title>
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
@include('products.baby-category.back-button')
<body class="relative">
    <main class="container mx-auto px-6 py-12 relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12 animate-fadeInUp transform -rotate-3 origin-left">
                <h1 class="text-6xl font-bold text-blue-900 mb-4 tracking-tight">
                    beauty &
                    <br />
                    <span class="text-blue-600">health</span>
                </h1>
                <p class="text-xl text-blue-600 max-w-2xl pl-4 border-l-4 border-blue-600">
                    Discover our premium selection of beauty and wellness products
                </p>
            </div>

            <div class="flex gap-4 mb-8 overflow-x-auto pb-4 animate-fadeInUp delay-100">
                <button class="category-pill px-6 py-2 rounded-full bg-blue-500 text-white font-medium">All Products</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Skincare</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Makeup</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Supplements</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Wellness</button>
            </div>

            @include('products.beauty-category.product-components')
  </main>
    <footer class="border-t border-blue-100 backdrop-blur-sm bg-white/80 mt-12">
        <div class="container mx-auto px-6 py-8">
            <p class="text-center text-blue-900 text-sm">
                2025 warung. all rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
