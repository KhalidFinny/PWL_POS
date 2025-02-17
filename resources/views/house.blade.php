<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Home</title>
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

        .swiss-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #3B82F6;
            overflow: hidden;
            position: relative;
        }

        .swiss-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .swiss-button:active {
            transform: translateY(0);
        }

        .swiss-button .arrow {
            transform: translateX(0);
            transition: transform 0.3s ease;
        }

        .swiss-button:hover .arrow {
            transform: translateX(4px);
        }

        .decorative-circle {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .decorative-circle:hover {
            transform: scale(1.1);
            border-color: #3B82F6;
            background-color: rgba(219, 234, 254, 0.3);
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
    <div class="flex flex-col justify-center mt-60 items-center space-y-12">
        @include('home.welcome')
        @include('home.category-buttons')
    </div>
</body>
</html>
