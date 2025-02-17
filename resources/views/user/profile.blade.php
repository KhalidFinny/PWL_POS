<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>User Profile | warung.</title>
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
    </style>
</head>
<body class="relative">
    @include('home.header')
    <main class="container mx-auto px-6 py-12 relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12 animate-fadeInUp transform -rotate-3 origin-left">
                <h1 class="text-6xl font-bold text-blue-900 mb-4 tracking-tight">
                    user
                    <br />
                    <span class="text-blue-600">profile</span>
                </h1>
                <p class="text-xl text-blue-600 max-w-2xl pl-4 border-l-4 border-blue-600">Manage your personal details and settings</p>
            </div>

            <div class="bg-white shadow-lg rounded-2xl p-6 max-w-4xl mx-auto animate-fadeInUp delay-100">
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-4xl font-semibold text-blue-900">
                        {{ substr($name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-blue-900">{{ $name }}</p>
                        <p class="text-lg text-blue-600">User ID: {{ $id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="border-t border-blue-100 backdrop-blur-sm bg-white/80 mt-12">
        <div class="container mx-auto px-6 py-8">
            <p class="text-center text-blue-900 text-sm"> 2025 warung. all rights reserved.</p>
        </div>
    </footer>
</body>
</html>
