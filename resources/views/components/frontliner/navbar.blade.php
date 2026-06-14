<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
        <!-- Logo with Back Button -->
        <div class="flex items-center gap-3 w-1/4 min-w-0">
            <div id="nav-back-container" class="flex items-center gap-3">
                <button id="nav-back-button" onclick="window.history.back()"
                    class="group flex items-center justify-center w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/60 hover:bg-[#0B3C9B] transition-all duration-300 hover:shadow-md hover:shadow-blue-200 cursor-pointer"
                    title="Kembali">
                    <svg class="w-5 h-5 text-slate-500 group-hover:text-white transition-colors duration-300"
                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <div id="nav-back-separator" class="w-px h-6 bg-slate-200"></div>
            </div>
            <a href="{{ auth()->check() ? route('frontliner') : route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="MD CAR RENTAL" class="h-12 w-auto">
            </a>
        </div>

        <!-- Navigation - Hidden on mobile -->
        <nav class="hidden lg:flex items-center justify-center gap-8 w-1/2 flex-shrink-0">
            @guest
            <a href="{{ route('home') }}"
                class="{{ Route::currentRouteName() === 'home' || Route::currentRouteName() === 'beranda' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Beranda</a>
            <a href="{{ route('armada') }}"
                class="{{ in_array(Route::currentRouteName(), ['armada', 'search-result', 'car-detail']) ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Armada</a>
            <a href="#testimoni" class="text-slate-600 hover:text-blue-600 transition font-medium">Testimoni Kami</a>
            @endguest

            @auth
            <a href="{{ route('frontliner') }}"
                class="{{ Route::currentRouteName() === 'frontliner' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Beranda</a>
            <a href="{{ route('armada') }}"
                class="{{ in_array(Route::currentRouteName(), ['armada', 'search-result', 'car-detail']) ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Armada</a>
            <a href="{{ route('pesanan-saya') }}"
                class="{{ Route::currentRouteName() === 'pesanan-saya' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Pesanan
                Saya</a>
            <a href="{{ route('favorite') }}"
                class="{{ Route::currentRouteName() === 'favorite' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-slate-600 hover:text-blue-600 transition font-medium' }}">Favorite</a>
            @endauth
        </nav>

        <!-- Right Section (Auth / Guest Buttons) -->
        <div class="flex items-center gap-4 w-1/4 justify-end min-w-0">
            @guest
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                    class="px-4 py-2.5 text-blue-600 hover:text-blue-700 font-semibold text-sm transition">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-500 transition font-semibold text-sm shadow-md shadow-blue-600/15">
                    Daftar
                </a>
            </div>
            @endguest

            @auth
            @php
            $currentUser = auth()->user();
            $notifications = $currentUser?->recentNotifications(6) ?? collect();
            $unreadNotificationCount = $currentUser?->unreadNotificationCount() ?? 0;
            @endphp

            <!-- Notifications -->
            <div class="relative">
                <button id="notification-dropdown-button" type="button"
                    class="relative text-slate-500 hover:text-blue-600 transition p-2 rounded-xl hover:bg-slate-50 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    @if($unreadNotificationCount > 0)
                    <span
                        class="absolute -top-0.5 -right-0.5 min-w-5 h-5 px-1 inline-flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full ring-2 ring-white">
                        {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                    </span>
                    @endif
                </button>

                <div id="notification-dropdown-menu"
                    class="absolute right-0 top-full mt-3 w-[25rem] max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-[2rem] border border-slate-300 bg-slate-300 shadow-2xl shadow-slate-400/30 hidden opacity-0 translate-y-1 transition-all duration-200 z-50">
                    <div class="relative overflow-hidden border-b border-blue-100 bg-gradient-to-br from-[#123C7A] via-[#1E4E9A] to-[#2C6DD5] px-5 py-4 text-white">
                        <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.24),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(191,219,254,0.18),_transparent_32%)]"></div>
                        <div class="relative flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold tracking-tight">Notifikasi</p>
                                    <span class="inline-flex items-center rounded-full border border-white/10 bg-white/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100">
                                        Inbox
                                    </span>
                                </div>
                                <p class="text-xs text-slate-200">
                                    {{ $unreadNotificationCount > 0 ? $unreadNotificationCount.' perlu perhatian' : 'Semua notifikasi sudah dibaca' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('notifications.index') }}"
                                    class="inline-flex items-center rounded-full bg-white px-3 py-2 text-[11px] font-bold text-slate-900 shadow-lg shadow-black/10 transition hover:-translate-y-0.5">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        @if($unreadNotificationCount > 0)
                        <div class="relative mt-4 flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/10 px-3.5 py-3 backdrop-blur">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-100">Belum dibaca</p>
                                <p class="mt-1 text-lg font-extrabold">{{ $unreadNotificationCount }}</p>
                            </div>
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-xl border border-white/15 bg-white/90 px-3 py-2 text-[11px] font-bold text-slate-900 transition hover:bg-white cursor-pointer">
                                    Tandai semua dibaca
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="max-h-[28rem] overflow-y-auto bg-slate-300 p-2.5">
                        @forelse($notifications as $notification)
                        @php
                        $data = $notification->data ?? [];
                        $type = strtoupper((string) ($data['type'] ?? 'SYSTEM'));
                        $meta = match ($type) {
                        'VERIFICATION' => ['label' => 'Verifikasi', 'tone' => 'blue', 'icon' => 'M9 12l2 2
                        4-4m6-2.25A11.96 11.96 0 0112 3.75a11.96 11.96 0 01-9 3.75v4.5c0 4.98 3.44 9.36 9 10.5 5.56-1.14
                        9-5.52 9-10.5v-4.5z'],
                        'PAYMENT' => ['label' => 'Pembayaran', 'tone' => 'amber', 'icon' => 'M21 12.75V19.5A2.25 2.25 0
                        0118.75 21h-13.5A2.25 2.25 0 013 18.75v-13.5A2.25 2.25 0 015.25 3H12M16.5 3l4.5 4.5M21 3l-7.5
                        7.5'],
                        'RENTAL' => ['label' => 'Rental', 'tone' => 'emerald', 'icon' => 'M3 10.5h18M6 6.75h12M6
                        14.25h12M6 18h8.25'],
                        'CANCELLATION' => ['label' => 'Pembatalan', 'tone' => 'rose', 'icon' => 'M6.75 6.75l10.5
                        10.5M17.25 6.75l-10.5 10.5'],
                        default => ['label' => 'Sistem', 'tone' => 'slate', 'icon' => 'M12 9v4.5m0 3.75h.008M21 12a9 9 0
                        11-18 0 9 9 0 0118 0z'],
                        };
                        $isUnread = is_null($notification->read_at);
                        $createdAt = $notification->created_at ?
                        $notification->created_at->locale('id')->diffForHumans() : '';
                        $message = (string) ($data['message'] ?? '');
                        $title = (string) ($data['title'] ?? 'Notifikasi');
                        $url = route('notifications.open', $notification->id);
                        $buttonRoute = route('notifications.read', $notification->id);
                        $toneClasses = match ($meta['tone']) {
                        'blue' => ['bg-blue-50 text-blue-600 border-blue-100', 'bg-blue-600/10 text-blue-700'],
                        'amber' => ['bg-amber-50 text-amber-600 border-amber-100', 'bg-amber-600/10 text-amber-700'],
                        'emerald' => ['bg-emerald-50 text-emerald-600 border-emerald-100', 'bg-emerald-600/10
                        text-emerald-700'],
                        'rose' => ['bg-rose-50 text-rose-600 border-rose-100', 'bg-rose-600/10 text-rose-700'],
                        default => ['bg-slate-50 text-slate-500 border-slate-100', 'bg-slate-600/10 text-slate-700'],
                        };
                        @endphp
                        <article class="mb-2 last:mb-0 rounded-[1.5rem] border p-3.5 transition {{ $isUnread ? 'border-blue-200 bg-white shadow-md shadow-blue-100/50' : 'border-slate-300 bg-slate-100/95' }}">
                            <div class="flex gap-3">
                                <div
                                    class="shrink-0 w-10 h-10 rounded-2xl border {{ $toneClasses[0] }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}">
                                        </path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wide {{ $toneClasses[1] }}">
                                                    {{ $meta['label'] }}
                                                </span>
                                                @if($isUnread)
                                                <span class="inline-flex items-center rounded-full bg-[#2C6DD5] px-2.5 py-1 text-[10px] font-bold tracking-wide text-white">
                                                    Baru
                                                </span>
                                                @endif
                                            </div>
                                            <a href="{{ $url }}"
                                                class="mt-2 block text-sm font-bold text-slate-900 hover:text-blue-700 transition leading-5">
                                                {{ $title }}
                                            </a>
                                            <p class="mt-1.5 text-xs text-slate-600 leading-5 max-h-10 overflow-hidden">
                                                {{ $message }}
                                            </p>
                                        </div>
                                        @if($isUnread)
                                        <span class="shrink-0 mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-[#2C6DD5] ring-4 ring-blue-100"></span>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                            <span>{{ $createdAt }}</span>
                                            @if(! empty($data['rental_id']))
                                            <span class="text-slate-400">•</span>
                                            <span>Booking #{{ $data['rental_id'] }}</span>
                                            @endif
                                        </div>
                                        @unless($isUnread)
                                        <span class="text-[11px] font-semibold text-slate-500">Sudah dibaca</span>
                                        @endunless
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <a href="{{ $url }}"
                                            class="inline-flex items-center rounded-xl bg-[#123C7A] px-3 py-2 text-[11px] font-bold text-white transition hover:bg-[#1E4E9A]">
                                            Buka detail
                                        </a>
                                        @if($isUnread)
                                        <form method="POST" action="{{ $buttonRoute }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 transition hover:bg-slate-50 cursor-pointer">
                                                Tandai dibaca
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                        @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-100 p-6 text-center">
                            <div
                                class="w-12 h-12 mx-auto rounded-2xl bg-white text-slate-400 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.182 15.182a4.5 4.5 0 11-6.364-6.364 4.5 4.5 0 016.364 6.364z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 22a8.5 8.5 0 118-4.4">
                                    </path>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-bold text-slate-900">Belum ada notifikasi</p>
                            <p class="mt-1 text-xs text-slate-500">Update booking akan muncul di sini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-3 border-l border-slate-100 pl-4 relative">
                <button id="user-dropdown-button"
                    class="flex items-center gap-3 hover:opacity-90 focus:outline-none cursor-pointer group text-left">
                    <div class="text-right hidden sm:block">
                        <p
                            class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors duration-200">
                            {{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-400">Customer</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/10 transition-transform duration-200 group-hover:scale-105">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <svg id="user-dropdown-caret"
                        class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-300"
                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="user-dropdown-menu"
                    class="absolute right-0 top-full mt-2.5 w-56 bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 hidden opacity-0 translate-y-1 transition-all duration-200 z-[100] p-1.5 space-y-0.5">
                    <a href="{{ route('customer.profile') }}"
                        class="group/item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200">
                        <svg class="w-[18px] h-[18px] text-slate-400 group-hover/item:text-blue-500 transition-colors duration-200"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profil Saya
                    </a>
                    <a href="{{ route('customer.settings') }}"
                        class="group/item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-700 hover:text-blue-600 hover:bg-slate-50">
                        <svg class="w-[18px] h-[18px] text-slate-400 group-hover/item:text-blue-500 transition-colors duration-200"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a>
                    <a href="{{ route('pembayaran.index') }}"
                        class="group/item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium {{ Route::currentRouteName() === 'pembayaran.index' ? 'text-blue-600 bg-blue-50/50' : 'text-slate-600' }} hover:text-blue-600 hover:bg-blue-50/50 transition-all duration-200">
                        <svg class="w-[18px] h-[18px] {{ Route::currentRouteName() === 'pembayaran.index' ? 'text-blue-500' : 'text-slate-400' }} group-hover/item:text-blue-500 transition-colors duration-200"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                        Pembayaran
                    </a>
                            <div class="h-px bg-slate-100/80 my-1 mx-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="group/item flex w-full items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50/60 transition-all duration-200 cursor-pointer">
                                    <svg class="w-[18px] h-[18px] text-red-500 group-hover/item:text-red-600 transition-colors duration-200"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17.25 9.75L19.5 12m0 0l-2.25 2.25M19.5 12H9m10.5-9v4.5m0 9V21M3 6.75h1.5M3 17.25h1.5" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
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
        const notificationButton = document.getElementById('notification-dropdown-button');
        const notificationMenu = document.getElementById('notification-dropdown-menu');

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

        if (notificationButton && notificationMenu) {
            const closeNotificationDropdown = function() {
                notificationMenu.classList.remove('opacity-100', 'translate-y-0');
                notificationMenu.classList.add('opacity-0', 'translate-y-1');
                setTimeout(() => {
                    notificationMenu.classList.add('hidden');
                }, 200);
            };

            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = notificationMenu.classList.contains('hidden');

                if (isHidden) {
                    notificationMenu.classList.remove('hidden');
                    setTimeout(() => {
                        notificationMenu.classList.remove('opacity-0', 'translate-y-1');
                        notificationMenu.classList.add('opacity-100', 'translate-y-0');
                    }, 10);
                } else {
                    closeNotificationDropdown();
                }
            });

            document.addEventListener('click', function(e) {
                if (!notificationButton.contains(e.target) && !notificationMenu.contains(e.target)) {
                    if (!notificationMenu.classList.contains('hidden')) {
                        closeNotificationDropdown();
                    }
                }
            });
        }

        // Back button container logic
        const container = document.getElementById('nav-back-container');
        if (container) {
            let depth = 1;
            const state = window.history.state;
            const referrerSameOrigin = document.referrer && document.referrer.startsWith(window.location
                .origin);

            if (state && typeof state.depth === 'number') {
                depth = state.depth;
            } else {
                if (referrerSameOrigin) {
                    const lastDepth = parseInt(sessionStorage.getItem('nav_app_depth') || '0');
                    depth = lastDepth + 1;
                } else {
                    depth = 1;
                }
                window.history.replaceState({
                    depth: depth
                }, '');
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
