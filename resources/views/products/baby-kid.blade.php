<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Baby & Kid Products</title>
    <style>
        body {
            font-family: 'Urbanist', sans-serif;
            background: linear-gradient(135deg, rgba(219, 234, 254, 0.3) 0%, rgba(255, 255, 255, 1) 100%);
            min-height: 100vh;
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #3B82F6;
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: #3B82F6;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .product-card:hover {
            border-color: #3B82F6;
            transform: translateY(-2px);
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

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
    </style>
</head>
<body>
    @include('products.baby-category.back-button')

    <main class="container mx-auto px-6 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12 animate-fadeInUp transform -rotate-3 origin-left">
                <h1 class="text-6xl font-bold text-blue-900 mb-4 tracking-tight">
                    baby & kid
                    <br />
                    <span class="text-blue-600">products</span>
                </h1>
                <p class="text-xl text-blue-600 max-w-2xl pl-4 border-l-4 border-blue-600">
                    discover our collection of quality products for your little ones
                </p>
            </div>
            <div class="flex gap-4 mb-8 overflow-x-auto pb-4 animate-fadeInUp delay-100">
                <button class="category-pill px-6 py-2 rounded-full bg-blue-500 text-white font-medium">All Products</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Toys</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Clothing</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Diapers</button>
                <button class="category-pill px-6 py-2 rounded-full bg-white border-2 border-blue-100 text-blue-900 font-medium hover:border-blue-500 transition-colors">Food</button>
            </div>
            @include('products.baby-category.product-grid')
        </div>
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
