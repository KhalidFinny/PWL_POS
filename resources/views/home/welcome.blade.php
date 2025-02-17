        <div class="w-full max-w-6xl grid md:grid-cols-12 gap-8">
            <div class="md:col-span-7 space-y-8">
                <h1 class="text-7xl md:text-8xl font-black text-blue-900 tracking-tight leading-none transform -rotate-3 origin-left">
                    Selamat
                    <br />
                    <span class="text-blue-600">Datang</span>
                </h1>
                <p class="text-2xl text-blue-800 max-w-2xl pl-4 border-l-4 border-blue-600">
                    Disini semuanya lengkap cuy. Explore a world of possibilities with our playful and comprehensive collection.
                </p>
                <div class="flex space-x-6">
                    <a href="{{ route('products.index') }}" class="bg-blue-900 text-white px-8 py-4 rounded-full flex items-center space-x-3 group hover:bg-blue-700 transition-colors">
                        <span>Browse Now</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="md:col-span-5 flex items-center justify-center">
                <div class="w-full max-w-xs bg-blue-50 rounded-2xl p-6 transform rotate-6 hover:rotate-0 transition-transform">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-blue-900">
                            Categories
                        </h2>
                    </div>
                    <p class="text-blue-800 opacity-75">
                        Discover our comprehensive collection across various categories.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-100 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-200 rounded-full opacity-30 blur-3xl"></div>
    </div>
</div>
