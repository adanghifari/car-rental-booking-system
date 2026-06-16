<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MD CAR RENTAL - Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&family=inter:400,500,600,700"
        rel="stylesheet" />
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
        <div
            class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="text-emerald-500 hover:text-emerald-700 font-bold text-lg leading-none">&times;</button>
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
            <button onclick="this.parentElement.parentElement.remove()"
                class="text-red-500 hover:text-red-700 font-bold text-lg leading-none">&times;</button>
        </div>
    </div>
    @endif

    <!-- Welcome Banner (Hero Section) -->
    <section class="max-w-7xl mx-auto px-4 lg:px-8 mt-6">
        <div
            class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-950 text-white rounded-2xl md:rounded-3xl shadow-xl overflow-hidden relative border border-slate-800 p-8 md:p-12">
            <!-- Glowing light effects -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-2xl">
                <span
                    class="inline-flex items-center gap-1.5 bg-blue-500/25 border border-blue-400/30 text-blue-200 px-3 py-1 rounded-full text-xs font-semibold mb-6 tracking-wide backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 text-blue-300 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span>Partner Perjalanan Terbaik Anda</span>
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-white mb-4 leading-tight">
                    Selamat datang kembali, <br class="hidden sm:inline"><span
                        class="bg-gradient-to-r from-blue-400 to-indigo-300 bg-clip-text text-transparent">{{ $user->name ?? 'User' }}</span>!
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-yellow-400 inline-block animate-bounce align-middle ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5a1.5 1.5 0 013 0v1" />
                    </svg>
                </h1>
                <p class="text-blue-100/80 text-base md:text-lg mb-8 font-light leading-relaxed">
                    Lanjutkan perjalanan Anda dengan MD CAR RENTAL. Nikmati berkendara aman dan nyaman dengan armada
                    pilihan terbaik yang terawat.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#cari-mobil"
                        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3.5 rounded-xl transition-all hover:-translate-y-0.5 shadow-lg shadow-blue-600/35 text-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari Kendaraan
                    </a>
                    <a href="{{ route('armada') }}"
                        class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/15 text-white border border-white/10 font-semibold px-6 py-3.5 rounded-xl transition-all hover:-translate-y-0.5 backdrop-blur-sm text-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lihat Semua Armada
                    </a>
                </div>
            </div>
        </div>
    </section>

    @php
    $totalSpent = $rentals->whereIn('status', [\App\Enums\RentalStatus::ONGOING,
    \App\Enums\RentalStatus::RETURNED])->sum('total_price');
    $activeCount = $rentals->where('status', \App\Enums\RentalStatus::ONGOING)->count();
    $prepaidCount = $rentals->where('status', \App\Enums\RentalStatus::PREPAID)->count();
    $completedCount = $rentals->where('status', \App\Enums\RentalStatus::RETURNED)->count();
    @endphp

    <!-- Quick Stats -->
    <section class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Rentals -->
            <div
                class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-blue-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Penyewaan</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ $rentals->count() }}</p>
                    <p class="text-xs text-slate-400 mt-1">Transaksi sewa</p>
                </div>
                <div
                    class="bg-blue-50 text-blue-600 p-3.5 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            <!-- Active Rentals -->
            <div
                class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Penyewaan Aktif</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $activeCount }}</p>
                    <p class="text-xs text-slate-400 mt-1">Sedang berlangsung</p>
                </div>
                <div
                    class="bg-emerald-50 text-emerald-600 p-3.5 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>

            <!-- Total Spent -->
            <div
                class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Pengeluaran</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-2">Rp
                        {{ number_format($totalSpent, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">Pembayaran terverifikasi</p>
                </div>
                <div
                    class="bg-indigo-50 text-indigo-600 p-3.5 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>

            <!-- Pending Payments (Fourth card instead of status member!) -->
            <div
                class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-amber-100 transition-all duration-300 flex items-center justify-between group">
                <div>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Menunggu Pembayaran</p>
                    <p
                        class="text-3xl font-extrabold @if($prepaidCount > 0) text-amber-600 animate-pulse @else text-slate-800 @endif mt-2">
                        {{ $prepaidCount }}</p>
                    <p class="text-xs text-slate-400 mt-1">Pemesanan tertunda</p>
                </div>
                <div
                    class="@if($prepaidCount > 0) bg-amber-50 text-amber-600 @else bg-slate-50 text-slate-400 @endif p-3.5 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                <p class="text-sm text-slate-500 mt-1">Daftar transaksi rental Anda yang sedang aktif atau menunggu
                    penyelesaian pembayaran.</p>
            </div>
            <a href="{{ route('pesanan-saya') }}"
                class="group flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm border border-blue-100"
                title="Lihat Semua Riwayat Pemesanan">
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none"
                    stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="space-y-6">
            @forelse($activeRentals as $rental)
            <div
                class="bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden relative @if($rental->status === \App\Enums\RentalStatus::ONGOING) border-l-4 border-l-blue-600 @elseif($rental->status === \App\Enums\RentalStatus::PREPAID) border-l-4 border-l-amber-500 @else border-l-4 border-l-slate-400 @endif">

                <!-- Card Top Header -->
                <div
                    class="px-6 py-4 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/40">
                    <div>
                        <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Booking ID:
                            BK-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $rental->car->brand ?? '' }}
                            {{ $rental->car->name ?? 'Mobil' }}</h3>
                    </div>
                    <div>
                        @if($rental->status === \App\Enums\RentalStatus::ONGOING)
                        <span
                            class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase">
                            ✓ Aktif & Lunas
                        </span>
                        @elseif($rental->status === \App\Enums\RentalStatus::PREPAID)
                        <span
                            class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase animate-pulse">
                            ⚠️ Menunggu Pembayaran
                        </span>
                        @else
                        <span
                            class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase">
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
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Tanggal Sewa
                                </p>
                                <p class="text-base font-semibold text-slate-800 mt-0.5">
                                    {{ \Carbon\Carbon::parse($rental->start_date)->translatedFormat('d M') }} -
                                    {{ \Carbon\Carbon::parse($rental->end_date)->translatedFormat('d M Y') }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ max(1, \Carbon\Carbon::parse($rental->start_date)->diffInDays(\Carbon\Carbon::parse($rental->end_date))) }}
                                    Hari Sewa</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-slate-50 p-2.5 rounded-xl text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Biaya</p>
                                <p class="text-base font-bold text-slate-800 mt-0.5">Rp
                                    {{ number_format($rental->total_price, 0, ',', '.') }}</p>
                                <p
                                    class="text-xs font-semibold mt-0.5 @if($rental->status === \App\Enums\RentalStatus::ONGOING) text-emerald-600 @elseif($rental->status === \App\Enums\RentalStatus::PREPAID) text-amber-600 @else text-slate-600 @endif">
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
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Tipe Layanan
                                </p>
                                <p class="text-base font-semibold text-slate-800 mt-0.5">
                                    {{ $rental->type === \App\Enums\RentalType::WITH_DRIVER ? 'Dengan Sopir' : 'Lepas Kunci' }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">Metode Layanan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-3 border-t border-slate-50">
                        <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}"
                            class="flex-1 text-center bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold py-3 px-4 rounded-xl transition-all text-sm border border-slate-200/60 cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Lihat Detail Pesanan</span>
                        </a>
                        @if($rental->status === \App\Enums\RentalStatus::PREPAID)
                        @php
                        $latestPayment = $rental->paymentHistories()->latest()->first();
                        $payUrl = $latestPayment?->redirect_url ?? route('booking.simulate-payment', ['rental_id' =>
                        $rental->id]);
                        @endphp
                        <a href="{{ $payUrl }}"
                            class="flex-1 text-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-md shadow-emerald-600/20 text-sm cursor-pointer hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>Lanjutkan Pembayaran</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <!-- Empty State -->
            <div class="bg-white border border-slate-100 rounded-2xl p-12 text-center shadow-sm max-w-xl mx-auto">
                <div
                    class="bg-blue-50 text-blue-500 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 mb-1">Belum Ada Pesanan Aktif</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Semua transaksi sewa yang sedang aktif atau
                    menunggu penyelesaian pembayaran akan ditampilkan di halaman ini.</p>
                <a href="#cari-mobil"
                    class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-3 rounded-xl transition text-sm shadow-md shadow-blue-500/10 cursor-pointer">
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
                <p class="text-slate-500 text-sm mt-1">Tentukan periode sewa dan budget harian Anda untuk menemukan
                    mobil terbaik yang siap disewa.</p>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 md:p-8">
                @php
                    $today = now()->toDateString();
                @endphp
                <form method="GET" action="{{ route('search-result') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Tanggal Mulai</span>
                        </label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" min="{{ $today }}"
                            oninput="if(this.form.end_date.value && this.form.end_date.value < this.value){this.form.end_date.value=this.value} this.form.end_date.min=this.value;"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-slate-700 text-sm">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                            </svg>
                            <span>Tanggal Selesai</span>
                        </label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" min="{{ request('start_date', $today) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-slate-700 text-sm">
                    </div>

                    <!-- Harga Harian -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 14a2 2 0 110-4h.01" />
                            </svg>
                            <span>Budget Harian Maksimal</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 text-sm">Rp</span>
                            <input type="number" name="max_price" placeholder="Contoh: 500000"
                                value="{{ request('max_price') }}"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-slate-700 text-sm">
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-4 rounded-xl transition shadow-lg shadow-blue-500/20 text-sm cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
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
        <div class="flex justify-between items-center pb-4 border-b border-gray-200 mb-8">
            <div>
                <span class="text-blue-600 text-xs font-semibold uppercase tracking-wider">
                    Katalog
                </span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">
                    Armada Unggulan
                </h2>
                <p class="text-slate-500 text-sm mt-1">
                    Pilihan kendaraan terbaik yang siap menemani perjalanan Anda.
                </p>
            </div>

            <a href="{{ route('armada') }}"
                class="text-[#0B3C9B] hover:text-[#082D76] font-semibold text-sm flex items-center gap-1 transition">
                Lihat Semua →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($cars as $car)

            <div
                class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition">

                <div>

                    {{-- IMAGE --}}
                    <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}"
                            alt="{{ $car->name }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex justify-between items-start mb-2">

                        <div>
                            <h4 class="text-sm font-bold text-gray-900">
                                {{ $car->name }}
                            </h4>

                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">
                                {{ $car->brand }} - {{ $car->vehicle_type->label() }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">

                            <span class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded">
                                ★ {{ number_format($car->average_rating, 1) }}
                            </span>

                            <button type="button" onclick="toggleFavorite({{ $car->id }}, event)"
                                data-car-id="{{ $car->id }}"
                                class="favorite-btn text-slate-500 hover:text-red-500 transition-colors duration-200 cursor-pointer focus:outline-none p-1"
                                title="Tambah ke Favorit">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.2" stroke="currentColor"
                                    class="w-5 h-5 heart-icon transition-transform duration-200 active:scale-75">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />

                                </svg>

                            </button>

                        </div>

                    </div>

                    {{-- SPEC --}}
                    <div
                        class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">

                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ $car->seat_count }} Penumpang</span>
                        </span>

                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $car->transmission->label() }}</span>
                        </span>

                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>{{ number_format($car->cc) }} cc</span>
                        </span>

                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Th {{ $car->year }}</span>
                        </span>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="border-t pt-3 border-gray-50 space-y-2.5">

                    <div class="flex justify-between items-center">
                        <p class="text-sm font-bold text-gray-900">
                            Rp {{ number_format($car->daily_rate, 0, ',', '.') }}
                            <span class="text-[10px] font-normal text-gray-400">
                                /hari
                            </span>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">

                        <a href="{{ route('car-detail', ['car' => $car->id]) }}"
                            class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 text-center py-2 rounded-xl text-xs font-bold transition">
                            Detail
                        </a>

                        @if(($car->status->value ?? $car->status) === 'available')

                        <button type="button" onclick="openBookingModal({
                                    id: {{ $car->id }},
                                    name: '{{ addslashes($car->name) }}',
                                    image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}',
                                    dailyRate: {{ $car->daily_rate }},
                                    status: '{{ $car->status->value ?? $car->status }}',
                                    selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }},
                                    driverAvailable: {{ $car->driver_available ? 'true' : 'false' }}
                                })"
                            class="bg-[#0B3C9B] hover:bg-[#082D76] text-white text-center py-2 rounded-xl text-xs font-bold transition cursor-pointer">

                            Pesan

                        </button>

                        @else

                        <button disabled
                            class="bg-gray-200 text-gray-400 text-center py-2 rounded-xl text-xs font-bold cursor-not-allowed">

                            Tidak Tersedia

                        </button>

                        @endif

                    </div>

                </div>

            </div>

            @empty

            <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-4">

                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>

                <p class="text-gray-500 font-medium text-base mb-1">
                    Armada Tidak Ditemukan
                </p>

                <p class="text-gray-400 text-xs">
                    Saat ini belum ada mobil yang tersedia.
                </p>

            </div>

            @endforelse
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('armada') }}"
                class="inline-flex items-center gap-2 bg-[#0B3C9B] hover:bg-[#082D76] text-white px-6 py-3 rounded-xl font-semibold text-sm transition">

                Lihat Semua Armada

                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />

                </svg>

            </a>
        </div>

    </section>

    <x-frontliner.footer />
    <x-frontliner.booking-modal />
</body>

</html>
