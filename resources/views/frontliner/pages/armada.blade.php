<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armada Kami - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <main class="max-w-[1400px] mx-auto px-4 md:px-8 py-10 w-full flex-grow">
        
        <div class="w-full space-y-10">
            
            <!-- Banner Kriteria Pencarian -->
            <div class="bg-gradient-to-r from-[#0B3C9B] to-[#1E40AF] rounded-2xl p-6 md:p-8 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                <div>
                    <nav class="text-xs text-blue-200 mb-2 flex items-center space-x-2">
                        <a href="{{ auth()->check() ? route('frontliner') : route('home') }}" class="hover:underline">Beranda</a>
                        <span>/</span>
                        <span class="text-white font-medium">Armada</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Katalog Armada</h1>
                    <p class="text-sm text-blue-100 font-light">
                        Menampilkan seluruh armada mobil yang tersedia untuk perjalanan Anda.
                    </p>
                </div>
            </div>

            <!-- Semua Hasil Pencarian -->
            <section>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Semua Armada</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
                    @forelse($cars as $car)
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition">
                            <div>
                                <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                    <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                                    @if(($car->status->value ?? $car->status) === 'available')
                                        <span class="absolute top-3 left-3 bg-[#10B981] text-white text-[9px] font-bold px-2.5 py-1 rounded uppercase tracking-wider">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="absolute top-3 left-3 bg-[#EF4444] text-white text-[9px] font-bold px-2.5 py-1 rounded uppercase tracking-wider">
                                            Disewa
                                        </span>
                                    @endif
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
                                    <a href="{{ route('car-detail', ['car' => $car->id]) }}" class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 text-center py-2 rounded-xl text-xs font-bold transition inline-block">Selengkapnya..</a>
                                    @if(($car->status->value ?? $car->status) === 'available')
                                        <button type="button" onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: '{{ $car->status->value ?? $car->status }}', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white text-center py-2 rounded-xl text-xs font-bold transition cursor-pointer">Pesan</button>
                                    @else
                                        <button type="button" disabled class="bg-gray-200 text-gray-400 text-center py-2 rounded-xl text-xs font-bold cursor-not-allowed">Pesan</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            <p class="text-gray-500 font-medium text-base mb-1">Armada Tidak Ditemukan</p>
                            <p class="text-gray-400 text-xs">Saat ini belum ada mobil yang terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </main>

    <x-frontliner.footer />
    <x-frontliner.booking-modal />

</body>
</html>
