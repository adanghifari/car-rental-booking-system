<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit Saya - Rental Mobil</title>
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
                        <span class="text-white font-medium">Favorite</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Armada Favorit Anda</h1>
                    <p class="text-sm text-blue-100 font-light">
                        Daftar kendaraan pilihan yang telah Anda simpan untuk perjalanan impian Anda.
                    </p>
                </div>
            </div>

            <!-- Bagian Grid Favorit -->
            <section id="favorites-section">
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Pilihan Saya</h2>
                </div>

                <!-- Grid Mobil -->
                <div id="favorites-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10 hidden">
                    @foreach($cars as $car)
                        <div class="favorite-card bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition duration-300 transform hidden" data-car-id="{{ $car->id }}">
                            <div>
                                <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                    <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
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
                                            class="favorite-btn text-red-600 transition-colors duration-200 cursor-pointer focus:outline-none p-1"
                                            title="Hapus dari Favorit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 heart-icon transition-transform duration-200 active:scale-75 text-red-600">
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
                                    <a href="{{ route('car-detail', ['car' => $car->id]) }}" class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 text-center py-2 rounded-xl text-xs font-bold transition inline-block">Selengkapnya..</a>
                                    @if(($car->status->value ?? $car->status) === 'available')
                                        <button type="button" onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: '{{ $car->status->value ?? $car->status }}', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white text-center py-2 rounded-xl text-xs font-bold transition cursor-pointer">Pesan</button>
                                    @else
                                        <button type="button" disabled class="bg-gray-200 text-gray-400 text-center py-2 rounded-xl text-xs font-bold cursor-not-allowed">Pesan</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm max-w-2xl mx-auto my-10 px-6">
                    <div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Mobil Favorit</h3>
                    <p class="text-slate-500 text-sm max-w-md mx-auto mb-8 leading-relaxed">
                        Jelajahi armada kami dan temukan berbagai pilihan kendaraan terbaik untuk menemani perjalanan Anda.
                    </p>
                    <a href="{{ route('armada') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#0B3C9B] hover:bg-[#082D76] text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md shadow-blue-900/10 hover:shadow-lg hover:shadow-blue-900/20">
                        Cari Kendaraan
                    </a>
                </div>

            </section>

        </div>
    </main>

    <x-frontliner.footer />
    <x-frontliner.booking-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = '{{ auth()->id() }}';
            const storageKey = 'favorites_' + (userId || 'guest');

            // Load favorites from localStorage
            let favorites = [];
            try {
                favorites = JSON.parse(localStorage.getItem(storageKey)) || [];
            } catch (e) {
                favorites = [];
            }

            const grid = document.getElementById('favorites-grid');
            const emptyState = document.getElementById('empty-state');

            function checkEmptyState() {
                const visibleCards = document.querySelectorAll('.favorite-card:not(.hidden)');
                if (visibleCards.length === 0) {
                    grid.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                } else {
                    grid.classList.remove('hidden');
                    emptyState.classList.add('hidden');
                }
            }

            // Show favorited cars
            document.querySelectorAll('.favorite-card').forEach(card => {
                const carId = parseInt(card.getAttribute('data-car-id'));
                if (favorites.includes(carId)) {
                    card.classList.remove('hidden');
                }
            });

            checkEmptyState();

            // Toggle favorite function (Remove from favorites page)
            window.toggleFavorite = function(carId, event) {
                if (event) event.stopPropagation();

                const index = favorites.indexOf(carId);
                if (index > -1) {
                    favorites.splice(index, 1);
                }
                localStorage.setItem(storageKey, JSON.stringify(favorites));

                // Remove card with animation
                const card = document.querySelector(`.favorite-card[data-car-id="${carId}"]`);
                if (card) {
                    card.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        card.classList.add('hidden');
                        card.remove();
                        checkEmptyState();
                    }, 300);
                }
            };
        });
    </script>

</body>
</html>
