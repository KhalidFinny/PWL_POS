<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Products</title>
    <style>
        body {
            font-family: 'Urbanist', sans-serif;
        }

        .swiss-card {
            transition: all 0.2s ease;
            border: 2px solid #E2E8F0;
        }

        .swiss-card:hover {
            border-color: #3B82F6;
            transform: translateY(-4px);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: currentColor;
            transition: width 0.2s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .grid-pattern {
            background-image: linear-gradient(rgba(219, 234, 254, 0.3) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(219, 234, 254, 0.3) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-white">
    @include('home.header')

    <main class="container mx-auto px-6 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12 animate-fadeInUp">
                <h1 class="text-4xl font-bold text-blue-900 mb-4">Product Categories</h1>
                <p class="text-blue-600">Discover our collection of quality products.</p>
            </div>
            @include('products.categories.product-categories')
        </div>
    </main>
    @include('products.categories.footer')
</body>
</html>
