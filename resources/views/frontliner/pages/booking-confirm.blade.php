<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Detail Pemesanan - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

<x-frontliner.navbar />

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-8 w-full">
        <!-- Step Indicator -->
        <div class="mb-8 max-w-3xl mx-auto">
            <div class="flex items-center justify-between text-xs sm:text-sm font-semibold">
                <!-- Step 1: Detail Sewa -->
                <div class="flex items-center gap-2 text-blue-600">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">1</span>
                    <span class="hidden sm:inline">Detail Sewa</span>
                    <span class="sm:hidden">Detail</span>
                </div>
                <div class="flex-grow mx-4 h-0.5 bg-blue-600"></div>

                <!-- Step 2: Verifikasi Booking (Active) -->
                <div class="flex items-center gap-2 text-blue-600">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">2</span>
                    <span class="hidden sm:inline">Verifikasi Booking</span>
                    <span class="sm:hidden">Booking</span>
                </div>
                <div class="flex-grow mx-4 h-0.5 bg-gray-200"></div>

                <!-- Step 3: Verifikasi Data Penyewa -->
                <div class="flex items-center gap-2 text-gray-400">
                    <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs">3</span>
                    <span class="hidden sm:inline">Verifikasi Data Penyewa</span>
                    <span class="sm:hidden">Identitas</span>
                </div>
                <div class="flex-grow mx-4 h-0.5 bg-gray-200"></div>

                <!-- Step 4: Pembayaran -->
                <div class="flex items-center gap-2 text-gray-400">
                    <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs">4</span>
                    <span class="hidden sm:inline">Pembayaran</span>
                    <span class="sm:hidden">Bayar</span>
                </div>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-gray-500 mb-6 gap-2 items-center">
            <a href="{{ route('armada') }}" class="hover:text-blue-600">Fleet</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('car-detail', $car->id) }}" class="hover:text-blue-600">{{ $car->name }}</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">Pemesanan</span>
        </nav>

        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Konfirmasi Detail Pemesanan</h1>
        <p class="text-sm text-gray-600 mb-8 font-medium">Periksa rincian pemesanan Anda sebelum melanjutkan ke verifikasi data penyewa.</p>

        <!-- Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            
            <!-- Left Side: Order Form (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                
                @if (session('error'))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm">
                        ⚠️ {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('booking.identity') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                    <input type="hidden" name="start_date" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" value="{{ $end_date }}">
                    <input type="hidden" name="service_type" value="{{ $service_type }}">

                    <!-- Durasi Sewa -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                    📅
                                </div>
                                <h2 class="text-base font-bold text-gray-900">Durasi Sewa</h2>
                            </div>
                            <span class="bg-blue-100 text-[#0B3C9B] text-xs font-bold px-3 py-1.5 rounded-xl border border-blue-200">{{ $days }} Hari</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Mulai Sewa</label>
                                <div class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 flex justify-between items-center select-none">
                                    <span>{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }}</span>
                                    <span class="text-gray-400">📅</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Selesai Sewa</label>
                                <div class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 flex justify-between items-center select-none">
                                    <span>{{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</span>
                                    <span class="text-gray-400">📅</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipe Layanan -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                🔑
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Tipe Layanan</h2>
                        </div>

                        <div class="w-full">
                            @if($service_type === 'self_drive')
                                <!-- Lepas Kunci Card -->
                                <div class="border border-blue-600 bg-blue-50/20 rounded-2xl p-4 flex flex-col justify-between h-24 transition relative w-full">
                                    <div class="flex justify-between items-start">
                                        <span class="text-lg">🔑</span>
                                        <div class="w-4 h-4 rounded-full border border-blue-600 bg-blue-600 text-white flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Lepas Kunci</h3>
                                        <p class="text-[10px] text-gray-500">Kemudi Sendiri</p>
                                    </div>
                                </div>
                            @else
                                <!-- Dengan Sopir Card -->
                                <div class="border border-blue-600 bg-blue-50/20 rounded-2xl p-4 flex flex-col justify-between h-24 transition relative w-full">
                                    <div class="flex justify-between items-start">
                                        <span class="text-lg">🤵</span>
                                        <div class="w-4 h-4 rounded-full border border-blue-600 bg-blue-600 text-white flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900">Dengan Sopir</h3>
                                        <p class="text-[10px] text-gray-500">Concierge Driver</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-4">
                        <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.99] text-white font-bold py-4 rounded-2xl text-sm transition-all duration-200 shadow-xl shadow-blue-200 flex items-center justify-center gap-2">
                            Lanjut ke Verifikasi Data Penyewa
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Car Details Sticky Card (2 Cols) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-md p-6 space-y-6 lg:sticky lg:top-24">
                    <!-- Image -->
                    <div class="rounded-2xl overflow-hidden h-48 bg-gray-50 relative">
                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1000&q=80' }}"
                             alt="{{ $car->name }}" class="w-full h-full object-cover">
                        <span class="absolute top-4 left-4 bg-emerald-500 text-white text-[9px] font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wider">
                            Premium Tier
                        </span>
                    </div>

                    <!-- Details -->
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">{{ $car->brand }} {{ $car->name }}</h2>
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-1.5">
                            ⚡ Full Electric Performance & Premium Comfort
                        </p>
                    </div>

                    <!-- Pricing Breakdown Summary -->
                    <div class="border-t border-gray-100 pt-4 space-y-3 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Durasi Sewa:</span>
                            <span class="font-bold text-gray-800">{{ $days }} Hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Sewa Mobil (Rp {{ number_format($car->daily_rate, 0, ',', '.') }} x {{ $days }}):</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($rentCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Layanan & Asuransi:</span>
                            <span class="font-bold text-gray-800">Rp 100.000</span>
                        </div>
                        @if($service_type === 'with_driver')
                            <div class="flex justify-between">
                                <span>Biaya Driver (Rp 150.000 x {{ $days }}):</span>
                                <span class="font-bold text-gray-800">Rp {{ number_format($driverCost, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-3 border-t border-dashed border-gray-200 text-sm font-extrabold text-gray-900">
                            <span>Total Harga:</span>
                            <span class="text-[#0B3C9B] text-base">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Ubah Pesanan Button -->
                    <a href="{{ route('car-detail', $car->id) }}?start_date={{ $start_date }}&end_date={{ $end_date }}&service_type={{ $service_type }}" 
                       class="flex items-center justify-center gap-2 w-full py-3 border border-dashed border-gray-300 hover:border-blue-600 text-gray-600 hover:text-blue-600 rounded-xl text-xs font-bold transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                        </svg>
                        Ubah Pesanan
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500">
        <p>&copy; 2026 MD CAR RENTAL. Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>
</body>
</html>
