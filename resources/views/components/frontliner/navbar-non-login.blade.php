<header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo with Back Button -->
            <div class="flex items-center gap-3">
                <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('home') }}'"
                    class="group flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-[#0B3C9B] transition-all duration-300 hover:shadow-md hover:shadow-blue-200"
                    title="Kembali">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>
                <div class="w-px h-6 bg-gray-200"></div>
                <span class="text-2xl font-bold text-blue-600">MD CAR RENTAL</span>
            </div>

            <!-- Navigation - Hidden on mobile -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}" class="{{ Route::currentRouteName() === 'home' || Route::currentRouteName() === 'beranda' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-gray-700 hover:text-blue-600 transition' }}">Beranda</a>
                <a href="{{ route('armada') }}" class="{{ in_array(Route::currentRouteName(), ['armada', 'search-result', 'car-detail']) ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-gray-700 hover:text-blue-600 transition' }}">Armada</a>
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