<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - HD RENTAL CAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    @php
        $start = \Carbon\Carbon::parse($rental->start_date);
        $end = \Carbon\Carbon::parse($rental->end_date);
        $days = max(1, $start->diffInDays($end));
        $rentCost = $car->daily_rate * $days;
        $driverCost = ($rental->type === \App\Enums\RentalType::WITH_DRIVER) ? 150000 * $days : 0;
        $serviceCost = 100000;
        $totalPrice = $rental->total_price;

        $targetTime = null;
        if ($rental->status === \App\Enums\RentalStatus::PREPAID) {
            $targetTime = $rental->prepaid_expires_at;
        } elseif ($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::VERIFIED) {
            $targetTime = $rental->verified_at ? $rental->verified_at->addHours(4) : null;
        }
        $hasIdentityDocs = filled($rental->ktp_path) && filled($rental->selfie_path);
    @endphp

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-8 w-full space-y-6">
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

                <!-- Step 2: Verifikasi Booking -->
                <div class="flex items-center gap-2 text-blue-600">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">2</span>
                    <span class="hidden sm:inline">Verifikasi Booking</span>
                    <span class="sm:hidden">Booking</span>
                </div>
                <div class="flex-grow mx-4 h-0.5 bg-blue-600"></div>

                <!-- Step 3: Verifikasi Data Penyewa -->
                @php
                    $step3Active = in_array($rental->status, [\App\Enums\RentalStatus::PENDING_VERIFICATION, \App\Enums\RentalStatus::PREPAID, \App\Enums\RentalStatus::ONGOING, \App\Enums\RentalStatus::RETURNED]);
                    $step3Completed = in_array($rental->status, [\App\Enums\RentalStatus::PREPAID, \App\Enums\RentalStatus::ONGOING, \App\Enums\RentalStatus::RETURNED]) || ($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::VERIFIED);
                @endphp
                <div class="flex items-center gap-2 {{ $step3Active ? 'text-blue-600' : 'text-gray-400' }}">
                    <span class="w-6 h-6 rounded-full {{ $step3Completed ? 'bg-blue-600 text-white' : ($step3Active ? 'border-2 border-blue-600 text-blue-600' : 'bg-gray-200 text-gray-600') }} flex items-center justify-center text-xs">3</span>
                    <span class="hidden sm:inline">Verifikasi Data Penyewa</span>
                    <span class="sm:hidden">Identitas</span>
                </div>
                <div class="flex-grow mx-4 h-0.5 {{ $step3Completed ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

                <!-- Step 4: Pembayaran -->
                @php
                    $step4Active = in_array($rental->status, [\App\Enums\RentalStatus::PREPAID, \App\Enums\RentalStatus::ONGOING, \App\Enums\RentalStatus::RETURNED]) || ($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::VERIFIED);
                    $step4Completed = in_array($rental->status, [\App\Enums\RentalStatus::ONGOING, \App\Enums\RentalStatus::RETURNED]);
                @endphp
                <div class="flex items-center gap-2 {{ $step4Active ? 'text-blue-600' : 'text-gray-400' }}">
                    <span class="w-6 h-6 rounded-full {{ $step4Completed ? 'bg-blue-600 text-white' : ($step4Active ? 'border-2 border-blue-600 text-blue-600' : 'bg-gray-200 text-gray-600') }} flex items-center justify-center text-xs">4</span>
                    <span class="hidden sm:inline">Pembayaran</span>
                    <span class="sm:hidden">Bayar</span>
                </div>
            </div>
        </div>
        
        <!-- Flash Message -->
        @if (session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl text-xs font-semibold shadow-sm flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <!-- Banner Status Pemesanan -->
        @if($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::PENDING && ! $hasIdentityDocs)
            <!-- Booking sementara, belum upload identitas -->
            <div class="bg-sky-500 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        🔒
                    </div>
                    <div>
                        <h2 class="text-base font-bold">Menunggu Kelengkapan Data Penyewa</h2>
                        <p class="text-xs text-sky-50 mt-0.5">Mobil sudah diamankan sementara untuk Anda. Silakan lengkapi KTP dan selfie untuk melanjutkan proses booking.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wider">
                    Data Belum Lengkap
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && ($rental->verification_status === \App\Enums\VerificationStatus::PENDING || $rental->verification_status === \App\Enums\VerificationStatus::NEEDS_REVIEW))
            <!-- Menunggu Review Admin -->
            <div class="bg-amber-500 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ⏱️
                    </div>
                    <div>
                        <h2 class="text-base font-bold">Verifikasi Sedang Ditinjau</h2>
                        <p class="text-xs text-amber-50 mt-0.5">Verifikasi identitas Anda sedang ditinjau. Mobil sementara kami amankan untuk Anda. Pembayaran dapat dilakukan setelah verifikasi disetujui.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wider">
                    Menunggu Verifikasi
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::VERIFIED)
            <!-- Terverifikasi Manual - Menunggu Pembayaran diinisiasi -->
            <div id="countdown-banner" class="bg-[#1E50DD] text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md transition-colors duration-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ⏱️
                    </div>
                    <div>
                        <h2 id="countdown-text" class="text-base font-bold">Verifikasi Identitas Disetujui</h2>
                        <p class="text-xs text-blue-100 mt-0.5">Selesaikan pembayaran sebelum batas waktu habis untuk mengaktifkan rental Anda.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2.5 rounded-full font-bold text-2xl tracking-wider min-w-[100px] text-center" id="countdown-timer">
                    04:00:00
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::PREPAID)
            <!-- Skenario Belum Lunas (Countdown Banner) -->
            <div id="countdown-banner" class="bg-[#1E50DD] text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md transition-colors duration-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ⏱️
                    </div>
                    <div>
                        <h2 id="countdown-text" class="text-base font-bold">Menunggu Pembayaran</h2>
                        <p class="text-xs text-blue-100 mt-0.5">Selesaikan pembayaran sebelum waktu habis untuk menjamin ketersediaan armada.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2.5 rounded-full font-bold text-2xl tracking-wider min-w-[100px] text-center" id="countdown-timer">
                    04:00:00
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::ONGOING)
            <!-- Skenario Lunas / Aktif -->
            <div class="bg-green-600 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ✓
                    </div>
                    <div>
                        <h2 class="text-base font-bold">Pembayaran Sukses</h2>
                        <p class="text-xs text-green-100 mt-0.5">Pesanan Anda telah aktif. Armada siap digunakan sesuai jadwal sewa Anda.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wider">
                    Aktif / Lunas
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::RETURNED)
            <!-- Skenario Selesai -->
            <div class="bg-blue-600 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ✓
                    </div>
                    <div>
                        <h2 class="text-base font-bold">Rental Selesai</h2>
                        <p class="text-xs text-blue-100 mt-0.5">Terima kasih telah menyewa kendaraan di HD RENTAL CAR.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wider">
                    Selesai
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::CANCELLED || $rental->verification_status === \App\Enums\VerificationStatus::REJECTED || $rental->verification_status === \App\Enums\VerificationStatus::CANCELLED)
            <!-- Skenario Dibatalkan -->
            <div class="bg-red-600 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ✕
                    </div>
                    <div>
                        <h2 class="text-base font-bold">Pemesanan Dibatalkan</h2>
                        <p class="text-xs text-red-100 mt-0.5">
                            @if($rental->verification_status === \App\Enums\VerificationStatus::REJECTED)
                                Verifikasi identitas belum dapat disetujui. Pengajuan dibatalkan dan mobil kembali tersedia.
                            @else
                                Pemesanan telah dibatalkan.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wider">
                    Dibatalkan
                </div>
            </div>
        @elseif($rental->status === \App\Enums\RentalStatus::EXPIRED)
            <!-- Skenario Expired -->
            <div class="bg-gray-600 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl">
                        ✕
                    </div>
                    <div>
                        <h2 class="text-base font-bold">Waktu Pembayaran Habis</h2>
                        <p class="text-xs text-gray-100 mt-0.5">Waktu Anda untuk menyelesaikan proses pembayaran telah habis. Mobil kembali tersedia.</p>
                    </div>
                </div>
                <div class="bg-white/20 px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wider">
                    Waktu Habis
                </div>
            </div>
        @endif

        <!-- Grid Detail Pesanan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left Side: Detail Kendaraan (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 border-l-4 border-blue-600 pl-3 mb-6">Detail Kendaraan</h2>
                    
                    <div class="relative rounded-2xl overflow-hidden bg-gray-950 h-[320px] mb-6 flex items-center justify-center">
                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1200&q=80' }}" 
                             alt="{{ $car->name }}" 
                             class="w-full h-full object-cover">
                        <span class="absolute top-4 right-4 bg-[#10B981] text-white text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wide">
                            {{ $car->vehicle_type->label() }}
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end border-b pb-6 border-gray-100 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Model Kendaraan</span>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $car->brand }} {{ $car->name }}</h3>
                            
                            <div class="flex items-center gap-2 mt-4">
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-lg">
                                    🚗 {{ $car->vehicle_type->label() }}
                                </span>
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-lg">
                                    ⚙️ {{ $car->transmission->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="sm:text-right border-l sm:border-l-0 sm:pl-0 pl-4 border-gray-200">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Durasi Sewa</span>
                            <span class="text-3xl font-extrabold text-[#0B3C9B] mt-1 block">{{ $days }} <span class="text-lg font-medium text-gray-500">Hari</span></span>
                            <span class="text-xs text-gray-500 mt-1 block">{{ \Carbon\Carbon::parse($rental->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($rental->end_date)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Keunggulan Layanan -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <div class="space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">🛡️</div>
                        <h4 class="text-xs font-bold text-gray-900">Asuransi All-Risk</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed">Perlindungan menyeluruh selama masa sewa kendaraan.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">👤</div>
                        <h4 class="text-xs font-bold text-gray-900">Layanan Concierge</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed">Tim bantuan 24/7 siap melayani kebutuhan perjalanan Anda.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">⛽</div>
                        <h4 class="text-xs font-bold text-gray-900">Full Tank</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed">Unit dikirimkan dengan kondisi bahan bakar/daya penuh.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Rincian Biaya (1 Col) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 border-l-4 border-blue-600 pl-3 mb-6">Rincian Biaya</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-start text-sm">
                            <div>
                                <p class="font-semibold text-gray-800">Harga Sewa</p>
                                <p class="text-[10px] text-gray-400 font-medium">{{ $days }} Hari x Rp {{ number_format($car->daily_rate, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-bold text-gray-900">Rp {{ number_format($rentCost, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-start text-sm">
                            <div>
                                <p class="font-semibold text-gray-800">Layanan & Asuransi</p>
                                <p class="text-[10px] text-gray-400 font-medium">Proteksi premium & biaya platform</p>
                            </div>
                            <span class="font-bold text-gray-900">Rp {{ number_format($serviceCost, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-start text-sm pb-4 border-b border-gray-100">
                            <div>
                                <p class="font-semibold text-gray-800">Biaya Supir</p>
                                <p class="text-[10px] text-gray-400 font-medium">Layanan profesional concierge</p>
                            </div>
                            <span class="font-bold text-gray-900">Rp {{ number_format($driverCost, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-4 flex flex-col gap-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Keseluruhan</span>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-black text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                <span class="bg-[#10B981]/10 text-[#059669] text-[9px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">Final Price</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mt-6">
                    @if($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::VERIFIED)
                        <form action="{{ route('booking.pay', $rental->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-center block bg-[#1E50DD] hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-md flex items-center justify-center space-x-2 text-sm">
                                <span>💳</span>
                                <span>Lanjutkan Pembayaran</span>
                            </button>
                        </form>
                    @elseif($rental->status === \App\Enums\RentalStatus::PREPAID)
                        @php
                            $payUrl = $payment?->redirect_url ?? route('booking.simulate-payment', ['rental_id' => $rental->id]);
                        @endphp
                        <a id="pay-button" href="{{ $payUrl }}" class="w-full text-center block bg-[#1E50DD] hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-md flex items-center justify-center space-x-2 text-sm">
                            <span>💳</span>
                            <span>Bayar Sekarang via Midtrans</span>
                        </a>
                    @elseif($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && $rental->verification_status === \App\Enums\VerificationStatus::PENDING && ! $hasIdentityDocs)
                        <button type="button" disabled class="w-full text-center block bg-sky-100 text-sky-600 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                            🔒 Menunggu Kelengkapan Data Penyewa
                        </button>
                    @elseif($rental->status === \App\Enums\RentalStatus::PENDING_VERIFICATION && ($rental->verification_status === \App\Enums\VerificationStatus::PENDING || $rental->verification_status === \App\Enums\VerificationStatus::NEEDS_REVIEW))
                        <button type="button" disabled class="w-full text-center block bg-gray-100 text-gray-400 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                            ⏱️ Menunggu Verifikasi Disetujui
                        </button>
                    @elseif($rental->status === \App\Enums\RentalStatus::ONGOING)
                        <button type="button" disabled class="w-full text-center block bg-emerald-100 text-emerald-600 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                            ✓ Pembayaran Lunas / Rental Aktif
                        </button>
                    @elseif($rental->status === \App\Enums\RentalStatus::RETURNED)
                        <button type="button" disabled class="w-full text-center block bg-blue-100 text-blue-600 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                            ✓ Rental Selesai
                        </button>
                    @elseif($rental->status === \App\Enums\RentalStatus::CANCELLED)
                        <button type="button" disabled class="w-full text-center block bg-red-100 text-red-500 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                            ✕ Dibatalkan
                        </button>
                    @elseif($rental->status === \App\Enums\RentalStatus::EXPIRED)
                        <button type="button" disabled class="w-full text-center block bg-gray-100 text-gray-400 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                            ✕ Waktu Habis
                        </button>
                    @endif

                    @if(in_array($rental->status, [\App\Enums\RentalStatus::PENDING_VERIFICATION, \App\Enums\RentalStatus::PREPAID]))
                        <form action="{{ route('booking.cancel', $rental->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini? Data identitas KTP dan selfie Anda akan dihapus demi privasi.');">
                            @csrf
                            <button type="submit" class="w-full text-center block border border-red-500 hover:bg-red-50 text-red-500 font-bold py-2.5 px-6 rounded-xl transition text-xs">
                                ✕ Batalkan & Hapus Data
                            </button>
                        </form>
                    @endif
                    <p class="text-center text-[10px] text-gray-400 font-semibold uppercase tracking-wider">
                        Secure Payment Encrypted By Rental Mobil Vault
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-xs">
            <p>&copy; 2026 HD Rental Car. All rights reserved.</p>
        </div>
    </footer>

    @if($targetTime)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const endTime = new Date("{{ $targetTime->toIso8601String() }}");

                function updateCountdown() {
                    const now = new Date();
                    const diff = endTime - now;

                    if (diff <= 0) {
                        const timerEl = document.getElementById('countdown-timer');
                        if (timerEl) timerEl.innerText = "00:00:00";
                        const banner = document.getElementById('countdown-banner');
                        if (banner) {
                            banner.classList.add('bg-red-600');
                            banner.classList.remove('bg-[#1E50DD]');
                        }
                        const text = document.getElementById('countdown-text');
                        if (text) {
                            text.innerText = "Waktu Pembayaran Habis";
                        }
                        const payBtn = document.getElementById('pay-button');
                        if (payBtn) {
                            payBtn.removeAttribute('href');
                            payBtn.style.pointerEvents = 'none';
                            payBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-60');
                            payBtn.classList.remove('bg-[#1E50DD]', 'hover:bg-blue-700');
                            payBtn.innerText = "Waktu Pembayaran Habis";
                        }
                        // Refresh page to transition state in DB
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        clearInterval(timerInterval);
                        return;
                    }

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    const hoursStr = String(hours).padStart(2, '0');
                    const minutesStr = String(minutes).padStart(2, '0');
                    const secondsStr = String(seconds).padStart(2, '0');

                    const timerEl = document.getElementById('countdown-timer');
                    if (timerEl) {
                        timerEl.innerText = `${hoursStr}:${minutesStr}:${secondsStr}`;
                    }
                }

                const timerInterval = setInterval(updateCountdown, 1000);
                updateCountdown();
            });
        </script>
    @endif
</body>
</html>
