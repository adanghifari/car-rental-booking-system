<header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-blue-600">MD CAR RENTAL</span>
            </div>

            <!-- Navigation - Hidden on mobile -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}" class="{{ Route::currentRouteName() === 'home' || Route::currentRouteName() === 'beranda' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-gray-700 hover:text-blue-600 transition' }}">Beranda</a>
                <a href="{{ route('armada') }}" class="{{ Route::currentRouteName() === 'armada' || Route::currentRouteName() === 'search-result' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-gray-700 hover:text-blue-600 transition' }}">Armada</a>
                <a href="#testimoni" class="text-gray-700 hover:text-blue-600 transition">Testimoni Kami</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2 text-blue-600 hover:text-blue-700 font-medium">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                    Daftar
                </a>
            </div>
        </div>
    </header>