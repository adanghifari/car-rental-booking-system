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

    <x-frontliner.navbar />

    <main class="max-w-[1400px] mx-auto px-4 md:px-8 py-10 w-full flex-grow">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
            
            <!-- Sidebar Filter Pencarian -->
            <aside class="w-full bg-white p-6 rounded-2xl border border-gray-100 shadow-sm md:sticky md:top-24 max-h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar">
                <h3 class="text-base font-bold text-gray-900 mb-6">Filter Pencarian</h3>
                
                <form id="filterForm" method="GET" action="{{ route('search-result') }}">
                    @if(request('start_date'))
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    @endif
                    @if(request('max_price'))
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
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
                            @if(request('start_date') && request('end_date'))
                                <span>Waktu rental: {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') }}</span>
                            @elseif(request('start_date'))
                                <span>Waktu rental: {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }}</span>
                            @endif
                            @if((request('start_date') || request('end_date')) && request('max_price'))
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span>
                            @endif
                            @if(request('max_price'))
                                <span>Budget Maksimal: Rp {{ number_format(request('max_price'), 0, ',', '.') }}</span>
                            @endif
                        @if(!($hasActiveFilters ?? false))
                                <span>Menampilkan semua armada tersedia</span>
                            @endif
                        </p>
                    </div>
                    <button type="button" id="toggle-detail-filter" class="bg-white text-[#0B3C9B] hover:bg-blue-50 transition px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center space-x-2 shadow-sm shrink-0 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5.25h18M3 12h18M3 18.75h18" />
                        </svg>
                        <span>Ubah Filter</span>
                    </button>
                </div>

                <div id="detail-filter-panel" class="hidden overflow-hidden">
                    <div class="mt-4 bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                        @php
                            $today = now()->toDateString();
                        @endphp
                        <form id="detailFilterForm" method="GET" action="{{ route('search-result') }}" class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] gap-4 items-end">
                            @foreach((array) request('types') as $selectedType)
                                <input type="hidden" name="types[]" value="{{ $selectedType }}">
                            @endforeach
                            @if(request('capacity'))
                                <input type="hidden" name="capacity" value="{{ request('capacity') }}">
                            @endif
                            @foreach((array) request('service_types') as $serviceType)
                                <input type="hidden" name="service_types[]" value="{{ $serviceType }}">
                            @endforeach

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" min="{{ $today }}"
                                    oninput="if(this.form.end_date.value && this.form.end_date.value < this.value){this.form.end_date.value=this.value} this.form.end_date.min=this.value;"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tanggal Selesai</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" min="{{ request('start_date', $today) }}"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Budget Harian Maksimal</label>
                                <div class="relative rounded-xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <span class="text-gray-400 text-xs font-semibold">Rp</span>
                                    </div>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" step="50000"
                                        class="focus:ring-[#0B3C9B] focus:border-[#0B3C9B] block w-full pl-9 pr-3 py-3 text-sm font-semibold border-gray-200 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 transition"
                                        placeholder="Contoh: 500000">
                                </div>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center justify-center bg-[#0B3C9B] hover:bg-[#082D76] text-white font-semibold px-5 py-3 rounded-xl text-sm transition cursor-pointer">
                                Terapkan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Rekomendasi Utama -->
                <section>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Rekomendasi untuk Kamu</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        @if(isset($recommendedCars) && $recommendedCars->count() > 0)
                            @php $firstCar = $recommendedCars->first(); @endphp
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
                                        <span class="text-yellow-400">★ <span class="text-white">{{ number_format($firstCar->average_rating, 1) }}</span></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm min-h-[300px] flex flex-col justify-center items-center p-8 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-4">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h3m5-10H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2z" />
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-slate-700">Tidak ada rekomendasi mobil tersedia.</p>
                                <p class="text-sm text-slate-400 mt-2">Coba ubah tanggal sewa, budget, atau filter kendaraan.</p>
                            </div>
                        @endif

                        @if(isset($recommendedCars) && $recommendedCars->count() > 1)
                            @php $secondCar = $recommendedCars->skip(1)->first(); @endphp
                            <div class="bg-slate-900 rounded-2xl overflow-hidden relative group min-h-[300px] flex flex-col justify-end p-6 shadow-sm">
                                <img src="{{ $secondCar->image ? asset('storage/' . $secondCar->image) : 'https://images.unsplash.com/photo-1520050206274-a1ae446cb3cc?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $secondCar->name }}" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition duration-500">
                                <div class="relative z-10 text-white">
                                    <h3 class="text-lg font-bold mb-0.5">{{ $secondCar->name }}</h3>
                                    <p class="text-[11px] text-gray-300 mb-3">{{ $secondCar->brand }} - {{ $secondCar->vehicle_type->label() }}</p>
                                    <p class="text-lg font-bold">Rp {{ number_format($secondCar->daily_rate, 0, ',', '.') }}<span class="text-xs font-light text-gray-300">/hari</span></p>
                                </div>
                            </div>
                        @else
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm min-h-[300px] flex flex-col justify-center items-center p-8 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-4">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 11-6.364-6.364 4.5 4.5 0 016.364 6.364z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 22a8.5 8.5 0 118-4.4"></path>
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-slate-700">Armada alternatif tidak tersedia.</p>
                                <p class="text-sm text-slate-400 mt-2">Belum ada unit lain yang cocok dengan filter ini.</p>
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
                                        <div class="flex flex-col items-end gap-1 shrink-0">
                                            <span class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center">
                                                ★ {{ number_format($car->average_rating, 1) }}
                                            </span>
                                            <button type="button" 
                                                onclick="toggleFavorite({{ $car->id }}, event)"
                                                data-car-id="{{ $car->id }}"
                                                class="favorite-btn text-slate-800 hover:text-red-600 transition-colors duration-200 cursor-pointer focus:outline-none p-1"
                                                title="Tambah ke Favorit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 heart-icon transition-transform duration-200 active:scale-75">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span>{{ $car->seat_count }} Penumpang</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $car->transmission->label() }}</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span>{{ number_format($car->cc) }} cc</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Th {{ $car->year }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="border-t pt-3 border-gray-50 space-y-2.5">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($car->daily_rate, 0, ',', '.') }}<span class="text-[10px] font-normal text-gray-400">/hari</span></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('car-detail', ['car' => $car->id]) }}" class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 text-center py-2 rounded-xl text-xs font-bold transition inline-block">Detail</a>
                                        <button type="button" onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: '{{ $car->status->value ?? $car->status }}', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white text-center py-2 rounded-xl text-xs font-bold transition cursor-pointer">Pesan</button>
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
    <x-frontliner.booking-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleDetailFilterButton = document.getElementById('toggle-detail-filter');
            const detailFilterPanel = document.getElementById('detail-filter-panel');

            if (toggleDetailFilterButton && detailFilterPanel) {
                toggleDetailFilterButton.addEventListener('click', function() {
                    if (detailFilterPanel.classList.contains('hidden')) {
                        detailFilterPanel.classList.remove('hidden');
                        detailFilterPanel.style.maxHeight = '0px';
                        detailFilterPanel.style.opacity = '0';
                        detailFilterPanel.style.transition = 'max-height 0.3s ease, opacity 0.25s ease';

                        requestAnimationFrame(() => {
                            detailFilterPanel.style.maxHeight = detailFilterPanel.scrollHeight + 'px';
                            detailFilterPanel.style.opacity = '1';
                        });
                    } else {
                        detailFilterPanel.style.maxHeight = detailFilterPanel.scrollHeight + 'px';
                        detailFilterPanel.style.opacity = '1';

                        requestAnimationFrame(() => {
                            detailFilterPanel.style.maxHeight = '0px';
                            detailFilterPanel.style.opacity = '0';
                        });

                        setTimeout(() => {
                            detailFilterPanel.classList.add('hidden');
                        }, 300);
                    }
                });
            }

            const userId = '{{ auth()->id() }}';
            const isGuest = !userId;
            const storageKey = 'favorites_' + (userId || 'guest');

            // Load favorites from localStorage
            let favorites = [];
            try {
                favorites = JSON.parse(localStorage.getItem(storageKey)) || [];
            } catch (e) {
                favorites = [];
            }

            // Initialize hearts visual state
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                const carId = parseInt(btn.getAttribute('data-car-id'));
                const svg = btn.querySelector('.heart-icon');
                
                if (favorites.includes(carId)) {
                    svg.setAttribute('fill', 'currentColor');
                    btn.classList.remove('text-slate-800');
                    btn.classList.add('text-red-600');
                } else {
                    svg.setAttribute('fill', 'none');
                    btn.classList.remove('text-red-600');
                    btn.classList.add('text-slate-800');
                }
            });

            // Toggle favorite function
            window.toggleFavorite = function(carId, event) {
                if (event) event.stopPropagation();

                if (isGuest) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                const btn = document.querySelector(`.favorite-btn[data-car-id="${carId}"]`);
                if (!btn) return;

                const svg = btn.querySelector('.heart-icon');
                const index = favorites.indexOf(carId);

                if (index > -1) {
                    // Remove
                    favorites.splice(index, 1);
                    svg.setAttribute('fill', 'none');
                    btn.classList.remove('text-red-600');
                    btn.classList.add('text-slate-800');
                } else {
                    // Add
                    favorites.push(carId);
                    svg.setAttribute('fill', 'currentColor');
                    btn.classList.remove('text-slate-800');
                    btn.classList.add('text-red-600');
                    
                    showSuccessPopup("Berhasil menambahkan ke favorite");
                }

                localStorage.setItem(storageKey, JSON.stringify(favorites));
            };
        });
    </script>

    <x-frontliner.success-popup />

</body>
</html>
