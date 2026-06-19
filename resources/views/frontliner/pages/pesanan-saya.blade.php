<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Riwayat Pemesanan - MD CAR RENTAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    </style>
</head>

<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-10 w-full space-y-8">

        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Semua Riwayat Pemesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua perjalanan Anda di sini.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('pesanan-saya') }}"
            class="bg-white p-4 md:p-5 rounded-[1.5rem] border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-col gap-3 md:flex-row md:items-center flex-1">
                    <div class="shrink-0">
                        <p class="text-sm font-bold text-slate-900">Filter</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1.7fr)_minmax(0,0.7fr)] gap-3 flex-1">
                        <div class="relative flex items-center">
                            <svg class="absolute left-4 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.602 10.602z" />
                            </svg>
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Cari resi atau kendaraan..."
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <input type="date" name="date" value="{{ request('date') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <a href="{{ route('pesanan-saya') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition shrink-0">
                    Reset
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                @php
                $activeStatus = request('status', '');
                $statuses = [
                '' => 'Semua Status',
                'aktif' => 'Aktif',
                'selesai' => 'Selesai',
                'pending' => 'Pending',
                'dibatalkan' => 'Dibatalkan'
                ];
                $activeType = request('type', '');
                $activeService = request('service', '');
                @endphp

                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                    <label for="status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.18em] mb-2">Status</label>
                    <select id="status" name="status"
                        onchange="this.form.submit()"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($statuses as $val => $label)
                        <option value="{{ $val }}" {{ $activeStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                    <label for="type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.18em] mb-2">Kendaraan</label>
                    <select id="type" name="type"
                        onchange="this.form.submit()"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ $activeType === '' ? 'selected' : '' }}>Semua Kendaraan</option>
                        @foreach(\App\Enums\VehicleType::cases() as $typeCase)
                        <option value="{{ $typeCase->value }}" {{ $activeType === $typeCase->value ? 'selected' : '' }}>
                            {{ $typeCase->label() }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3">
                    <label for="service" class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.18em] mb-2">Layanan</label>
                    <select id="service" name="service"
                        onchange="this.form.submit()"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ $activeService === '' ? 'selected' : '' }}>Semua Layanan</option>
                        @foreach(\App\Enums\RentalType::cases() as $serviceCase)
                        <option value="{{ $serviceCase->value }}" {{ $activeService === $serviceCase->value ? 'selected' : '' }}>
                            {{ $serviceCase->value === 'Self Drive' ? 'Lepas Kunci' : 'Dengan Sopir' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Rentals List -->
        <div class="space-y-4">
            @forelse($rentals as $rental)
            @php
            $car = $rental->car;
            $latestPayment = $rental->paymentHistories()->latest()->first();
            $hasIdentityDocs = filled($rental->ktp_path) && filled($rental->selfie_path);
            $receiptLabel = $latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::PAID
                ? ($rental->booking_code ?? ('Booking #'.$rental->id))
                : '-';

            // Determine Status State and Label
            $state = 'dibatalkan';
            $statusLabel = 'Dibatalkan';
            $badgeColor = 'bg-gray-500';

            if ($rental->status === \App\Enums\RentalStatus::ONGOING) {
            $state = 'aktif';
            $statusLabel = 'Aktif / Lunas';
            $badgeColor = 'bg-[#1E50DD]';
            } elseif ($rental->status === \App\Enums\RentalStatus::RETURNED) {
            $state = 'selesai';
            $statusLabel = 'Selesai';
            $badgeColor = 'bg-[#10B981]';
            } elseif ($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION) {
            if ($rental->verification_status === \App\Enums\VerificationStatus::VERIFIED) {
            $state = 'need_pay';
            $statusLabel = 'Menunggu Pembayaran';
            $badgeColor = 'bg-[#F59E0B]';
            } elseif (! $hasIdentityDocs) {
            $state = 'waiting_docs';
            $statusLabel = 'Menunggu Kelengkapan Data Penyewa';
            $badgeColor = 'bg-sky-500';
            } else {
            $state = 'verifying';
            $statusLabel = 'Menunggu Verifikasi Data Penyewa';
            $badgeColor = 'bg-amber-500';
            }
            } elseif ($rental->status === \App\Enums\RentalStatus::PREPAID) {
            $state = 'pending_pay';
            $statusLabel = 'Menunggu Pembayaran';
            $badgeColor = 'bg-[#F59E0B]';
            } elseif ($rental->status === \App\Enums\RentalStatus::CANCELLED) {
            $state = 'dibatalkan';
            $statusLabel = 'Dibatalkan';
            $badgeColor = 'bg-red-600';
            } elseif ($rental->status === \App\Enums\RentalStatus::EXPIRED) {
            $state = 'expired';
            $statusLabel = 'Waktu Habis';
            $badgeColor = 'bg-gray-600';
            }

            $rentalDays = max(1, \Carbon\Carbon::parse($rental->start_date)->diffInDays(\Carbon\Carbon::parse($rental->end_date)));
            @endphp
            <article
                class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm shadow-slate-200/50 px-4 py-4 md:px-5 md:py-5 transition hover:shadow-md hover:shadow-slate-200/60">
                <div class="flex flex-col xl:flex-row xl:items-center gap-4 xl:gap-5">
                    <div class="flex items-center gap-4 min-w-0 xl:w-[30%]">
                        <div class="relative w-28 h-20 md:w-32 md:h-24 rounded-2xl overflow-hidden bg-slate-100 shrink-0">
                            <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}"
                                alt="{{ $car->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                <span>Resi: {{ $receiptLabel }}</span>
                            </div>
                            <h3 class="mt-1 text-base md:text-lg font-bold tracking-tight text-slate-900 truncate">
                                {{ $car->brand }} {{ $car->name }}
                            </h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600">
                                    {{ $car->vehicle_type->label() }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600">
                                    {{ $rental->type->value === 'Self Drive' ? 'Lepas Kunci' : 'Dengan Sopir' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="xl:w-[24%]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Periode Sewa</p>
                        <p class="mt-1.5 text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($rental->start_date)->translatedFormat('d M') }} -
                            {{ \Carbon\Carbon::parse($rental->end_date)->translatedFormat('d M Y') }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">{{ $rentalDays }} hari</p>
                    </div>

                    <div class="xl:w-[18%]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Status</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center rounded-full {{ $badgeColor }} text-white text-[10px] font-bold px-3 py-1.5 uppercase tracking-wider shadow-sm">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="xl:w-[16%]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Total Biaya</p>
                        @if($state === 'waiting_docs')
                        <p class="mt-1.5 text-sm font-bold text-sky-600">Lengkapi Data</p>
                        @elseif($state === 'verifying')
                        <p class="mt-1.5 text-sm font-bold text-amber-500">Ditinjau</p>
                        @elseif($state === 'need_pay' || $state === 'pending_pay')
                        <p class="mt-1.5 text-sm font-bold text-yellow-600">Menunggu Pembayaran</p>
                        @else
                        <p class="mt-1.5 text-lg font-extrabold tracking-tight text-slate-900">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</p>
                        @endif
                    </div>

                    <div class="xl:w-[12%] xl:ml-auto">
                        <div class="flex flex-wrap xl:justify-end gap-2">
                        @if($state === 'need_pay')
                        <form action="{{ route('booking.pay', $rental->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-[#F59E0B] hover:bg-yellow-600 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition cursor-pointer">
                                Lanjutkan Pembayaran
                            </button>
                        </form>
                        @elseif($state === 'pending_pay')
                        @php
                        $detailUrl = route('booking.detail', array_merge(['rental' => $rental->id],
                        $latestPayment?->provider_order_id ? [
                        'order_id' => $latestPayment->provider_order_id,
                        'status_code' => 201,
                        'transaction_status' => 'pending',
                        'action' => 'back',
                        ] : []));
                        $payUrl = $latestPayment?->redirect_url ?? route('booking.simulate-payment', ['rental_id' =>
                        $rental->id]);
                        @endphp
                        <div class="flex flex-col w-full gap-2">
                            <a href="{{ $detailUrl }}"
                                class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 font-bold text-xs py-2.5 px-4 rounded-xl transition cursor-pointer text-center">
                                Lihat Detail
                            </a>
                            <a href="{{ $payUrl }}"
                                class="bg-[#F59E0B] hover:bg-yellow-600 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition cursor-pointer text-center">
                                Bayar Sekarang
                            </a>
                        </div>
                        @elseif($state === 'verifying')
                        <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}"
                            class="border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Lihat Status
                        </a>
                        @elseif($state === 'waiting_docs')
                        <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}"
                            class="border border-sky-200 hover:bg-sky-50 text-sky-700 font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Lengkapi Data
                        </a>
                        @elseif($state === 'selesai')
                        @if(!$rental->review)
                        <a href="{{ route('booking.review', ['rental' => $rental->id]) }}"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Tulis Ulasan
                        </a>
                        @else
                        <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}"
                            class="border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Detail
                        </a>
                        @endif
                        <a href="{{ route('car-detail', ['car' => $car->id]) }}"
                            class="bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Pesan Lagi
                        </a>
                        @elseif($state === 'aktif')
                        <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}"
                            class="bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Lihat Detail
                        </a>
                        @else
                        <a href="{{ route('booking.detail', ['rental' => $rental->id]) }}"
                            class="border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl transition">
                            Detail
                        </a>
                        @endif
                        </div>
                    </div>
                </div>
            </article>
            @empty
            <div
                class="col-span-full bg-white border border-gray-100 rounded-2xl p-16 text-center text-gray-500 shadow-sm">
                <p class="font-medium text-base">Tidak ada riwayat pemesanan yang sesuai.</p>
                <p class="text-xs text-gray-400 mt-1">Coba gunakan filter pencarian yang berbeda.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($rentals->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 pt-6 mt-8 gap-4">
            <span class="text-xs text-gray-500">
                Menampilkan {{ $rentals->firstItem() ?? 0 }} - {{ $rentals->lastItem() ?? 0 }} dari
                {{ $rentals->total() }}
            </span>
            <div class="flex items-center space-x-1.5">
                <!-- Previous Page -->
                @if ($rentals->onFirstPage())
                <span
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs cursor-not-allowed select-none">&lt;</span>
                @else
                <a href="{{ $rentals->previousPageUrl() }}"
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 text-xs transition">&lt;</a>
                @endif

                <!-- Page Links -->
                @foreach ($rentals->getUrlRange(1, $rentals->lastPage()) as $page => $url)
                @if ($page == $rentals->currentPage())
                <span
                    class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs select-none">{{ $page }}</span>
                @else
                <a href="{{ $url }}"
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 text-xs transition">{{ $page }}</a>
                @endif
                @endforeach

                <!-- Next Page -->
                @if ($rentals->hasMorePages())
                <a href="{{ $rentals->nextPageUrl() }}"
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-blue-50 text-xs transition">&gt;</a>
                @else
                <span
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs cursor-not-allowed select-none">&gt;</span>
                @endif
            </div>
        </div>
        @endif

    </main>

    <x-frontliner.footer />

</body>

</html>
