<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
        <!-- Logo with Back Button -->
        <div class="flex items-center gap-3 w-1/4 min-w-0">
            <div id="nav-back-container" class="flex items-center gap-3">
                <button id="nav-back-button" onclick="window.history.back()"
                    class="group flex items-center justify-center w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/60 hover:bg-[#0B3C9B] transition-all duration-300 hover:shadow-md hover:shadow-blue-200 cursor-pointer"
                    title="Kembali">
                    <svg class="w-5 h-5 text-slate-500 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>
                <div id="nav-back-separator" class="w-px h-6 bg-slate-200"></div>
            </div>
            <a href="{{ auth()->check() ? route('frontliner') : route('home') }}" class="text-2xl font-extrabold tracking-wider bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent transition duration-300">MD CAR RENTAL</a>
        </div>

        <!-- Navigation - Hidden on mobile -->
        <nav class="hidden lg:flex items-center justify-center gap-8 w-1/2 flex-shrink-0">
            @guest
                <a href="{{ route('home') }}" class="{{ Route::currentRouteName() === 'home' || Route::currentRouteName() === 'beranda' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Beranda</a>
                <a href="{{ route('armada') }}" class="{{ in_array(Route::currentRouteName(), ['armada', 'search-result', 'car-detail']) ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Armada</a>
                <a href="#testimoni" class="text-slate-600 hover:text-blue-600 transition font-medium">Testimoni Kami</a>
            @endguest

            @auth
                <a href="{{ route('frontliner') }}" class="{{ Route::currentRouteName() === 'frontliner' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Beranda</a>
                <a href="{{ route('armada') }}" class="{{ in_array(Route::currentRouteName(), ['armada', 'search-result', 'car-detail']) ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Armada</a>
                <a href="{{ route('pesanan-saya') }}" class="{{ Route::currentRouteName() === 'pesanan-saya' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Pesanan Saya</a>
                <a href="{{ route('favorite') }}" class="{{ Route::currentRouteName() === 'favorite' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Favorite</a>
            @endauth
        </nav>

        <!-- Right Section (Auth / Guest Buttons) -->
        <div class="flex items-center gap-4 w-1/4 justify-end min-w-0">
            @guest
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="px-4 py-2.5 text-blue-600 hover:text-blue-700 font-semibold text-sm transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-500 transition font-semibold text-sm shadow-md shadow-blue-600/15">
                        Daftar
                    </a>
                </div>
            @endguest

            @auth
                <!-- Notifications -->
                <button class="relative text-slate-500 hover:text-blue-600 transition p-1.5 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1 right-1 inline-flex items-center justify-center w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- User Menu -->
                <div class="flex items-center gap-3 border-l border-slate-100 pl-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-400">Customer</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/10">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="relative group">
                        <button class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-lg hidden group-hover:block divide-y divide-slate-50 overflow-hidden z-50">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    👤 Profil Saya
                                </a>
                                <a href="#" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    ⚙️ Pengaturan
                                </a>
                                <a href="#" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    💳 Pembayaran
                                </a>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50/50 transition">
                                        🚪 Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('nav-back-container');
            if (!container) return;

            let depth = 1;
            const state = window.history.state;
            const referrerSameOrigin = document.referrer && document.referrer.startsWith(window.location.origin);

            if (state && typeof state.depth === 'number') {
                depth = state.depth;
            } else {
                if (referrerSameOrigin) {
                    const lastDepth = parseInt(sessionStorage.getItem('nav_app_depth') || '0');
                    depth = lastDepth + 1;
                } else {
                    depth = 1;
                }
                window.history.replaceState({ depth: depth }, '');
            }

            sessionStorage.setItem('nav_app_depth', depth);

            const isHomepage = {{ in_array(Route::currentRouteName(), ['home', 'frontliner', 'beranda']) ? 'true' : 'false' }};

            if (isHomepage || depth <= 1) {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
            }
        });
    </script>
</header>
