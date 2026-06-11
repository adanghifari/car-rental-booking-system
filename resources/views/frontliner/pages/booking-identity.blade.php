<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Data Penyewa - HD RENTAL CAR</title>
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
                <a href="{{ route('booking.start') }}?car_id={{ $car->id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&service_type={{ $service_type }}"
                    class="group flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-[#0B3C9B] transition-all duration-300 hover:shadow-md hover:shadow-blue-200"
                    title="Kembali ke Detail Pemesanan">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
                <div class="w-px h-6 bg-gray-200"></div>
                <span class="text-2xl font-bold text-blue-600">HD RENTAL CAR</span>
            </div>

            <!-- Navigation - Hidden on mobile -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('frontliner') }}" class="text-gray-700 hover:text-blue-600 transition">Beranda</a>
                <a href="{{ route('armada') }}" class="text-[#0B3C9B] border-b-2 border-[#0B3C9B] pb-1 font-semibold">Armada</a>
                <a href="{{ route('frontliner') }}#pesanan-saya" class="text-gray-700 hover:text-blue-600 transition">Pesanan Saya</a>
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

                <!-- Step 3: Verifikasi Data Penyewa (Active) -->
                <div class="flex items-center gap-2 text-blue-600">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">3</span>
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
            <span class="text-gray-800 font-medium">Verifikasi Data Penyewa</span>
        </nav>

        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Verifikasi Data Penyewa</h1>
        <p class="text-sm text-gray-600 mb-4 font-medium">Silakan unggah foto KTP dan selfie Anda untuk keperluan verifikasi identitas penyewa.</p>
        <div class="mb-8 rounded-2xl border border-blue-100 bg-blue-50/80 p-4 text-sm text-blue-900">
            Mobil ini telah diamankan sementara untuk Anda selama proses verifikasi. Lengkapi data penyewa untuk melanjutkan ke pembayaran.
        </div>

        <!-- Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            
            <!-- Left Side: Upload Form (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                
                @if (session('error'))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm">
                        ⚠️ {{ session('error') }}
                    </div>
                @endif

                @if (!empty($error_message))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm">
                        ⚠️ {{ $error_message }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm space-y-1">
                        <p>⚠️ Terdapat kesalahan pada data yang diunggah:</p>
                        <ul class="list-disc list-inside font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('booking.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                    <input type="hidden" name="start_date" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" value="{{ $end_date }}">
                    <input type="hidden" name="service_type" value="{{ $service_type }}">

                    <!-- Dokumen Identitas KTP -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                🪪
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Dokumen Identitas (KTP)</h2>
                        </div>

                        <!-- Drag and Drop Area -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl bg-gray-50/30 transition duration-200 p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('ktp-input').click()">
                            
                            <input type="file" name="ktp" id="ktp-input" accept="image/*" class="hidden" onchange="handleKtpSelected(this)">
                            
                            <div id="ktp-placeholder" class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-xl">
                                    📤
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Klik untuk unggah KTP</p>
                                    <p class="text-xs text-gray-400 mt-1">Format JPG, PNG (Maks. 5MB) - Wajib</p>
                                </div>
                            </div>

                            <!-- Uploaded Preview -->
                            <div id="ktp-preview-container" class="hidden space-y-3 w-full">
                                <div class="max-w-[160px] mx-auto rounded-lg overflow-hidden border border-gray-200 bg-white">
                                    <img id="ktp-preview-image" src="" alt="Pratinjau KTP" class="w-full object-contain max-h-24">
                                </div>
                                <div>
                                    <p id="ktp-filename" class="text-xs font-bold text-gray-800 max-w-[250px] truncate mx-auto"></p>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full uppercase">KTP Dipilih</span>
                                </div>
                            </div>
                        </div>

                        <!-- OCR Result Loading / Results Mock -->
                        <div id="ocr-loader" class="hidden p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center gap-3 justify-center text-xs font-bold text-blue-700">
                            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengekstrak data identitas (OCR)...
                        </div>

                        <div id="ocr-result" class="hidden p-4 bg-emerald-50 border border-emerald-100 rounded-xl space-y-2 text-xs">
                            <p class="font-bold text-emerald-800 text-center uppercase tracking-wider text-[10px]">✓ Hasil Ekstraksi OCR KTP Berhasil</p>
                            <div class="grid grid-cols-3 gap-1 pt-1.5 border-t border-emerald-100/50">
                                <span class="text-emerald-600/70 font-semibold">NIK:</span>
                                <span class="col-span-2 font-bold text-emerald-900">3273123456789001</span>
                            </div>
                            <div class="grid grid-cols-3 gap-1">
                                <span class="text-emerald-600/70 font-semibold">Nama:</span>
                                <span class="col-span-2 font-bold text-emerald-900 uppercase">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-1">
                                <span class="text-emerald-600/70 font-semibold">Status:</span>
                                <span class="col-span-2 font-bold text-emerald-900 uppercase">Valid & Terverifikasi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Foto Selfie (Verifikasi Wajah) -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                📸
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Verifikasi Wajah (Selfie)</h2>
                        </div>

                        <!-- Drag and Drop Area -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl bg-gray-50/30 transition duration-200 p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('selfie-input').click()">
                            
                            <input type="file" name="selfie" id="selfie-input" accept="image/*" class="hidden" onchange="handleSelfieSelected(this)">
                            
                            <div id="selfie-placeholder" class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-xl">
                                    🤳
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Klik untuk unggah Selfie</p>
                                    <p class="text-xs text-gray-400 mt-1">Format JPG, PNG (Maks. 5MB) - Wajib</p>
                                </div>
                            </div>

                            <!-- Uploaded Preview -->
                            <div id="selfie-preview-container" class="hidden space-y-3 w-full">
                                <div class="max-w-[160px] mx-auto rounded-lg overflow-hidden border border-gray-200 bg-white">
                                    <img id="selfie-preview-image" src="" alt="Pratinjau Selfie" class="w-full object-contain max-h-24">
                                </div>
                                <div>
                                    <p id="selfie-filename" class="text-xs font-bold text-gray-800 max-w-[250px] truncate mx-auto"></p>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full uppercase">Selfie Dipilih</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-4">
                        <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.99] text-white font-bold py-4 rounded-2xl text-sm transition-all duration-200 shadow-xl shadow-blue-200 flex items-center justify-center gap-2">
                            Kirim Verifikasi
                        </button>
                        <p class="text-[11px] text-[#475569] leading-relaxed bg-[#F1F5F9] p-3 rounded-xl border border-gray-200">
                            ℹ️ <strong>Informasi Privasi:</strong> KTP dan selfie hanya digunakan untuk proses verifikasi identitas penyewa. Jika pengajuan ditolak atau dibatalkan, data verifikasi akan dihapus sesuai kebijakan sistem.
                        </p>
                    </div>
                </form>

                <a href="{{ route('booking.start') }}?car_id={{ $car->id }}&start_date={{ $start_date }}&end_date={{ $end_date }}&service_type={{ $service_type }}"
                   class="mt-4 w-full bg-red-600 hover:bg-red-700 active:scale-[0.99] text-white font-bold py-4 rounded-2xl text-sm transition-all duration-200 shadow-xl shadow-red-100 flex items-center justify-center gap-2">
                    ← Kembali
                </a>
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
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500">
        <p>&copy; 2026 HD RENTAL CAR. Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>

    <script>
        function handleKtpSelected(input) {
            const file = input.files[0];
            const placeholder = document.getElementById('ktp-placeholder');
            const preview = document.getElementById('ktp-preview-container');
            const previewImage = document.getElementById('ktp-preview-image');
            const filenameEl = document.getElementById('ktp-filename');
            const ocrLoader = document.getElementById('ocr-loader');
            const ocrResult = document.getElementById('ocr-result');

            if (file) {
                // Show Image Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    placeholder.classList.add('hidden');
                    preview.classList.remove('hidden');
                    
                    // Mock OCR Extraction loading and feedback
                    ocrResult.classList.add('hidden');
                    ocrLoader.classList.remove('hidden');
                    setTimeout(() => {
                        ocrLoader.classList.add('hidden');
                        ocrResult.classList.remove('hidden');
                    }, 1200);
                }
                reader.readAsDataURL(file);
                filenameEl.textContent = file.name;
            } else {
                placeholder.classList.remove('hidden');
                preview.classList.add('hidden');
                ocrLoader.classList.add('hidden');
                ocrResult.classList.add('hidden');
            }
        }

        function handleSelfieSelected(input) {
            const file = input.files[0];
            const placeholder = document.getElementById('selfie-placeholder');
            const preview = document.getElementById('selfie-preview-container');
            const previewImage = document.getElementById('selfie-preview-image');
            const filenameEl = document.getElementById('selfie-filename');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    placeholder.classList.add('hidden');
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
                filenameEl.textContent = file.name;
            } else {
                placeholder.classList.remove('hidden');
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
