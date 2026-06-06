<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian Armada - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F8F9FC;
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }
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

    <main class="max-w-[1400px] mx-auto px-4 md:px-8 py-10 w-full flex-grow">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
            
            <!-- Sidebar Filter Pencarian -->
            <aside class="w-full bg-white p-6 rounded-2xl border border-gray-100 shadow-sm md:sticky md:top-24 max-h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar">
                <h3 class="text-base font-bold text-gray-900 mb-6">Filter Pencarian</h3>
                
                <form id="filterForm" method="GET" action="{{ route('search-result') }}">
                    @if(request('start_date'))
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    @endif

                    <div class="mb-6">
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Tipe Mobil</h4>
                        <div class="space-y-3">
                            @foreach(\App\Enums\VehicleType::cases() as $type)
                                @php
                                    $checked = request('types') && in_array($type->value, (array)request('types'));
                                @endphp
                                <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer hover:text-[#0B3C9B] transition">
                                    <input type="checkbox" name="types[]" value="{{ $type->value }}" {{ $checked ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                                    <span>{{ $type->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6 border-t pt-5 border-gray-50">
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Harga Per Hari Maksimal</h4>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-semibold">Rp</span>
                            </div>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" min="0" step="50000" class="focus:ring-[#0B3C9B] focus:border-[#0B3C9B] block w-full pl-9 pr-3 py-2.5 text-xs font-semibold border-gray-200 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 transition" placeholder="Contoh: 500000" onkeypress="if(event.key === 'Enter') { this.form.submit(); }" onblur="this.form.submit()">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5">Tekan Enter atau klik di luar untuk menerapkan.</p>
                    </div>

                    <div class="mb-6 border-t pt-5 border-gray-50">
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Kapasitas</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="capacity" value="2" {{ request('capacity') == '2' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                <div class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-[11px] font-semibold text-center hover:border-[#0B3C9B] peer-checked:bg-[#0B3C9B] peer-checked:text-white peer-checked:border-[#0B3C9B] transition">2 Kursi</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="capacity" value="4-5" {{ request('capacity') == '4-5' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                <div class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-[11px] font-semibold text-center hover:border-[#0B3C9B] peer-checked:bg-[#0B3C9B] peer-checked:text-white peer-checked:border-[#0B3C9B] transition">4-5 Kursi</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="capacity" value="7" {{ request('capacity') == '7' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                <div class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-[11px] font-semibold text-center hover:border-[#0B3C9B] peer-checked:bg-[#0B3C9B] peer-checked:text-white peer-checked:border-[#0B3C9B] transition">7 Kursi</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="capacity" value="other" {{ request('capacity') == 'other' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                <div class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-[11px] font-semibold text-center hover:border-[#0B3C9B] peer-checked:bg-[#0B3C9B] peer-checked:text-white peer-checked:border-[#0B3C9B] transition">Lainnya</div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6 border-t pt-5 border-gray-50">
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Tipe Layanan</h4>
                        <div class="space-y-3">
                            @php
                                $selfDriveChecked = request('service_types') && in_array('self_drive', (array)request('service_types'));
                                $withDriverChecked = request('service_types') && in_array('with_driver', (array)request('service_types'));
                            @endphp
                            <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer hover:text-[#0B3C9B] transition">
                                <input type="checkbox" name="service_types[]" value="self_drive" {{ $selfDriveChecked ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                                <span>Lepas Kunci</span>
                            </label>
                            <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer hover:text-[#0B3C9B] transition">
                                <input type="checkbox" name="service_types[]" value="with_driver" {{ $withDriverChecked ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                                <span>Dengan Driver</span>
                            </label>
                        </div>
                    </div>
                </form>

                <a href="{{ route('search-result', ['start_date' => request('start_date')]) }}" class="w-full text-center block bg-blue-50 hover:bg-blue-100 text-[#0B3C9B] font-semibold py-3 rounded-xl text-sm transition">
                    Reset Filter
                </a>
            </aside>

            <!-- Main Content Area -->
            <div class="w-full md:col-span-3 space-y-10">
                
                <!-- Banner Kriteria Pencarian -->
                <div class="bg-gradient-to-r from-[#0B3C9B] to-[#1E40AF] rounded-2xl p-6 md:p-8 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                    <div>
                        <nav class="text-xs text-blue-200 mb-2 flex items-center space-x-2">
                            <a href="{{ auth()->check() ? route('frontliner') : route('home') }}" class="hover:underline">Beranda</a>
                            <span>/</span>
                            <span class="text-white font-medium">Hasil Pencarian</span>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Hasil Pencarian Armada</h1>
                        <p class="text-sm text-blue-100 font-light flex flex-wrap items-center gap-2">
                            @if(request('start_date'))
                                <span>Tanggal Mulai: {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }}</span>
                            @endif
                            @if(request('start_date') && request('max_price'))
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span>
                            @endif
                            @if(request('max_price'))
                                <span>Budget Maksimal: Rp {{ number_format(request('max_price'), 0, ',', '.') }}</span>
                            @endif
                            @if(!request('start_date') && !request('max_price'))
                                <span>Menampilkan semua armada tersedia</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ auth()->check() ? route('frontliner') : route('home') }}" class="bg-white text-[#0B3C9B] hover:bg-blue-50 transition px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center space-x-2 shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        <span>Ubah Detail</span>
                    </a>
                </div>

                <!-- Rekomendasi Utama -->
                <section>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Rekomendasi untuk Kamu</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        @if(isset($cars) && $cars->count() > 0)
                            @php $firstCar = $cars->first(); @endphp
                            <div class="lg:col-span-2 bg-slate-900 rounded-2xl overflow-hidden relative group min-h-[300px] flex flex-col justify-end p-6 shadow-sm">
                                <img src="{{ $firstCar->image ? asset('storage/' . $firstCar->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $firstCar->name }}" class="absolute inset-0 w-full h-full object-cover opacity-70 group-hover:scale-105 transition duration-500">
                                <div class="absolute top-6 right-6 bg-black/40 backdrop-blur-sm border border-white/10 rounded-xl p-3 text-right text-white">
                                    <p class="text-[10px] text-gray-300 uppercase tracking-wider">Mulai Dari</p>
                                    <p class="text-lg font-bold">Rp {{ number_format($firstCar->daily_rate, 0, ',', '.') }}<span class="text-xs font-light text-gray-300">/hari</span></p>
                                </div>
                                <div class="relative z-10 text-white">
                                    <span class="inline-block bg-[#10B981] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide mb-2">PILIHAN UTAMA</span>
                                    <h3 class="text-2xl font-bold mb-2">{{ $firstCar->name }}</h3>
                                    <div class="flex items-center space-x-4 text-xs text-gray-300 font-medium">
                                        <span>🚗 {{ $firstCar->brand }} - {{ $firstCar->vehicle_type->label() }}</span>
                                        <span class="text-yellow-400">★ <span class="text-white">{{ $firstCar->rating ?? '4.8' }}</span></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="lg:col-span-2 bg-slate-900 rounded-2xl overflow-hidden relative min-h-[300px] flex flex-col justify-center items-center p-6 text-center text-white">
                                <p class="text-gray-400">Tidak ada rekomendasi mobil tersedia.</p>
                            </div>
                        @endif

                        @if(isset($cars) && $cars->count() > 1)
                            @php $secondCar = $cars->skip(1)->first(); @endphp
                            <div class="bg-slate-900 rounded-2xl overflow-hidden relative group min-h-[300px] flex flex-col justify-end p-6 shadow-sm">
                                <img src="{{ $secondCar->image ? asset('storage/' . $secondCar->image) : 'https://images.unsplash.com/photo-1520050206274-a1ae446cb3cc?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $secondCar->name }}" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition duration-500">
                                <div class="relative z-10 text-white">
                                    <h3 class="text-lg font-bold mb-0.5">{{ $secondCar->name }}</h3>
                                    <p class="text-[11px] text-gray-300 mb-3">{{ $secondCar->brand }} - {{ $secondCar->vehicle_type->label() }}</p>
                                    <p class="text-lg font-bold">Rp {{ number_format($secondCar->daily_rate, 0, ',', '.') }}<span class="text-xs font-light text-gray-300">/hari</span></p>
                                </div>
                            </div>
                        @else
                            <div class="bg-slate-800 rounded-2xl overflow-hidden relative min-h-[300px] flex flex-col justify-center items-center p-6 text-center text-white">
                                <p class="text-gray-400">Armada alternatif tidak tersedia.</p>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Semua Hasil Pencarian -->
                <section>
                    <div class="flex justify-between items-center pb-4 border-b border-gray-200 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Hasil Armada</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                        @forelse($cars as $car)
                            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition">
                                <div>
                                    <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                                        <span class="absolute top-3 left-3 bg-[#10B981] text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase">
                                            {{ $car->status->value ?? $car->status }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900">{{ $car->name }}</h4>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">{{ $car->brand }} - {{ $car->vehicle_type->label() }}</p>
                                        </div>
                                        <span class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center">
                                            ★ {{ $car->rating ?? '4.8' }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">
                                        <span>👥 {{ $car->seat_count }} Penumpang</span>
                                        <span>⚙️ {{ $car->transmission->label() }}</span>
                                        <span>⚡ {{ $car->cc }} cc</span>
                                        <span>📅 Th {{ $car->year }}</span>
                                    </div>
                                </div>
                                <div class="border-t pt-3 border-gray-50 space-y-2.5">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($car->daily_rate, 0, ',', '.') }}<span class="text-[10px] font-normal text-gray-400">/hari</span></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('car-detail', ['car' => $car->id]) }}" class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 text-center py-2 rounded-xl text-xs font-bold transition inline-block">Detail</a>
                                        <a href="{{ route('booking.start') }}" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white text-center py-2 rounded-xl text-xs font-bold transition inline-block">Pesan</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <p class="text-gray-500 font-medium text-base mb-1">Armada Tidak Ditemukan</p>
                                <p class="text-gray-400 text-xs">Silakan sesuaikan filter tanggal sewa atau budget maksimal Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

            </div>

        </div>
    </main>

    <x-frontliner.footer />

</body>
</html>
