<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Pemesanan - HD RENTAL CAR</title>
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
            <!-- Logo with Back Button -->
            <div class="flex items-center gap-3">
                <a href="{{ route('booking.start') }}?car_id={{ $car->id }}&start_date={{ $booking['start_date'] }}&end_date={{ $booking['end_date'] }}&service_type={{ $booking['service_type'] }}"
                    class="group flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-[#0B3C9B] transition-all duration-300 hover:shadow-md hover:shadow-blue-200"
                    title="Kembali ke Detail Pemesanan">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
                <div class="w-px h-6 bg-gray-200"></div>
                <span class="text-2xl font-bold text-blue-600">HD RENTAL CAR</span>
            </div>

            <!-- Navigation -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('frontliner') }}" class="text-gray-700 hover:text-blue-600 transition">Beranda</a>
                <a href="{{ route('armada') }}" class="text-[#0B3C9B] border-b-2 border-[#0B3C9B] pb-1 font-semibold">Armada</a>
                <a href="{{ route('frontliner') }}#pesanan-saya" class="text-gray-700 hover:text-blue-600 transition">Pesanan Saya</a>
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
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-8 w-full">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-gray-500 mb-6 gap-2 items-center">
            <a href="{{ route('armada') }}" class="hover:text-blue-600">Fleet</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('car-detail', $car->id) }}" class="hover:text-blue-600">{{ $car->name }}</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">Ringkasan</span>
        </nav>

        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Ringkasan Pemesanan</h1>
        <p class="text-sm text-gray-600 mb-8 font-medium">Periksa kembali detail pesanan Anda dan lakukan verifikasi wajah untuk menyelesaikan pembayaran.</p>

        <!-- Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            
            <!-- Left Side: Pricing Summary & Verification (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                
                @if (session('error'))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('booking.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Detail & Rincian Harga -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                📊
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Rincian Pembayaran</h2>
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-3.5 text-sm">
                            <div class="flex justify-between text-gray-500">
                                <span>Durasi Sewa</span>
                                <span class="font-bold text-gray-800">{{ $days }} Hari</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Biaya Sewa Mobil (Rp {{ number_format($car->daily_rate, 0, ',', '.') }} x {{ $days }})</span>
                                <span class="font-semibold text-gray-800">Rp {{ number_format($rentCost, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Biaya Layanan & Asuransi</span>
                                <span class="font-semibold text-gray-800">Rp 100.000</span>
                            </div>
                            @if($booking['service_type'] === 'with_driver')
                                <div class="flex justify-between text-gray-500">
                                    <span>Biaya Driver (Rp 150.000 x {{ $days }})</span>
                                    <span class="font-semibold text-gray-800">Rp {{ number_format($driverCost, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-4 border-t border-dashed border-gray-200 text-base font-extrabold text-gray-900">
                                <span>Total Harga</span>
                                <span class="text-[#0B3C9B] text-xl">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- KTP Preview -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pratinjau KTP Anda</h3>
                        <div class="rounded-xl overflow-hidden max-h-48 border border-gray-100 bg-gray-50 flex items-center justify-center">
                            <img src="{{ asset('storage/' . $booking['ktp_path']) }}" alt="KTP Preview" class="w-full max-h-48 object-contain">
                        </div>
                    </div>

                    <!-- Selfie Face Verification -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                📸
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Verifikasi Wajah (Selfie)</h2>
                                <p class="text-xs text-gray-500 mt-0.5">Unggah foto selfie Anda untuk dicocokkan secara instan dengan KTP.</p>
                            </div>
                        </div>

                        <!-- Selfie Upload Area -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl bg-gray-50/30 transition duration-200 p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('selfie-input').click()">
                            
                            <input type="file" name="selfie" id="selfie-input" accept="image/*" class="hidden" required onchange="handleSelfieSelected(this)">
                            
                            <div id="selfie-placeholder" class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-xl">
                                    🤳
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Klik untuk unggah Selfie</p>
                                    <p class="text-xs text-gray-400 mt-1">Format JPG, PNG (Maks. 5MB)</p>
                                </div>
                            </div>

                            <!-- Uploaded Preview -->
                            <div id="selfie-preview-container" class="hidden space-y-3">
                                <div class="w-16 h-16 rounded-xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-2xl">
                                    ✅
                                </div>
                                <div>
                                    <p id="selfie-filename" class="text-sm font-bold text-gray-800 max-w-[250px] truncate mx-auto"></p>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full uppercase">Selfie Dipilih</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-bold py-4 rounded-2xl text-sm transition-all duration-200 shadow-xl shadow-blue-200 flex items-center justify-center gap-2">
                            💳 Konfirmasi & Bayar Sekarang
                        </button>
                        <p class="text-[10px] text-center text-gray-400">
                            Dengan mengklik tombol di atas, Anda menyetujui Syarat & Ketentuan sewa kendaraan kami.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Side: Car Details Sticky Card (2 Cols) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-md p-6 space-y-6 lg:sticky lg:top-24">
                    <div class="rounded-2xl overflow-hidden h-48 bg-gray-50">
                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1000&q=80' }}"
                             alt="{{ $car->name }}" class="w-full h-full object-cover">
                    </div>

                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">{{ $car->brand }} {{ $car->name }}</h2>
                        <span class="inline-block bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider mt-2">
                            {{ $booking['service_type'] === 'with_driver' ? '🤵 Dengan Driver' : '🔑 Lepas Kunci' }}
                        </span>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Jadwal Sewa:</span>
                            <span class="font-bold text-gray-800 text-right">
                                {{ \Carbon\Carbon::parse($booking['start_date'])->translatedFormat('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($booking['end_date'])->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Kapasitas:</span>
                            <span class="font-bold text-gray-800">{{ $car->seat_count }} Kursi</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Transmisi:</span>
                            <span class="font-bold text-gray-800 uppercase">{{ $car->transmission->value }}</span>
                        </div>
                    </div>

                    <!-- Change Order Button -->
                    <a href="{{ route('booking.start') }}?car_id={{ $car->id }}&start_date={{ $booking['start_date'] }}&end_date={{ $booking['end_date'] }}&service_type={{ $booking['service_type'] }}"
                       class="flex items-center justify-center gap-2 w-full py-3 border border-gray-200 hover:bg-gray-50 text-gray-600 hover:text-gray-900 rounded-xl text-xs font-bold transition duration-200">
                        ✍️ Ubah Identitas / KTP
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500">
        <p>&copy; 2026 HD RENTAL CAR. Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>

    <script>
        function handleSelfieSelected(input) {
            const file = input.files[0];
            const placeholder = document.getElementById('selfie-placeholder');
            const preview = document.getElementById('selfie-preview-container');
            const filenameEl = document.getElementById('selfie-filename');

            if (file) {
                filenameEl.textContent = file.name;
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
            } else {
                placeholder.classList.remove('hidden');
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
