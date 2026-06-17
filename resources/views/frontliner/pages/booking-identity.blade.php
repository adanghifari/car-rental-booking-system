<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Data Penyewa - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
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
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (!empty($error_message))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $error_message }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm space-y-2">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p>Terdapat kesalahan pada data yang diunggah:</p>
                        </div>
                        <ul class="list-disc list-inside font-medium ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="booking-identity-form" action="{{ route('booking.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                    <input type="hidden" name="start_date" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" value="{{ $end_date }}">
                    <input type="hidden" name="service_type" value="{{ $service_type }}">

                    <!-- Dokumen Identitas KTP -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5.5 h-5.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm-1.25 6.125c0-1.38-1.12-2.5-2.5-2.5s-2.5 1.12-2.5 2.5v1.25h5V15.5z" />
                                </svg>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Dokumen Identitas (KTP)</h2>
                        </div>

                        <!-- Drag and Drop Area -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl bg-gray-50/30 transition duration-200 p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('ktp-input').click()">
                            
                            <input type="file" name="ktp" id="ktp-input" accept="image/*" class="hidden" onchange="handleKtpSelected(this)">
                            
                            <div id="ktp-placeholder" class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
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

                        <!-- OCR Status -->
                        <div id="ocr-loader" class="hidden p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center gap-3 justify-center text-xs font-bold text-blue-700">
                            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengekstrak data identitas (OCR)...
                        </div>

                        <div id="ocr-result" class="hidden p-4 bg-amber-50 border border-amber-100 rounded-xl text-xs text-amber-900">
                            <p class="font-bold uppercase tracking-wider text-[10px] mb-1">Pratinjau OCR</p>
                            <p class="leading-relaxed">Data identitas akan dicek saat tombol <strong>Kirim Verifikasi</strong> ditekan. Status ini bukan hasil verifikasi final.</p>
                        </div>
                    </div>

                    <!-- Foto Selfie (Verifikasi Wajah) -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5.5 h-5.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Verifikasi Wajah (Selfie)</h2>
                        </div>

                        <!-- Drag and Drop Area -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl bg-gray-50/30 transition duration-200 p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('selfie-input').click()">
                            
                            <input type="file" name="selfie" id="selfie-input" accept="image/*" class="hidden" onchange="handleSelfieSelected(this)">
                            
                            <div id="selfie-placeholder" class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-6 15h9M12 18.75h.008v.008H12v-.008zM12 6a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
                                    </svg>
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
                        <div id="client-validation-error" class="hidden p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold shadow-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Mohon unggah KTP dan selfie terlebih dahulu sebelum mengirim verifikasi.</span>
                        </div>
                        <button id="submit-verification-button" type="button" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.99] text-white font-bold py-4 rounded-2xl text-sm transition-all duration-200 shadow-xl shadow-blue-200 flex items-center justify-center gap-2">
                            Kirim Verifikasi
                        </button>
                        <p class="text-[11px] text-[#475569] leading-relaxed bg-[#F1F5F9] p-3 rounded-xl border border-gray-200 flex items-start gap-1.5">
                            <svg class="w-4 h-4 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75 0 111.063.852l-.708 2.836a.75 0 001.063.852l.041-.028M12 9h.008v.008H12V9zm9 3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Informasi Privasi:</strong> KTP dan selfie hanya digunakan untuk proses verifikasi identitas penyewa. Jika pengajuan ditolak atau dibatalkan, data verifikasi akan dihapus sesuai kebijakan sistem.</span>
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
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            <span>Full Electric Performance & Premium Comfort</span>
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
        <p>&copy; 2026 MD CAR RENTAL. Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>

    <script>
        const bookingForm = document.getElementById('booking-identity-form');
        const submitButton = document.getElementById('submit-verification-button');
        const clientValidationError = document.getElementById('client-validation-error');
        const ktpInput = document.getElementById('ktp-input');
        const selfieInput = document.getElementById('selfie-input');

        function submitBookingIdentityForm() {
            const hasKtp = ktpInput && ktpInput.files && ktpInput.files.length > 0;
            const hasSelfie = selfieInput && selfieInput.files && selfieInput.files.length > 0;

            if (!hasKtp || !hasSelfie) {
                clientValidationError.classList.remove('hidden');
                if (!hasKtp) {
                    ktpInput.focus({ preventScroll: true });
                } else {
                    selfieInput.focus({ preventScroll: true });
                }
                return;
            }

            clientValidationError.classList.add('hidden');
            submitButton.disabled = true;
            submitButton.classList.add('opacity-75', 'cursor-not-allowed');
            bookingForm.submit();
        }

        submitButton.addEventListener('click', submitBookingIdentityForm);

        function handleKtpSelected(input) {
            const file = input.files[0];
            const placeholder = document.getElementById('ktp-placeholder');
            const preview = document.getElementById('ktp-preview-container');
            const previewImage = document.getElementById('ktp-preview-image');
            const filenameEl = document.getElementById('ktp-filename');
            const ocrLoader = document.getElementById('ocr-loader');
            const ocrResult = document.getElementById('ocr-result');

            if (file) {
                clientValidationError.classList.add('hidden');
                // Show Image Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    placeholder.classList.add('hidden');
                    preview.classList.remove('hidden');
                    
                    // Show a neutral OCR status, not a success state.
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
                clientValidationError.classList.add('hidden');
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
