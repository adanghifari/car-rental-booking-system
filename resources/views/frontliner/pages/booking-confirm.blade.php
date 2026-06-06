<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Detail Pemesanan - HD RENTAL CAR</title>
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
                <a href="{{ route('car-detail', $car->id) }}?start_date={{ $start_date }}&end_date={{ $end_date }}&service_type={{ $service_type }}"
                    class="group flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-[#0B3C9B] transition-all duration-300 hover:shadow-md hover:shadow-blue-200"
                    title="Kembali ke Detail Mobil">
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
            <span class="text-gray-800 font-medium">Pemesanan</span>
        </nav>

        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Konfirmasi Detail Pemesanan</h1>
        <p class="text-sm text-gray-600 mb-8">Lengkapi detail perjalanan Anda untuk memastikan layanan presisi kami.</p>

        <!-- Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            
            <!-- Left Side: Order Form (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                
                @if (session('error'))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl text-xs font-semibold">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('booking.confirm') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

                    <!-- Dokumen Identitas -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                                🪪
                            </div>
                            <h2 class="text-base font-bold text-gray-900 font-semibold">Dokumen Identitas (KTP)</h2>
                        </div>

                        <!-- Drag and Drop Area -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl bg-gray-50/30 transition duration-200 p-8 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('ktp-input').click()">
                            
                            <input type="file" name="ktp" id="ktp-input" accept="image/*" class="hidden" required onchange="handleKtpSelected(this)">
                            
                            <div id="ktp-placeholder" class="space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-xl">
                                    📤
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Klik untuk unggah KTP</p>
                                    <p class="text-xs text-gray-400 mt-1">Format JPG, PNG (Maks. 5MB)</p>
                                </div>
                            </div>

                            <!-- Uploaded Preview -->
                            <div id="ktp-preview-container" class="hidden space-y-3">
                                <div class="w-16 h-16 rounded-xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-2xl">
                                    📄
                                </div>
                                <div>
                                    <p id="ktp-filename" class="text-sm font-bold text-gray-800 max-w-[250px] truncate mx-auto"></p>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full uppercase">KTP Dipilih</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-4">
                        <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.99] text-white font-bold py-4 rounded-2xl text-sm transition-all duration-200 shadow-xl shadow-blue-200 flex items-center justify-center gap-2">
                            Lanjutkan ke Ringkasan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </button>
                        <p class="text-xs text-center text-gray-400 flex items-center justify-center gap-1.5">
                            🔒 Pemesanan Anda diproses dengan enkripsi keamanan tinggi.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Side: Car Info Card (2 Cols) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-md p-6 space-y-6 lg:sticky lg:top-24">
                    <!-- Image -->
                    <div class="rounded-2xl overflow-hidden h-48 bg-gray-50 relative">
                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1000&q=80' }}"
                             alt="{{ $car->name }}" class="w-full h-full object-cover">
                        <span class="absolute top-4 left-4 bg-emerald-500 text-white text-[9px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
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

                    <div class="grid grid-cols-2 gap-4 border-y border-gray-100 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🛋️</span>
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Kapasitas</p>
                                <p class="text-xs font-bold text-gray-800">{{ $car->seat_count }} Kursi</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg">⚙️</span>
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Akselerasi</p>
                                <p class="text-xs font-bold text-gray-800">2.8s (0-100)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Price Estimate -->
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Estimasi Biaya</p>
                        <p class="text-2xl font-extrabold text-[#0B3C9B] mt-1">
                            Rp {{ number_format($car->daily_rate, 0, ',', '.') }}
                            <span class="text-xs font-medium text-gray-400">/hari</span>
                        </p>
                    </div>

                    <!-- Inclusions -->
                    <div class="bg-gray-50/70 p-4 rounded-xl flex gap-3 text-xs text-gray-600 leading-relaxed border border-gray-100">
                        <span class="text-blue-500 font-bold text-base mt-0.5">✓</span>
                        <p>Termasuk Asuransi All-Risk Platinum, Layanan Penjemputan 24/7, dan Pembersihan Interior Premium sebelum pengiriman.</p>
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
        <p>&copy; 2026 HD RENTAL CAR. Hak Cipta Dilindungi Undang-Undang.</p>
    </footer>

    <script>
        function handleKtpSelected(input) {
            const file = input.files[0];
            const placeholder = document.getElementById('ktp-placeholder');
            const preview = document.getElementById('ktp-preview-container');
            const filenameEl = document.getElementById('ktp-filename');

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
