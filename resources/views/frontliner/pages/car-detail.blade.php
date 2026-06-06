<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail {{ $car->name }} - HD RENTAL CAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    @if(auth()->check())
        <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-blue-600">HD RENTAL CAR</span>
                </div>

                <!-- Navigation - Hidden on mobile -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('frontliner') }}" class="text-gray-700 hover:text-blue-600 transition">Beranda</a>
                    <a href="{{ route('armada') }}" class="text-[#0B3C9B] border-b-2 border-[#0B3C9B] pb-1 font-semibold">Armada</a>
                    <a href="{{ route('frontliner') }}#pesanan-saya" class="text-gray-700 hover:text-blue-600 transition">Pesanan Saya</a>
                    <a href="{{ route('frontliner') }}#testimoni" class="text-gray-700 hover:text-blue-600 transition">Testimoni</a>
                </nav>

                <!-- Right Section - User Profile -->
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button class="relative text-gray-700 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            2
                        </span>
                    </button>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
                        <div class="text-right border-r pr-4 border-gray-100 mr-2">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500">Member</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>

                        <!-- Dropdown Menu -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-blue-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block z-50">
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                    👤 Profil Saya
                                </a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    ⚙️ Pengaturan
                                </a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    💳 Pembayaran
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="border-t">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 rounded-b-lg">
                                        🚪 Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    @else
        <x-frontliner.navbar-non-login />
    @endif

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-10 w-full flex-grow">
        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start mb-12">
            
            <div class="lg:col-span-3 space-y-6">
                <!-- Main Car Photo -->
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm aspect-[16/10]">
                    <img id="main-car-photo" src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1000&q=80' }}" alt="{{ $car->name }} Side" class="w-full h-full object-cover">
                </div>

                <!-- Gallery Thumbnails -->
                @php
                    $thumbnails = [];
                    $thumbnails[] = $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1000&q=80';
                    if (is_array($car->gallery_images)) {
                        foreach ($car->gallery_images as $gImg) {
                            $thumbnails[] = asset('storage/' . $gImg);
                        }
                    }
                    $thumbnails = array_slice($thumbnails, 0, 4);
                @endphp
                <div class="grid grid-cols-{{ count($thumbnails) }} gap-4">
                    @foreach($thumbnails as $index => $thumbUrl)
                        <div onclick="changeMainImage('{{ $thumbUrl }}', this)" class="thumbnail-item bg-white rounded-xl overflow-hidden border {{ $index === 0 ? 'border-2 border-[#0B3C9B]' : 'border-gray-100' }} p-1 cursor-pointer shadow-sm hover:border-[#0B3C9B] transition aspect-[16/10]">
                            <img src="{{ $thumbUrl }}" alt="{{ $car->name }} Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>

                <!-- Title & Description -->
                <div class="pt-4">
                    <span class="inline-block bg-blue-50 text-[#0B3C9B] text-xs font-semibold px-3 py-1 rounded-md mb-3 uppercase tracking-wider">{{ $car->vehicle_type->label() }}</span>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-4">{{ $car->name }}</h1>
                    <p class="text-gray-500 text-sm leading-relaxed font-light text-justify">
                        {{ $car->description ?? 'Nikmati kenyamanan berkendara premium dengan ' . $car->name . '. Unit terawat, bersih, wangi, dan dalam kondisi mesin optimal untuk perjalanan Anda.' }}
                    </p>
                </div>

                <!-- Specifications Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">⚙️ Transmisi</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->transmission->label() }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">👥 Kapasitas</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->seat_count }} Kursi</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">⚡ Mesin</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->cc ? number_format($car->cc, 0, ',', '.') : '-' }} cc</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">📅 Tahun</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->year }}</p>
                    </div>
                </div>
            </div>

            <!-- Sticky Booking Sidebar -->
            <aside class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-md lg:sticky lg:top-24">
                <div class="rounded-xl overflow-hidden h-32 mb-4 bg-gray-100">
                    <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=500&q=80' }}" alt="Car Side Mini" class="w-full h-full object-cover">
                </div>
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tarif Sewa</p>
                        <p class="text-xl font-bold text-[#0B3C9B]">Rp {{ number_format($car->daily_rate, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">/hari</span></p>
                    </div>
                    @if($car->status->value === 'available')
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">Tersedia</span>
                    @else
                        <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">Disewa</span>
                    @endif
                </div>

                <form action="{{ route('booking.start') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                    <input type="hidden" name="service_type" id="service_type" value="self_drive">

                    <!-- Dates Picker -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Mulai Sewa</label>
                            <input type="date" name="start_date" id="rent_start_date" onchange="calculatePrice()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]/20">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Selesai Sewa</label>
                            <input type="date" name="end_date" id="rent_end_date" onchange="calculatePrice()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]/20">
                        </div>
                    </div>

                    <!-- Layanan Picker -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Pilihan Layanan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" id="btn-self-drive" onclick="selectService('self_drive')" 
                                class="border bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 transition {{ !$car->self_drive_available ? 'opacity-40 cursor-not-allowed' : '' }}">
                                <span class="text-base">🔑</span>
                                <span>Lepas Kunci</span>
                            </button>
                            <button type="button" id="btn-with-driver" onclick="selectService('with_driver')" 
                                class="border bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 transition {{ !$car->driver_available ? 'opacity-40 cursor-not-allowed' : '' }}">
                                <span class="text-base">👤</span>
                                <span>Dengan Sopir</span>
                            </button>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t border-gray-100 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span id="display-days">Sewa 3 Hari</span>
                            <span id="display-rent-cost" class="font-semibold text-gray-800">Rp -</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Biaya Layanan & Asuransi</span>
                            <span id="display-service-cost" class="font-semibold text-gray-800">Rp -</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-dashed text-sm font-bold text-gray-900">
                            <span>Total Harga</span>
                            <span id="display-total-cost" class="text-[#0B3C9B] text-base">Rp -</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold py-3.5 rounded-xl text-xs transition shadow-md tracking-wider uppercase">
                        Booking Sekarang
                    </button>
                    <p class="text-[9px] text-center text-gray-400">Pembatalan gratis hingga 24 jam sebelum pengambilan</p>
                </form>
            </aside>
        </div>


        <!-- Customer Reviews -->
        <section class="border-t border-gray-200 pt-10 mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-900">Ulasan Pengguna</h2>
                <span class="text-xs font-bold text-amber-500">★ 4.9 <span class="text-gray-400 font-normal">({{ 10 + ($car->id % 90) }} Ulasan)</span></span>
            </div>

            <div class="space-y-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center space-x-3">
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80" alt="Reza Avatar" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Reza Ardiansyah</h4>
                                <p class="text-[10px] text-gray-400">2 hari yang lalu</p>
                            </div>
                        </div>
                        <div class="text-amber-400 text-xs">★★★★★</div>
                    </div>
                    <p class="text-gray-600 text-xs leading-relaxed italic">
                        "Pengalaman luar biasa dengan rental mobil ini. Unit terawat dengan sangat baik, bersih, dan wanginya luar biasa saat serah terima. Respon admin juga sangat ramah dan cepat."
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center space-x-3">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" alt="Amanda Avatar" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Amanda Wijaya</h4>
                                <p class="text-[10px] text-gray-400">1 minggu yang lalu</p>
                            </div>
                        </div>
                        <div class="text-amber-400 text-xs">★★★★★</div>
                    </div>
                    <p class="text-gray-600 text-xs leading-relaxed italic">
                        "Pelayanan HD Rental Car sangat profesional. Mobil diantar tepat waktu, sopir sopan, dan unitnya benar-benar terasa seperti mobil baru."
                    </p>
                </div>
            </div>
        </section>

        <!-- Similar Cars Section -->
        <section class="border-t border-gray-200 pt-10">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-[10px] font-bold text-[#0B3C9B] uppercase tracking-wider">Rekomendasi</p>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Mobil Serupa</h2>
                </div>
                <a href="{{ route('armada') }}" class="text-[#0B3C9B] font-semibold text-xs flex items-center hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($similarCars as $similarCar)
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                <img src="{{ $similarCar->image ? asset('storage/' . $similarCar->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $similarCar->name }}" class="w-full h-full object-cover">
                                <a href="{{ route('car-detail', ['car' => $similarCar->id]) }}" class="absolute top-3 right-3 w-8 h-8 bg-black/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/10 hover:bg-white hover:text-red-500 transition">🤍</a>
                            </div>
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $similarCar->name }}</h4>
                                    <p class="text-[10px] text-gray-400">{{ $similarCar->brand }} - {{ $similarCar->vehicle_type->label() }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($similarCar->daily_rate, 0, ',', '.') }}<span class="text-[10px] font-normal text-gray-400">/hari</span></span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-[11px] text-gray-500 border-t pt-3 border-gray-50 mt-2">
                            <span>👥 {{ $similarCar->seat_count }} Kursi</span>
                            <span>⚙️ {{ $similarCar->transmission->label() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 bg-white rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-gray-500 text-sm">Tidak ada rekomendasi mobil serupa.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>

    <x-frontliner.footer />

    <script>
        // Gallery Swap Script
        function changeMainImage(url, element) {
            document.getElementById('main-car-photo').src = url;
            document.querySelectorAll('.thumbnail-item').forEach(el => {
                el.classList.remove('border-2', 'border-[#0B3C9B]');
                el.classList.add('border-gray-100');
            });
            element.classList.remove('border-gray-100');
            element.classList.add('border-2', 'border-[#0B3C9B]');
        }

        // Price Calculation Script
        const dailyRate = {{ $car->daily_rate }};
        const selfDriveAvailable = {{ $car->self_drive_available ? 'true' : 'false' }};
        const driverAvailable = {{ $car->driver_available ? 'true' : 'false' }};
        
        let selectedService = '';
        if (selfDriveAvailable) {
            selectedService = 'self_drive';
        } else if (driverAvailable) {
            selectedService = 'with_driver';
        }
        
        function selectService(type) {
            if (type === 'self_drive' && !selfDriveAvailable) return;
            if (type === 'with_driver' && !driverAvailable) return;
            
            selectedService = type;
            document.getElementById('service_type').value = type;
            
            const selfDriveBtn = document.getElementById('btn-self-drive');
            const withDriverBtn = document.getElementById('btn-with-driver');
            
            if (type === 'self_drive') {
                if (selfDriveBtn) {
                    selfDriveBtn.classList.remove('border-gray-200', 'bg-gray-50', 'text-gray-500', 'font-medium');
                    selfDriveBtn.classList.add('border-[#0B3C9B]', 'bg-white', 'text-[#0B3C9B]', 'font-bold', 'border-2');
                }
                if (withDriverBtn) {
                    withDriverBtn.classList.remove('border-[#0B3C9B]', 'bg-white', 'text-[#0B3C9B]', 'font-bold', 'border-2');
                    withDriverBtn.classList.add('border-gray-200', 'bg-gray-50', 'text-gray-500', 'font-medium');
                }
            } else {
                if (withDriverBtn) {
                    withDriverBtn.classList.remove('border-gray-200', 'bg-gray-50', 'text-gray-500', 'font-medium');
                    withDriverBtn.classList.add('border-[#0B3C9B]', 'bg-white', 'text-[#0B3C9B]', 'font-bold', 'border-2');
                }
                if (selfDriveBtn) {
                    selfDriveBtn.classList.remove('border-[#0B3C9B]', 'bg-white', 'text-[#0B3C9B]', 'font-bold', 'border-2');
                    selfDriveBtn.classList.add('border-gray-200', 'bg-gray-50', 'text-gray-500', 'font-medium');
                }
            }
            
            calculatePrice();
        }
        
        function calculatePrice() {
            const startInput = document.getElementById('rent_start_date').value;
            const endInput = document.getElementById('rent_end_date').value;
            
            if (!startInput || !endInput) return;
            
            const start = new Date(startInput);
            const end = new Date(endInput);
            
            let diffTime = end - start;
            let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (days < 1) days = 1;
            
            const rentCost = dailyRate * days;
            
            let driverCost = 0;
            if (selectedService === 'with_driver') {
                driverCost = 150000 * days;
            }
            
            const serviceCost = 100000 + driverCost;
            const totalCost = rentCost + serviceCost;
            
            const formatRupiah = (num) => 'Rp ' + num.toLocaleString('id-ID');
            
            document.getElementById('display-days').textContent = `Sewa ${days} Hari`;
            document.getElementById('display-rent-cost').textContent = formatRupiah(rentCost);
            document.getElementById('display-service-cost').textContent = formatRupiah(serviceCost);
            document.getElementById('display-total-cost').textContent = formatRupiah(totalCost);
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const threeDaysLater = new Date(tomorrow);
            threeDaysLater.setDate(threeDaysLater.getDate() + 3);
            
            const formatDate = (date) => date.toISOString().split('T')[0];
            
            const startEl = document.getElementById('rent_start_date');
            const endEl = document.getElementById('rent_end_date');
            
            if (startEl) {
                startEl.value = formatDate(tomorrow);
                startEl.min = formatDate(tomorrow);
            }
            if (endEl) {
                endEl.value = formatDate(threeDaysLater);
                endEl.min = formatDate(tomorrow);
            }
            
            selectService(selectedService);
        });
    </script>
</body>
</html>
