<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-10 w-full space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Pembayaran</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola transaksi dan riwayat pembayaran sewa mobil Anda.</p>
            </div>
            <a href="{{ route('pesanan-saya') }}" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-4 py-2.5 rounded-xl transition duration-300">
                Lihat Pesanan Saya →
            </a>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Pengeluaran -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-750 text-white rounded-3xl p-6 shadow-lg shadow-blue-500/10 border border-blue-500/20 relative overflow-hidden flex flex-col justify-between h-40">
                <div class="absolute right-0 top-0 translate-x-4 -translate-y-4 opacity-10">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 8.25h19.5M2 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-100 block font-semibold">Total Pengeluaran</span>
                    <h2 class="text-2xl font-black mt-2">Rp {{ number_format($totalSpend, 0, ',', '.') }}</h2>
                </div>
                <p class="text-[10px] text-blue-200 font-semibold">✓ Berhasil dibayarkan & terverifikasi</p>
            </div>

            <!-- Card 2: Menunggu Pembayaran -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block font-semibold">Menunggu Pembayaran</span>
                        <h2 class="text-3xl font-black text-slate-900 mt-2">{{ $pendingCount }}</h2>
                    </div>
                    <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                @if($pendingCount > 0)
                    <div class="text-[10px] text-amber-600 font-bold bg-amber-50 border border-amber-100 rounded-xl px-3 py-1.5 w-fit flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Selesaikan pembayaran tertunda Anda</span>
                    </div>
                @else
                    <p class="text-[10px] text-slate-400 font-semibold">Semua pembayaran aman & beres</p>
                @endif
            </div>

            <!-- Card 3: Total Transaksi -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block font-semibold">Total Transaksi</span>
                        <h2 class="text-3xl font-black text-slate-900 mt-2">{{ $totalCount }}</h2>
                    </div>
                    <span class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </span>
                </div>
                <p class="text-[10px] text-slate-400 font-semibold">Keseluruhan sesi tagihan Anda</p>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('pembayaran.index') }}" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative flex items-center">
                        <svg class="absolute left-4 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Kode Transaksi (Order ID) atau Nama Kendaraan..." class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200/60 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                    </div>
                </div>
                <!-- Status Filter Dropdown -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status Pembayaran</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                        <option value="">Semua Status</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <!-- Quick Filter Pills -->
            <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-2">Filter Cepat:</span>
                    @php
                        $activeStatus = request('status', '');
                        $statusPills = [
                            '' => 'Semua',
                            'paid' => 'Lunas',
                            'pending' => 'Menunggu',
                            'expired' => 'Expired',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp
                    @foreach($statusPills as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => null]) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-300 border {{ $activeStatus === $val ? 'bg-[#0B3C9B] border-[#0B3C9B] text-white shadow-md shadow-blue-900/10' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                
                @if(request()->anyFilled(['q', 'status']))
                    <a href="{{ route('pembayaran.index') }}" class="text-xs font-semibold text-red-500 hover:text-red-650 transition">Hapus Semua Filter ×</a>
                @endif
            </div>
        </form>

        <!-- Payments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($payments as $payment)
                @php
                    $rental = $payment->rental;
                    $car = $rental?->car;
                    
                    // Style attributes by status
                    $statusLabel = 'Menunggu';
                    $badgeColor = 'bg-amber-50 text-amber-700 border-amber-150';
                    
                    if ($payment->status === \App\Enums\PaymentStatus::PAID) {
                        $statusLabel = 'Lunas';
                        $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-150';
                    } elseif ($payment->status === \App\Enums\PaymentStatus::EXPIRED) {
                        $statusLabel = 'Expired';
                        $badgeColor = 'bg-slate-100 text-slate-600 border-slate-200';
                    } elseif ($payment->status === \App\Enums\PaymentStatus::CANCELLED) {
                        $statusLabel = 'Dibatalkan';
                        $badgeColor = 'bg-rose-50 text-rose-700 border-rose-150';
                    }

                    // Extract payment method name from payload
                    $paymentMethodLabel = 'Midtrans Gate';
                    if ($payment->payload) {
                        $payload = $payment->payload;
                        $type = $payload['payment_type'] ?? null;
                        if ($type === 'bank_transfer') {
                            $bank = $payload['va_numbers'][0]['bank'] ?? null;
                            $paymentMethodLabel = $bank ? strtoupper($bank) . ' VA' : 'Bank VA';
                        } elseif ($type === 'gopay') {
                            $paymentMethodLabel = 'GoPay';
                        } elseif ($type === 'qris') {
                            $paymentMethodLabel = 'QRIS';
                        } elseif ($type === 'cstore') {
                            $paymentMethodLabel = ucfirst($payload['store'] ?? 'Gerai');
                        } elseif ($type === 'credit_card') {
                            $paymentMethodLabel = 'Credit Card';
                        } elseif ($type === 'echannel') {
                            $paymentMethodLabel = 'Mandiri Bill';
                        } elseif ($type === 'shopeepay') {
                            $paymentMethodLabel = 'ShopeePay';
                        } elseif ($payment->provider === 'midtrans' && empty(config('services.midtrans.server_key'))) {
                            $paymentMethodLabel = 'Simulasi Lokal';
                        }
                    }
                @endphp
                
                @if($rental && $car)
                    <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition duration-300">
                        <div>
                            <!-- Header Area -->
                            <div class="relative bg-slate-100 h-44 overflow-hidden flex items-center justify-center">
                                <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                                <span class="absolute top-4 right-4 border text-[10px] font-bold px-3 py-1.5 rounded-xl uppercase tracking-wider {{ $badgeColor }}">{{ $statusLabel }}</span>
                            </div>

                            <!-- Info Area -->
                            <div class="p-6 space-y-4">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">ORDER ID: {{ $payment->provider_order_id }}</span>
                                    <h3 class="text-base font-bold text-slate-800 mt-1">{{ $car->brand }} {{ $car->name }}</h3>
                                </div>

                                <div class="divide-y divide-slate-50 text-xs text-slate-500 space-y-2.5">
                                    <div class="flex justify-between items-center pt-2.5">
                                        <span class="font-medium text-slate-400">Metode</span>
                                        <span class="font-bold text-slate-800">{{ $paymentMethodLabel }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2.5">
                                        <span class="font-medium text-slate-400">Tanggal Transaksi</span>
                                        <span class="font-bold text-slate-800">
                                            {{ $payment->created_at->translatedFormat('d M Y, H:i') }} WIB
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2.5">
                                        <span class="font-medium text-slate-400">Jumlah Tagihan</span>
                                        <span class="font-bold text-slate-900 text-sm">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Area -->
                        <div class="p-6 border-t border-slate-50 bg-slate-50/20 flex items-center gap-2">
                            <a href="{{ route('booking.detail', $rental->id) }}" class="flex-1 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs py-3 px-4 rounded-2xl transition duration-300 text-center cursor-pointer">
                                Lihat Detail
                            </a>
                            
                            @if($payment->status === \App\Enums\PaymentStatus::PENDING && in_array($rental->status, [\App\Enums\RentalStatus::PREPAID, \App\Enums\RentalStatus::PENDING_VERIFICATION]))
                                @php
                                    $payUrl = $payment->redirect_url ?? route('booking.simulate-payment', ['rental_id' => $rental->id]);
                                @endphp
                                <a href="{{ $payUrl }}" class="flex-1 bg-[#F59E0B] hover:bg-yellow-600 text-white font-bold text-xs py-3 px-4 rounded-2xl transition duration-300 text-center cursor-pointer">
                                    Bayar Sekarang
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-full bg-white border border-slate-100 rounded-3xl p-16 text-center text-slate-500 shadow-sm flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <p class="font-bold text-base text-slate-800">Belum ada riwayat pembayaran</p>
                    <p class="text-xs text-slate-400 mt-1">Transaksi Anda akan otomatis tercatat di sini setelah melakukan booking kendaraan.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between border-t border-slate-150 pt-6 mt-8 gap-4">
                <span class="text-xs text-slate-500">
                    Menampilkan {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }}
                </span>
                <div class="flex items-center space-x-1.5">
                    <!-- Previous Page -->
                    @if ($payments->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 text-xs cursor-not-allowed select-none">&lt;</span>
                    @else
                        <a href="{{ $payments->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 text-xs transition">&lt;</a>
                    @endif

                    <!-- Page Links -->
                    @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                        @if ($page == $payments->currentPage())
                            <span class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs select-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 text-xs transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    <!-- Next Page -->
                    @if ($payments->hasMorePages())
                        <a href="{{ $payments->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 text-xs transition">&gt;</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 text-xs cursor-not-allowed select-none">&gt;</span>
                    @endif
                </div>
            </div>
        @endif

    </main>

    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-xs">
            <p>&copy; 2026 MD CAR RENTAL. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
