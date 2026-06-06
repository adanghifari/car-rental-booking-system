<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HD Rental Car - Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Instrument Sans', 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-white text-gray-900">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-blue-600">HD RENTAL CAR</span>
            </div>

            <!-- Navigation - Hidden on mobile -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('frontliner') }}" class="{{ Route::currentRouteName() === 'frontliner' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-gray-700 hover:text-blue-600 transition' }}">Beranda</a>
                <a href="{{ route('armada') }}" class="{{ Route::currentRouteName() === 'armada' || Route::currentRouteName() === 'search-result' ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-semibold' : 'text-gray-700 hover:text-blue-600 transition' }}">Armada</a>
                <a href="#pesanan-saya" class="text-gray-700 hover:text-blue-600 transition">Pesanan Saya</a>
                <a href="#testimoni" class="text-gray-700 hover:text-blue-600 transition">Testimoni</a>
            </nav>

            <!-- Right Section - User Profile -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <button class="relative text-gray-700 hover:text-blue-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                        2
                    </span>
                </button>

                <!-- User Menu -->
                <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">Member</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($user->name ?? 'U', 0, 1) }}
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-blue-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                👤 Profil Saya
                            </a>
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                ⚙️ Pengaturan
                            </a>
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                💳 Pembayaran
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 rounded-b-lg">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 lg:px-8 mt-6">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg leading-none">×</button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 lg:px-8 mt-6">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold text-lg leading-none">×</button>
            </div>
        </div>
    @endif

    <!-- Welcome Banner -->
    <section class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Selamat datang kembali, {{ $user->name ?? 'User' }}! 👋
            </h1>
            <p class="text-gray-600">
                Lanjutkan perjalanan Anda dengan HD Rental Car. Nikmati pengalaman berkendara yang luar biasa.
            </p>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Rentals -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Penyewaan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">5</p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Rentals -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Penyewaan Aktif</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">1</p>
                    </div>
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Biaya</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">Rp 12,5jt</p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.16 5.314l4.897-4.897a1 1 0 011.415 0l4.896 4.897a1 1 0 01-1.414 1.414L13 4.586V12a1 1 0 11-2 0V4.586l-3.793 3.793a1 1 0 01-1.414-1.414zM11.84 14.686l-4.897 4.897a1 1 0 01-1.415 0l-4.896-4.897a1 1 0 011.414-1.414L7 15.414V8a1 1 0 112 0v7.414l3.793-3.793a1 1 0 011.414 1.414z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Member Level -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Status Member</p>
                        <p class="text-lg font-bold text-blue-600 mt-2">Regular ⭐</p>
                        <p class="text-xs text-gray-500 mt-1">Upgrade dalam 2 sewa</p>
                    </div>
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Active Rental Section -->
    <section id="pesanan-saya" class="max-w-7xl mx-auto px-4 lg:px-8 py-8 border-t border-gray-200">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Penyewaan Aktif</h2>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Booking ID: BK-2024-001</p>
                        <h3 class="text-xl font-bold">Premium S-Series</h3>
                    </div>
                    <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                        Dalam Perjalanan
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Rental Details -->
                    <div>
                        <p class="text-gray-600 text-sm mb-2">Tanggal Sewa</p>
                        <p class="text-lg font-semibold text-gray-900">24 Mei - 26 Mei 2024</p>
                        <p class="text-sm text-gray-500">3 hari</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm mb-2">Total Biaya</p>
                        <p class="text-lg font-semibold text-gray-900">Rp 7,500,000</p>
                        <p class="text-sm text-green-600">✓ Pembayaran Lunas</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm mb-2">Lokasi Jemput</p>
                        <p class="text-lg font-semibold text-gray-900">Bandara Sokarno Hatta</p>
                        <p class="text-sm text-gray-500">Terminal 3, Level M</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-gray-600 text-sm">KM Awal</p>
                            <p class="text-2xl font-bold text-gray-900">25,432</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">KM Saat Ini</p>
                            <p class="text-2xl font-bold text-blue-600">28,156</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Perjalanan</p>
                            <p class="text-2xl font-bold text-gray-900">2,724 km</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        📍 Tracking Kendaraan
                    </button>
                    <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-2 px-4 rounded-lg transition">
                        📞 Hubungi Support
                    </button>
                    <button class="flex-1 border-2 border-gray-300 hover:border-gray-400 text-gray-900 font-semibold py-2 px-4 rounded-lg transition">
                        📄 Invoice
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="bg-gray-50 py-8 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Pesan Kendaraan Baru</h2>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <form method="GET" action="{{ route('search-result') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">📅 Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">💰 Harga Maksimal (Budget)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-500 text-sm">Rp</span>
                            <input type="number" name="max_price" placeholder="Contoh: 500000" value="{{ request('max_price') }}" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            🔍 Cari Kendaraan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Vehicles Section -->
    <section id="armada" class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Armada Unggulan</h2>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold">Lihat Semua →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cars as $car)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <div class="bg-gray-200 h-48 flex items-center justify-center overflow-hidden">
                            <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-1">{{ $car->name }}</h3>
                            <p class="text-gray-600 text-sm mb-3">{{ $car->brand }} - {{ $car->vehicle_type->label() }}</p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-600">⭐ {{ $car->rating ?? '4.8' }}/5</span>
                                <span class="text-sm text-gray-600">👥 {{ $car->seat_count }} penumpang</span>
                            </div>
                            <p class="text-2xl font-bold text-blue-600 mb-4">Rp {{ number_format($car->daily_rate, 0, ',', '.') }} / hari</p>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <button type="button" onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: 'available', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })" class="w-full text-center block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition cursor-pointer">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-xl border border-gray-200">
                    <p class="text-gray-500">Tidak ada mobil yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="bg-gray-50 py-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Kesan Eksklusif dari Pengguna Lain</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex gap-1 mb-4">
                        <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "Layanan HD Rental Car sangat memuaskan. Mobil dalam kondisi prima dan stafnya sangat profesional!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <p class="font-semibold text-gray-900">Budi Santoso</p>
                            <p class="text-sm text-gray-600">Pengusaha Jakarta</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 - Featured -->
                <div class="bg-blue-600 text-white rounded-xl p-6 md:scale-105 shadow-lg">
                    <div class="flex gap-1 mb-4">
                        <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                    </div>
                    <p class="mb-4 text-lg">
                        "Sangat puas dengan harga dan kualitas. Proses booking mudah. Rekomendasi untuk semua!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-400 rounded-full"></div>
                        <div>
                            <p class="font-semibold">Siti Maya</p>
                            <p class="text-sm text-blue-100">Travel Blogger</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex gap-1 mb-4">
                        <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "Terbaik di kelasnya! Armada terawat, lokasi strategis, dan harga sangat kompetitif!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <p class="font-semibold text-gray-900">Ahmad Rizki</p>
                            <p class="text-sm text-gray-600">Konsultan Bisnis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">HD RENTAL CAR</h3>
                    <p class="text-sm text-gray-400">
                        Penyewaan mobil terpercaya dengan armada terlengkap dan harga terjangkau.
                    </p>
                </div>

                <!-- Rental Models -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Model Rental</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Harian</a></li>
                        <li><a href="#" class="hover:text-white transition">Mingguan</a></li>
                        <li><a href="#" class="hover:text-white transition">Bulanan</a></li>
                        <li><a href="#" class="hover:text-white transition">Long-term</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Penjemput Bandara</a></li>
                        <li><a href="#" class="hover:text-white transition">Sopir Profesional</a></li>
                        <li><a href="#" class="hover:text-white transition">Asuransi Komprehensif</a></li>
                        <li><a href="#" class="hover:text-white transition">24/7 Support</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="tel:+62212345678" class="hover:text-white transition">+62 (21) 2345678</a></li>
                        <li><a href="mailto:info@hdrentalcar.com" class="hover:text-white transition">info@hdrentalcar.com</a></li>
                        <li><p>Jakarta, Indonesia</p></li>
                        <li><p>Jam Operasional: 24/7</p></li>
                    </ul>
                </div>
            </div>

            <hr class="border-gray-700 mb-8">

            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">&copy; 2024 HD Rental Car. All rights reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>
    <x-frontliner.booking-modal />
</body>
</html>
