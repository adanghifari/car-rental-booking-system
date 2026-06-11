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
                <div class="flex items-center gap-3 border-l border-slate-100 pl-4 relative">
                    <button id="user-dropdown-button" class="flex items-center gap-3 hover:opacity-90 focus:outline-none cursor-pointer group text-left">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors duration-200">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-slate-400">Customer</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/10 transition-transform duration-200 group-hover:scale-105">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <svg id="user-dropdown-caret" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown-menu" class="absolute right-0 top-full mt-2.5 w-56 bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-100/40 hidden opacity-0 translate-y-1 transition-all duration-200 z-50 p-1.5 space-y-0.5">
                        <a href="#" class="group/item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all duration-200">
                            <svg class="w-[18px] h-[18px] text-slate-400 group-hover/item:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Profil Saya
                        </a>
                        <a href="#" class="group/item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all duration-200">
                            <svg class="w-[18px] h-[18px] text-slate-400 group-hover/item:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Pengaturan
                        </a>
                        <a href="#" class="group/item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all duration-200">
                            <svg class="w-[18px] h-[18px] text-slate-400 group-hover/item:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            Pembayaran
                        </a>
                        <div class="h-px bg-slate-100 my-1 mx-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="group/item flex w-full items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50/60 transition-all duration-200 cursor-pointer">
                                <svg class="w-[18px] h-[18px] text-red-500 group-hover/item:text-red-600 transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Toggle Logic
            const dropdownButton = document.getElementById('user-dropdown-button');
            const dropdownMenu = document.getElementById('user-dropdown-menu');
            const caret = document.getElementById('user-dropdown-caret');

            if (dropdownButton && dropdownMenu) {
                dropdownButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = dropdownMenu.classList.contains('hidden');
                    
                    if (isHidden) {
                        dropdownMenu.classList.remove('hidden');
                        setTimeout(() => {
                            dropdownMenu.classList.remove('opacity-0', 'translate-y-1');
                            dropdownMenu.classList.add('opacity-100', 'translate-y-0');
                        }, 10);
                        if (caret) {
                            caret.classList.add('rotate-180');
                        }
                    } else {
                        closeDropdown();
                    }
                });

                function closeDropdown() {
                    dropdownMenu.classList.remove('opacity-100', 'translate-y-0');
                    dropdownMenu.classList.add('opacity-0', 'translate-y-1');
                    if (caret) {
                        caret.classList.remove('rotate-180');
                    }
                    setTimeout(() => {
                        dropdownMenu.classList.add('hidden');
                    }, 200);
                }

                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        if (!dropdownMenu.classList.contains('hidden')) {
                            closeDropdown();
                        }
                    }
                });
            }

            // Back button container logic
            const container = document.getElementById('nav-back-container');
            if (container) {
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
            }
        });
    </script>
</header>
