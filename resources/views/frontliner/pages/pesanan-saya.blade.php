<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Riwayat Pemesanan - HD RENTAL CAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-blue-600">HD RENTAL CAR</span>
            </div>

            <!-- Navigation -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('frontliner') }}" class="text-gray-700 hover:text-blue-600 transition">Beranda</a>
                <a href="{{ route('armada') }}" class="text-gray-700 hover:text-blue-600 transition">Armada</a>
                <a href="{{ route('pesanan-saya') }}" class="text-[#0B3C9B] border-b-2 border-[#0B3C9B] pb-1 font-semibold">Pesanan Saya</a>
                <a href="{{ route('frontliner') }}#testimoni" class="text-gray-700 hover:text-blue-600 transition">Testimoni</a>
            </nav>

            <!-- User Profile -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
                    <div class="text-right border-r pr-4 border-gray-100 mr-2">
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">Member</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-10 w-full space-y-8">
        
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Semua Riwayat Pemesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua perjalanan Anda di sini.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('pesanan-saya') }}" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-gray-400">🔍</span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Resi, Nomor Kendaraan, atau Lokasi..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <!-- Date Filter -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Data tanggal</label>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
                <!-- Status Filter Pills -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status</label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $activeStatus = request('status', '');
                            $statuses = [
                                '' => 'Semua Status',
                                'aktif' => 'Aktif',
                                'selesai' => 'Selesai',
                                'pending' => 'Pending',
                                'dibatalkan' => 'Dibatalkan'
                            ];
                        @endphp
                        @foreach($statuses as $val => $label)
                            <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => null]) }}" 
                               class="px-4 py-2 rounded-xl text-xs font-semibold transition border {{ $activeStatus === $val ? 'bg-[#0B3C9B] border-[#0B3C9B] text-white shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                        <input type="hidden" name="status" value="{{ $activeStatus }}">
                    </div>
                </div>

                <!-- Vehicle Type Filter Pills -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tipe Kendaraan</label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $activeType = request('type', '');
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['type' => '', 'page' => null]) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-semibold transition border {{ $activeType === '' ? 'bg-[#0B3C9B] border-[#0B3C9B] text-white shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            Semua Kendaraan
                        </a>
                        @foreach(\App\Enums\VehicleType::cases() as $typeCase)
                            <a href="{{ request()->fullUrlWithQuery(['type' => $typeCase->value, 'page' => null]) }}" 
                               class="px-4 py-2 rounded-xl text-xs font-semibold transition border {{ $activeType === $typeCase->value ? 'bg-[#0B3C9B] border-[#0B3C9B] text-white shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                {{ $typeCase->label() }}
                            </a>
                        @endforeach
                        <input type="hidden" name="type" value="{{ $activeType }}">
                    </div>
                </div>

                <!-- Service Type Filter Pills -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tipe Layanan</label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $activeService = request('service', '');
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['service' => '', 'page' => null]) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-semibold transition border {{ $activeService === '' ? 'bg-[#0B3C9B] border-[#0B3C9B] text-white shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            Semua Layanan
                        </a>
                        @foreach(\App\Enums\RentalType::cases() as $serviceCase)
                            <a href="{{ request()->fullUrlWithQuery(['service' => $serviceCase->value, 'page' => null]) }}" 
                               class="px-4 py-2 rounded-xl text-xs font-semibold transition border {{ $activeService === $serviceCase->value ? 'bg-[#0B3C9B] border-[#0B3C9B] text-white shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                {{ $serviceCase->value === 'Self Drive' ? 'Lepas Kunci' : 'Dengan Sopir' }}
                            </a>
                        @endforeach
                        <input type="hidden" name="service" value="{{ $activeService }}">
                    </div>
                </div>
            </div>
        </form>

        <!-- Rentals Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rentals as $rental)
                @php
                    $car = $rental->car;
                    $latestPayment = $rental->paymentHistories()->latest()->first();
                    
                    // Determine Status State
                    if ($rental->status === \App\Enums\RentalStatus::ONGOING) {
                        $state = 'aktif';
                    } elseif ($rental->status === \App\Enums\RentalStatus::RETURNED && $latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::PAID) {
                        $state = 'selesai';
                    } elseif ($rental->status === \App\Enums\RentalStatus::PREPAID) {
                        $state = 'pending';
                    } else {
                        $state = 'dibatalkan';
                    }
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Top Image -->
                        <div class="relative bg-gray-100 h-44 overflow-hidden flex items-center justify-center">
                            <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                            
                            @if($state === 'pending')
                                <span class="absolute top-4 right-4 bg-[#F59E0B] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Pending</span>
                            @elseif($state === 'selesai')
                                <span class="absolute top-4 right-4 bg-[#10B981] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Selesai</span>
                            @elseif($state === 'aktif')
                                <span class="absolute top-4 right-4 bg-[#1E50DD] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Aktif</span>
                            @else
                                <span class="absolute top-4 right-4 bg-gray-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Dibatalkan</span>
                            @endif
                        </div>

                        <!-- Card Details -->
                        <div class="p-5 space-y-4">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">RESI: {{ $car->license_plate ?? 'B 1234 XYZ' }}</span>
                                <h3 class="text-base font-bold text-gray-900 mt-0.5">{{ $car->brand }} {{ $car->name }}</h3>
                            </div>

                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex items-center gap-2">
                                    <span>📅</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($rental->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($rental->end_date)->translatedFormat('d M Y') }}
                                        <span class="text-[10px] text-gray-400 ml-1">({{ max(1, \Carbon\Carbon::parse($rental->start_date)->diffInDays(\Carbon\Carbon::parse($rental->end_date))) }} hari)</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span>📍</span>
                                    <span>Bandara Soekarno Hatta T3</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-5 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Total Biaya</span>
                            @if($state === 'pending')
                                <span class="text-sm font-bold text-yellow-600">Menunggu</span>
                            @else
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @if($state === 'pending')
                                @php
                                    $payUrl = $latestPayment?->redirect_url ?? route('booking.simulate-payment', ['rental_id' => $rental->id]);
                                @endphp
                                <a href="{{ $payUrl }}" class="bg-[#F59E0B] hover:bg-yellow-600 text-white font-bold text-xs py-2 px-4 rounded-xl transition cursor-pointer">
                                    Bayar Sekarang
                                </a>
                            @elseif($state === 'selesai')
                                <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}" class="border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2 px-3 rounded-xl transition">
                                    Detail
                                </a>
                                <a href="{{ route('car-detail', ['car' => $car->id]) }}" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold text-xs py-2 px-3 rounded-xl transition">
                                    Pesan Lagi
                                </a>
                            @elseif($state === 'aktif')
                                <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold text-xs py-2 px-4 rounded-xl transition">
                                    Lihat Detail
                                </a>
                            @else
                                <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}" class="border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2 px-4 rounded-xl transition">
                                    Detail
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-100 rounded-2xl p-16 text-center text-gray-500 shadow-sm">
                    <p class="font-medium text-base">Tidak ada riwayat pemesanan yang sesuai.</p>
                    <p class="text-xs text-gray-400 mt-1">Coba gunakan filter pencarian yang berbeda.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($rentals->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 pt-6 mt-8 gap-4">
                <span class="text-xs text-gray-500">
                    Menampilkan {{ $rentals->firstItem() ?? 0 }} - {{ $rentals->lastItem() ?? 0 }} dari {{ $rentals->total() }}
                </span>
                <div class="flex items-center space-x-1.5">
                    <!-- Previous Page -->
                    @if ($rentals->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs cursor-not-allowed select-none">&lt;</span>
                    @else
                        <a href="{{ $rentals->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 text-xs transition">&lt;</a>
                    @endif

                    <!-- Page Links -->
                    @foreach ($rentals->getUrlRange(1, $rentals->lastPage()) as $page => $url)
                        @if ($page == $rentals->currentPage())
                            <span class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs select-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 text-xs transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    <!-- Next Page -->
                    @if ($rentals->hasMorePages())
                        <a href="{{ $rentals->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 text-xs transition">&gt;</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs cursor-not-allowed select-none">&gt;</span>
                    @endif
                </div>
            </div>
        @endif

    </main>

    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-xs">
            <p>&copy; 2026 HD Rental Car. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
