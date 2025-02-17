<header class="relative backdrop-blur-sm bg-white/80 border-b border-blue-100 sticky top-0 z-50">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-blue-900 tracking-tight">warung.</h1>
            <nav class="flex gap-8">
                <a href="/" class="nav-link text-blue-900 font-medium">home</a>
                <a href="{{ route('products.index') }}" class="nav-link text-blue-900 font-medium">food & beverage</a>
                <a href="{{ route('users.show', ['id' => 1, 'name' => 'john']) }}" class="nav-link text-blue-900 font-medium">profile</a>
                <a href="{{ route('sales.index') }}" class="nav-link text-blue-900 font-medium">sales</a>
            </nav>
        </div>
    </div>
</header>
