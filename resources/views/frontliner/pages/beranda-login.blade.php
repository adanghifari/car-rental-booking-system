<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MD CAR RENTAL - Dashboard</title>
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
    <x-frontliner.navbar />

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 lg:px-8 mt-6">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg leading-none">&times;</button>
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
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    <!-- Welcome Banner (Hero Section) -->
    <section class="max-w-7xl mx-auto px-4 lg:px-8 mt-6">
        <div class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-950 text-white rounded-2xl md:rounded-3xl shadow-xl overflow-hidden relative border border-slate-800 p-8 md:p-12">
            <!-- Glowing light effects -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 bg-blue-500/25 border border-blue-400/30 text-blue-200 px-3 py-1 rounded-full text-xs font-semibold mb-6 tracking-wide backdrop-blur-sm">
                    ✨ Partner Perjalanan Terbaik Anda
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-white mb-4 leading-tight">
                    Selamat datang kembali, <br class="hidden sm:inline"><span class="bg-gradient-to-r from-blue-400 to-indigo-300 bg-clip-text text-transparent">{{ $user->name ?? 'User' }}</span>! 👋
                </h1>
                <p class="text-blue-100/80 text-base md:text-lg mb-8 font-light leading-relaxed">
                    Lanjutkan perjalanan Anda dengan MD CAR RENTAL. Nikmati berkendara aman dan nyaman dengan armada pilihan terbaik yang terawat.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#cari-mobil" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3.5 rounded-xl transition-all hover:-translate-y-0.5 shadow-lg shadow-blue-600/35 text-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari Kendaraan
                    </a>
                    <a href="{{ route('armada') }}" class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/15 text-white border border-white/10 font-semibold px-6 py-3.5 rounded-xl transition-all hover:-translate-y-0.5 backdrop-blur-sm text-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lihat Semua Armada
                    </a>
                </div>
            </div>
        </div>
    </section>

    @php
        $totalSpent = $rentals->whereIn('status', [\App\Enums\RentalStatus::ONGOING, \App\Enums\RentalStatus::RETURNED])->sum('total_price');
        $activeCount = $rentals->where('status', \App\Enums\RentalStatus::ONGOING)->count();
        $prepaidCount = $rentals->where('status', \App\Enums\RentalStatus::PREPAID)->count();
        $completedCount = $rentals->where('status', \App\Enums\RentalStatus::RETURNED)->count();
    @endphp

    <!-- Quick Stats -->
    <section class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Rentals -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-blue-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Penyewaan</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ $rentals->count() }}</p>
                    <p class="text-xs text-slate-400 mt-1">Transaksi sewa</p>
                </div>
                <div class="bg-blue-50 text-blue-600 p-3.5 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            <!-- Active Rentals -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Penyewaan Aktif</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $activeCount }}</p>
                    <p class="text-xs text-slate-400 mt-1">Sedang berlangsung</p>
                </div>
                <div class="bg-emerald-50 text-emerald-600 p-3.5 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Pengeluaran</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-2">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">Pembayaran terverifikasi</p>
                </div>
                <div class="bg-indigo-50 text-indigo-600 p-3.5 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>

            <!-- Pending Payments (Fourth card instead of status member!) -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-amber-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Menunggu Pembayaran</p>
                    <p class="text-3xl font-extrabold @if($prepaidCount > 0) text-amber-600 animate-pulse @else text-slate-800 @endif mt-2">{{ $prepaidCount }}</p>
                    <p class="text-xs text-slate-400 mt-1">Pemesanan tertunda</p>
                </div>
                <div class="@if($prepaidCount > 0) bg-amber-50 text-amber-600 @else bg-slate-50 text-slate-400 @endif p-3.5 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </section>

    @php
        $activeRentals = $rentals->whereIn('status', [\App\Enums\RentalStatus::ONGOING, \App\Enums\RentalStatus::PREPAID]);
    @endphp

    <!-- Active Rental Section -->
    <section class="max-w-7xl mx-auto px-4 lg:px-8 py-8 border-t border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Pesanan Aktif Anda</h2>
                <p class="text-sm text-slate-500 mt-1">Daftar transaksi rental Anda yang sedang aktif atau menunggu penyelesaian pembayaran.</p>
            </div>
            <a href="{{ route('pesanan-saya') }}" class="group flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm border border-blue-100" title="Lihat Semua Riwayat Pemesanan">
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        <div class="space-y-6">
            @forelse($activeRentals as $rental)
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden relative @if($rental->status === \App\Enums\RentalStatus::ONGOING) border-l-4 border-l-blue-600 @elseif($rental->status === \App\Enums\RentalStatus::PREPAID) border-l-4 border-l-amber-500 @else border-l-4 border-l-slate-400 @endif">
                    
                    <!-- Card Top Header -->
                    <div class="px-6 py-4 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/40">
                        <div>
                            <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Booking ID: BK-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $rental->car->brand ?? '' }} {{ $rental->car->name ?? 'Mobil' }}</h3>
                        </div>
                        <div>
                            @if($rental->status === \App\Enums\RentalStatus::ONGOING)
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase">
                                    ✓ Aktif & Lunas
                                </span>
                            @elseif($rental->status === \App\Enums\RentalStatus::PREPAID)
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase animate-pulse">
                                    ⚠️ Menunggu Pembayaran
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase">
                                    Selesai
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Rental Details -->
                            <div class="flex items-start gap-3">
                                <div class="bg-slate-50 p-2.5 rounded-xl text-slate-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Tanggal Sewa</p>
                                    <p class="text-base font-semibold text-slate-800 mt-0.5">
                                        {{ \Carbon\Carbon::parse($rental->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($rental->end_date)->translatedFormat('d M Y') }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ max(1, \Carbon\Carbon::parse($rental->start_date)->diffInDays(\Carbon\Carbon::parse($rental->end_date))) }} Hari Sewa</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="bg-slate-50 p-2.5 rounded-xl text-slate-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Biaya</p>
                                    <p class="text-base font-bold text-slate-800 mt-0.5">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</p>
                                    <p class="text-xs font-semibold mt-0.5 @if($rental->status === \App\Enums\RentalStatus::ONGOING) text-emerald-600 @elseif($rental->status === \App\Enums\RentalStatus::PREPAID) text-amber-600 @else text-slate-600 @endif">
                                        @if($rental->status === \App\Enums\RentalStatus::ONGOING)
                                            ✓ Sudah Lunas
                                        @elseif($rental->status === \App\Enums\RentalStatus::PREPAID)
                                            ⚠ Menunggu Transaksi
                                        @else
                                            ✓ Selesai
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="bg-slate-50 p-2.5 rounded-xl text-slate-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Tipe Layanan</p>
                                    <p class="text-base font-semibold text-slate-800 mt-0.5">
                                        {{ $rental->type === \App\Enums\RentalType::WITH_DRIVER ? 'Dengan Sopir' : 'Lepas Kunci' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">Metode Layanan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-3 border-t border-slate-50">
                            <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}" class="flex-1 text-center bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold py-3 px-4 rounded-xl transition-all text-sm border border-slate-200/60 cursor-pointer">
                                🔍 Lihat Detail Pesanan
                            </a>
                            @if($rental->status === \App\Enums\RentalStatus::PREPAID)
                                @php
                                    $latestPayment = $rental->paymentHistories()->latest()->first();
                                    $payUrl = $latestPayment?->redirect_url ?? route('booking.simulate-payment', ['rental_id' => $rental->id]);
                                @endphp
                                <a href="{{ $payUrl }}" class="flex-1 text-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md shadow-emerald-600/20 text-sm cursor-pointer hover:-translate-y-0.5">
                                    💳 Lanjutkan Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white border border-slate-100 rounded-2xl p-12 text-center shadow-sm max-w-xl mx-auto">
                    <div class="bg-blue-50 text-blue-500 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800 mb-1">Belum Ada Pesanan Aktif</h3>
                    <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Semua transaksi sewa yang sedang aktif atau menunggu penyelesaian pembayaran akan ditampilkan di halaman ini.</p>
                    <a href="#cari-mobil" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-3 rounded-xl transition text-sm shadow-md shadow-blue-500/10 cursor-pointer">
                        Cari & Sewa Mobil Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section id="cari-mobil" class="bg-slate-50/70 py-10 border-t border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="max-w-3xl mb-8">
                <span class="text-blue-600 text-xs font-semibold uppercase tracking-wider">Pesan Mobil</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Pesan Kendaraan Baru</h2>
                <p class="text-slate-500 text-sm mt-1">Tentukan tanggal mulai dan budget harian Anda untuk menemukan mobil terbaik yang siap disewa.</p>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 md:p-8">
                <form method="GET" action="{{ route('search-result') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">📅 Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-slate-700 text-sm">
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">💰 Harga Maksimal (Budget)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 text-sm">Rp</span>
                            <input type="number" name="max_price" placeholder="Contoh: 500000" value="{{ request('max_price') }}" class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-slate-700 text-sm">
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-4 rounded-xl transition shadow-lg shadow-blue-500/20 text-sm cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari Kendaraan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Vehicles Section -->
    <section id="armada" class="max-w-7xl mx-auto px-4 lg:px-8 py-12">
        <div class="flex justify-between items-end mb-8">
            <div>
                <span class="text-blue-600 text-xs font-semibold uppercase tracking-wider">Katalog</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Armada Unggulan</h2>
                <p class="text-slate-500 text-sm mt-1">Jajaran mobil terbaik kami yang siap menemani perjalanan bisnis maupun wisata Anda.</p>
            </div>
            <a href="{{ route('armada') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm transition flex items-center gap-1 cursor-pointer">
                Lihat Semua <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($cars as $car)
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                            <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-4 left-4 bg-white/95 text-slate-800 px-2.5 py-1 rounded-lg text-xs font-bold shadow-sm uppercase tracking-wide">
                                {{ $car->vehicle_type->label() }}
                            </span>
                        </div>
                        <div class="p-5">
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ $car->brand }}</span>
                            <h3 class="font-extrabold text-slate-800 text-lg mt-0.5 mb-3">{{ $car->name }}</h3>
                            <div class="grid grid-cols-3 gap-2 text-[11px] text-gray-500 font-medium mb-6 border-t pt-4 border-gray-50">
                                <span class="flex items-center">👥 {{ $car->seat_count }} Kursi</span>
                                <span class="flex items-center">⚙️ {{ $car->transmission->label() }}</span>
                                <span class="flex items-center">⚡ {{ $car->cc }} cc</span>
                            </div>
                            <p class="text-2xl font-extrabold text-blue-600">Rp {{ number_format($car->daily_rate, 0, ',', '.') }}<span class="text-xs text-slate-400 font-normal"> / hari</span></p>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <button type="button" onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: 'available', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })" class="w-full text-center block bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition cursor-pointer text-sm shadow-md shadow-blue-500/10 hover:shadow-blue-500/20">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-100">
                    <p class="text-slate-500">Tidak ada mobil yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="bg-slate-50/50 py-16 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-blue-600 text-xs font-semibold uppercase tracking-wider">Testimoni</span>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">Kesan Eksklusif dari Pengguna Kami</h2>
                <p class="text-slate-500 text-sm mt-1">Simak pengalaman langsung dari pelanggan yang mempercayakan perjalanan mereka kepada MD CAR RENTAL.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                @forelse($reviews as $index => $review)
                    @php
                        $isFeatured = ($index === 1);
                        $initials = '';
                        $names = explode(' ', $review->user->name);
                        foreach (array_slice($names, 0, 2) as $n) {
                            $initials .= strtoupper(substr($n, 0, 1));
                        }
                    @endphp
                    @if($isFeatured)
                        <!-- Testimonial Featured -->
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-md shadow-blue-500/10 flex flex-col justify-between">
                            <div>
                                <div class="flex gap-0.5 text-yellow-300 mb-4 text-xs font-bold">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <p class="text-white/95 text-sm leading-relaxed mb-6 font-light italic">
                                    "{{ $review->comment ?? 'Sangat puas dengan layanan MD CAR RENTAL!' }}"
                                </p>
                            </div>
                            <div class="flex items-center gap-3 border-t border-white/10 pt-4">
                                <div class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ $initials ?: 'U' }}
                                </div>
                                <div>
                                    <p class="font-bold text-sm">{{ $review->user->name }}</p>
                                    <p class="text-xs text-blue-200">{{ $review->car->name }} ({{ $review->car->brand }})</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Testimonial Regular -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex gap-0.5 text-amber-400 mb-4 text-xs font-bold">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <p class="text-slate-600 text-sm leading-relaxed mb-6 font-light italic">
                                    "{{ $review->comment ?? 'Layanan yang luar biasa, unit bersih dan terawat!' }}"
                                </p>
                            </div>
                            <div class="flex items-center gap-3 border-t border-slate-50 pt-4">
                                <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ $initials ?: 'U' }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $review->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $review->car->name }} ({{ $review->car->brand }})</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <!-- Testimonial 1 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex gap-0.5 text-amber-400 mb-4">
                                ★ ★ ★ ★ ★
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed mb-6 font-light">
                                "Layanan MD CAR RENTAL sangat memuaskan. Mobil dalam kondisi prima, bersih, dan stafnya sangat profesional dalam memandu serah terima!"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 border-t border-slate-50 pt-4">
                            <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold text-sm">
                                BS
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">Budi Santoso</p>
                                <p class="text-xs text-slate-400">Pengusaha Jakarta</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 - Featured -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-md shadow-blue-500/10 flex flex-col justify-between">
                        <div>
                            <div class="flex gap-0.5 text-yellow-300 mb-4">
                                ★ ★ ★ ★ ★
                            </div>
                            <p class="text-white/95 text-base leading-relaxed mb-6 font-light">
                                "Sangat puas dengan harga & kualitas mobil. Proses booking online cepat & verifikasi wajahnya modern sekali. Rekomendasi untuk semua traveler!"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 border-t border-white/10 pt-4">
                            <div class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                SM
                            </div>
                            <div>
                                <p class="font-bold text-sm">Siti Maya</p>
                                <p class="text-xs text-blue-200">Travel Blogger</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex gap-0.5 text-amber-400 mb-4">
                                ★ ★ ★ ★ ★
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed mb-6 font-light">
                                "Terbaik di kelasnya! Armada sangat terawat, lokasi serah terima strategis, dan harganya sangat kompetitif dibanding sewa konvensional lainnya."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 border-t border-slate-50 pt-4">
                            <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold text-sm">
                                AR
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">Ahmad Rizki</p>
                                <p class="text-xs text-slate-400">Konsultan Bisnis</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">MD CAR RENTAL</h3>
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
                <p class="text-sm text-gray-400">&copy; 2024 MD CAR RENTAL. All rights reserved.</p>
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
