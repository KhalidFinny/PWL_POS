<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Sales Transactions</title>
    <style>
        body {
            font-family: 'Urbanist', sans-serif;
            background: linear-gradient(
                135deg,
                rgba(219, 234, 254, 0.3) 0%,
                rgba(255, 255, 255, 1) 100%
            );
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

        .transaction-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .transaction-card:hover {
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
        .delay-3 { animation-delay: 0.3s; }
    </style>
</head>
<body>
    @include('home.header')

    <main class="container mx-auto px-6 py-12">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-12 animate-fadeInUp">
                <h1 class="text-4xl font-bold text-blue-900 mb-4">Sales Transactions</h1>
                <p class="text-blue-600">Track your daily transactions and monitor sales performance</p>
            </div>

            <!-- Stats Overview -->
            @include('sales.stats-overview')

            <!-- Recent Transactions -->
            @include('sales.recent-transactions')
        </div>
    </main>
</body>
</html>
