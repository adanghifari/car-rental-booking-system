<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail {{ $car->name }} - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

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
                     <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center flex flex-col items-center justify-center">
                         <div class="flex items-center gap-1 mb-1 justify-center">
                             <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                             </svg>
                             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Transmisi</span>
                         </div>
                         <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->transmission->label() }}</p>
                     </div>
                     <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center flex flex-col items-center justify-center">
                         <div class="flex items-center gap-1 mb-1 justify-center">
                             <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                             </svg>
                             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kapasitas</span>
                         </div>
                         <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->seat_count }} Kursi</p>
                     </div>
                     <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center flex flex-col items-center justify-center">
                         <div class="flex items-center gap-1 mb-1 justify-center">
                             <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                             </svg>
                             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mesin</span>
                         </div>
                         <p class="text-sm font-bold text-[#0B3C9B]">{{ $car->cc ? number_format($car->cc, 0, ',', '.') : '-' }} cc</p>
                     </div>
                     <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center flex flex-col items-center justify-center">
                         <div class="flex items-center gap-1 mb-1 justify-center">
                             <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                             </svg>
                             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tahun</span>
                         </div>
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
                </div>

                <form id="detail-booking-form" action="{{ route('booking.start') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                    <input type="hidden" name="service_type" id="service_type" value="self_drive">

                    <!-- Dates Picker -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Mulai Sewa</label>
                            <input type="date" name="start_date" id="rent_start_date" onchange="calculatePrice()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]/20 focus:border-[#0B3C9B]/30 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Selesai Sewa</label>
                            <input type="date" name="end_date" id="rent_end_date" onchange="calculatePrice()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]/20 focus:border-[#0B3C9B]/30 transition">
                        </div>
                    </div>

                    <div id="detail-availability-banner" class="mb-1 rounded-2xl border px-4 py-3 hidden">
                        <div class="flex items-start gap-3">
                            <div id="detail-availability-dot" class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-500 shrink-0"></div>
                            <div>
                                <p id="detail-availability-label" class="text-sm font-bold text-slate-900">Pilih tanggal untuk cek ketersediaan</p>
                                <p id="detail-availability-message" class="mt-1 text-xs text-slate-600">Status mobil akan menyesuaikan dengan tanggal yang Anda pilih.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Layanan Picker -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Pilihan Layanan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" id="btn-self-drive" onclick="selectService('self_drive')" 
                                class="border bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 transition-all duration-200 {{ !$car->self_drive_available ? 'opacity-40 cursor-not-allowed' : '' }}">
                                <svg class="w-5 h-5 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2a2 2 0 002 2m0 0a2 2 0 01-2 2m0-4a2 2 0 01-2 2m0 0v5a3 3 0 01-3 3H9a3 3 0 01-3-3V9a3 3 0 013-3h6a3 3 0 013 3z" />
                                </svg>
                                <span>Lepas Kunci</span>
                            </button>
                            <button type="button" id="btn-with-driver" onclick="selectService('with_driver')" 
                                class="border bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 transition-all duration-200 {{ !$car->driver_available ? 'opacity-40 cursor-not-allowed' : '' }}">
                                <svg class="w-5 h-5 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Dengan Sopir</span>
                            </button>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t border-gray-100 pt-4 space-y-2.5 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span id="display-days">Sewa 0 Hari</span>
                            <span id="display-rent-cost" class="font-semibold text-gray-800">Rp -</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Biaya Layanan & Asuransi</span>
                            <span id="display-service-cost" class="font-semibold text-gray-800">Rp -</span>
                        </div>
                        <div class="flex justify-between items-center pt-2.5 border-t border-dashed border-gray-200 text-sm font-bold text-gray-900">
                            <span>Total Harga</span>
                            <span id="display-total-cost" class="text-[#0B3C9B] text-base">Rp -</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.98] text-white font-bold py-3.5 rounded-xl text-xs transition-all duration-200 shadow-lg shadow-blue-200 tracking-wider uppercase">
                        Booking Sekarang
                    </button>
                    <p class="text-[9px] text-center text-gray-400 italic">Pembatalan gratis hingga 24 jam sebelum pengambilan</p>
                </form>
            </aside>

        </div>


        <!-- Customer Reviews -->
        <section class="border-t border-gray-200 pt-10 mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-900">Ulasan Pengguna</h2>
                @if($car->total_reviews > 0)
                    <span class="text-xs font-bold text-amber-500">{{ $car->has_rating ? '★ ' . $car->rating_display : $car->rating_display }} <span class="text-gray-400 font-normal">({{ $car->total_reviews }} Ulasan)</span></span>
                @else
                    <span class="text-xs text-gray-400">Belum ada ulasan</span>
                @endif
            </div>

            <div class="space-y-4">
                @forelse($car->reviews as $review)
                    @php
                        $initials = '';
                        $names = explode(' ', $review->user->name);
                        foreach (array_slice($names, 0, 2) as $n) {
                            $initials .= strtoupper(substr($n, 0, 1));
                        }
                    @endphp
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold text-xs">
                                    {{ $initials ?: 'U' }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">{{ $review->user->name }}</h4>
                                    <p class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-amber-400 text-xs font-bold">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $review->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-600 text-xs leading-relaxed italic">
                            "{{ $review->comment ?? 'Pengguna tidak menulis komentar.' }}"
                        </p>
                    </div>
                @empty
                    <div class="text-center py-8 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 text-xs">
                        Belum ada ulasan untuk mobil ini. Jadilah yang pertama memberikan ulasan!
                    </div>
                @endforelse
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
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ $similarCar->seat_count }} Kursi</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $similarCar->transmission->label() }}</span>
                            </span>
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

        // ── Sidebar Booking Form Logic ──
        const dailyRate = {{ $car->daily_rate }};
        const selfDriveAvailable = {{ $car->self_drive_available ? 'true' : 'false' }};
        const driverAvailable = {{ $car->driver_available ? 'true' : 'false' }};
        const availabilityUrl = "{{ route('booking.availability') }}";
        
        let selectedService = selfDriveAvailable ? 'self_drive' : (driverAvailable ? 'with_driver' : '');
        
        function selectService(type) {
            if (type === 'self_drive' && !selfDriveAvailable) return;
            if (type === 'with_driver' && !driverAvailable) return;
            
            selectedService = type;
            document.getElementById('service_type').value = type;
            
            const selfBtn = document.getElementById('btn-self-drive');
            const driverBtn = document.getElementById('btn-with-driver');
            const activeClasses = ['border-[#0B3C9B]', 'bg-white', 'text-[#0B3C9B]', 'font-bold', 'border-2', 'shadow-sm'];
            const inactiveClasses = ['border-gray-200', 'bg-gray-50', 'text-gray-500', 'font-medium'];
            
            if (type === 'self_drive') {
                selfBtn?.classList.remove(...inactiveClasses);
                selfBtn?.classList.add(...activeClasses);
                driverBtn?.classList.remove(...activeClasses);
                driverBtn?.classList.add(...inactiveClasses);
            } else {
                driverBtn?.classList.remove(...inactiveClasses);
                driverBtn?.classList.add(...activeClasses);
                selfBtn?.classList.remove(...activeClasses);
                selfBtn?.classList.add(...inactiveClasses);
            }
            calculatePrice();
        }
        
        function calculatePrice() {
            const startVal = document.getElementById('rent_start_date').value;
            const endVal = document.getElementById('rent_end_date').value;
            if (!startVal || !endVal) return;
            
            const start = new Date(startVal);
            const end = new Date(endVal);
            let days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            if (days < 1) days = 1;
            
            const rentCost = dailyRate * days;
            let driverCost = selectedService === 'with_driver' ? 150000 * days : 0;
            const serviceCost = 100000 + driverCost;
            const totalCost = rentCost + serviceCost;
            const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');
            
            document.getElementById('display-days').textContent = `Sewa ${days} Hari`;
            document.getElementById('display-rent-cost').textContent = fmt(rentCost);
            document.getElementById('display-service-cost').textContent = fmt(serviceCost);
            document.getElementById('display-total-cost').textContent = fmt(totalCost);
        }

        function renderDetailAvailability(state) {
            const banner = document.getElementById('detail-availability-banner');
            const dot = document.getElementById('detail-availability-dot');
            const label = document.getElementById('detail-availability-label');
            const message = document.getElementById('detail-availability-message');
            const submitButton = document.querySelector('#detail-booking-form button[type="submit"]');

            if (!banner || !dot || !label || !message || !submitButton) return;

            banner.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50/70', 'border-amber-200', 'bg-amber-50/70', 'border-rose-200', 'bg-rose-50/70');
            dot.classList.remove('bg-emerald-500', 'bg-amber-500', 'bg-rose-500');

            const tone = state.tone || 'emerald';
            if (tone === 'rose') {
                banner.classList.add('border-rose-200', 'bg-rose-50/70');
                dot.classList.add('bg-rose-500');
            } else if (tone === 'amber') {
                banner.classList.add('border-amber-200', 'bg-amber-50/70');
                dot.classList.add('bg-amber-500');
            } else {
                banner.classList.add('border-emerald-200', 'bg-emerald-50/70');
                dot.classList.add('bg-emerald-500');
            }

            label.textContent = state.label;
            message.textContent = state.message;

            submitButton.disabled = state.available === false;
            submitButton.classList.toggle('opacity-60', state.available === false);
            submitButton.classList.toggle('cursor-not-allowed', state.available === false);
        }

        async function refreshDetailAvailability() {
            const startEl = document.getElementById('rent_start_date');
            const endEl = document.getElementById('rent_end_date');
            if (!startEl?.value || !endEl?.value) return;

            const params = new URLSearchParams({
                car_id: "{{ $car->id }}",
                start_date: startEl.value,
                end_date: endEl.value,
            });

            try {
                const response = await fetch(`${availabilityUrl}?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const payload = await response.json();
                renderDetailAvailability(payload);
            } catch (error) {
                renderDetailAvailability({
                    available: false,
                    tone: 'rose',
                    label: 'Gagal memeriksa ketersediaan',
                    message: 'Silakan coba lagi setelah mengubah tanggal sewa.',
                });
            }
        }

        function syncDetailBookingDates() {
            const startEl = document.getElementById('rent_start_date');
            const endEl = document.getElementById('rent_end_date');
            if (!startEl || !endEl || !startEl.value) return;

            endEl.min = startEl.value;
            if (!endEl.value || endEl.value < startEl.value) {
                endEl.value = startEl.value;
            }

            calculatePrice();
            refreshDetailAvailability();
        }
        
        // Auth check on form submit
        document.getElementById('detail-booking-form').addEventListener('submit', function(e) {
            @guest
                e.preventDefault();
                const urlParams = new URLSearchParams(new FormData(this));
                const dest = "/booking/start?" + urlParams.toString();
                window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(dest);
            @endguest
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const qStart = urlParams.get('start_date');
            const qEnd = urlParams.get('end_date');
            const qService = urlParams.get('service_type');

            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const threeDaysLater = new Date(tomorrow);
            threeDaysLater.setDate(threeDaysLater.getDate() + 3);
            const formatDate = (d) => d.toISOString().split('T')[0];
            
            const startEl = document.getElementById('rent_start_date');
            const endEl = document.getElementById('rent_end_date');
            
            if (startEl) { 
                startEl.value = qStart || formatDate(tomorrow); 
                startEl.min = formatDate(tomorrow); 
                startEl.addEventListener('change', syncDetailBookingDates);
            }
            if (endEl) { 
                endEl.value = qEnd || formatDate(threeDaysLater); 
                endEl.min = formatDate(tomorrow); 
                endEl.addEventListener('change', () => {
                    calculatePrice();
                    refreshDetailAvailability();
                });
            }
            
            if (qService) {
                selectedService = qService;
            }
            syncDetailBookingDates();
            selectService(selectedService);
            refreshDetailAvailability();
        });
    </script>
</body>
</html>
