<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armada Kami - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    </style>
</head>

<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <main class="max-w-[1400px] mx-auto px-4 md:px-8 py-10 w-full flex-grow">

        <div class="w-full space-y-10">

            <!-- Banner Kriteria Pencarian -->
            <div
                class="bg-gradient-to-r from-[#0B3C9B] to-[#1E40AF] rounded-2xl p-6 md:p-8 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                <div>
                    <nav class="text-xs text-blue-200 mb-2 flex items-center space-x-2">
                        <a href="{{ auth()->check() ? route('frontliner') : route('home') }}"
                            class="hover:underline">Beranda</a>
                        <span>/</span>
                        <span class="text-white font-medium">Armada</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Katalog Armada</h1>
                    <p class="text-sm text-blue-100 font-light">
                        Menampilkan seluruh armada mobil yang tersedia untuk perjalanan Anda.
                    </p>
                </div>
            </div>

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('armada') }}" class="flex-1">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                                    </svg>
                                </span>
                                <input type="text" name="q" value="{{ $search ?? request('q') }}"
                                    placeholder="Cari nama mobil, brand, tipe, atau plat nomor..."
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]">
                            </div>
                            <div class="flex gap-3">
                                <button type="submit"
                                    class="inline-flex items-center justify-center bg-[#0B3C9B] hover:bg-[#082D76] text-white px-5 py-3 rounded-xl text-sm font-semibold transition whitespace-nowrap">
                                    Cari
                                </button>
                                @if(($search ?? request('q')) !== '')
                                <a href="{{ route('armada') }}"
                                    class="inline-flex items-center justify-center border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 px-5 py-3 rounded-xl text-sm font-semibold transition whitespace-nowrap">
                                    Reset
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('search-result') }}"
                        class="inline-flex items-center justify-center gap-2 border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 px-5 py-3 rounded-xl text-sm font-semibold transition whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 5.25h18M3 12h18M3 18.75h18" />
                        </svg>
                        Cari dengan Filter
                    </a>
                </div>
            </section>

            <!-- Semua Hasil Pencarian -->
            <section>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Semua Armada</h2>
                        @if(($search ?? request('q')) !== '')
                        <p class="text-sm text-gray-500 mt-1">
                            Hasil pencarian untuk "<span class="font-semibold text-gray-700">{{ $search ?? request('q') }}</span>"
                        </p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($cars as $car)
                    <div
                        class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}"
                                    alt="{{ $car->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $car->name }}</h4>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">
                                        {{ $car->brand }} - {{ $car->vehicle_type->label() }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span
                                        class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center">
                                        ★ {{ number_format($car->average_rating, 1) }}
                                    </span>
                                    <button type="button" onclick="toggleFavorite({{ $car->id }}, event)"
                                        data-car-id="{{ $car->id }}"
                                        class="favorite-btn text-slate-800 hover:text-red-600 transition-colors duration-200 cursor-pointer focus:outline-none p-1"
                                        title="Tambah ke Favorit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor"
                                            class="w-5 h-5 heart-icon transition-transform duration-200 active:scale-75">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">
                                <span>👥 {{ $car->seat_count }} Penumpang</span>
                                <span>⚙️ {{ $car->transmission->label() }}</span>
                                <span>⚡ {{ $car->cc }} cc</span>
                                <span>📅 Th {{ $car->year }}</span>
                            </div>
                        </div>
                        <div class="border-t pt-3 border-gray-50 space-y-2.5">
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-bold text-gray-900">Rp
                                    {{ number_format($car->daily_rate, 0, ',', '.') }}<span
                                        class="text-[10px] font-normal text-gray-400">/hari</span></p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('car-detail', ['car' => $car->id]) }}"
                                    class="border border-[#0B3C9B] text-[#0B3C9B] hover:bg-blue-50 text-center py-2 rounded-xl text-xs font-bold transition inline-block">Detail</a>
                                <button type="button"
                                    onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: '{{ $car->status->value ?? $car->status }}', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })"
                                    class="bg-[#0B3C9B] hover:bg-[#082D76] text-white text-center py-2 rounded-xl text-xs font-bold transition cursor-pointer">Pesan</button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-4 text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <p class="text-gray-500 font-medium text-base mb-1">Armada Tidak Ditemukan</p>
                        <p class="text-gray-400 text-xs">Saat ini belum ada mobil yang terdaftar.</p>
                    </div>
                    @endforelse
                </div>

                @if ($cars->hasPages())
                @php
                $currentPage = $cars->currentPage();
                $lastPage = $cars->lastPage();
                $windowStart = max(1, $currentPage - 1);
                $windowEnd = min($lastPage, $currentPage + 1);

                if ($lastPage <= 5) { $pageItems=range(1, $lastPage); } else { $pageItems=[1]; if ($windowStart> 2) {
                    $pageItems[] = '...';
                    }

                    for ($page = $windowStart; $page <= $windowEnd; $page++) { if ($page> 1 && $page < $lastPage) {
                            $pageItems[]=$page; } } if ($windowEnd < $lastPage - 1) { $pageItems[]='...' ; }
                            $pageItems[]=$lastPage; $pageItems=array_values(array_unique($pageItems, SORT_REGULAR)); }
                            @endphp <div
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-4 flex items-center justify-between gap-4">
                            <p class="text-sm text-gray-500">
                                Menampilkan {{ $cars->firstItem() ?? 0 }}-{{ $cars->lastItem() ?? 0 }} dari
                                {{ $cars->total() }} armada
                            </p>

                            <div class="flex items-center gap-2">
                                @if ($cars->onFirstPage())
                                <span
                                    class="px-3 py-2 rounded-xl border border-blue-100 bg-blue-50 text-blue-300 text-sm font-semibold">‹</span>
                                @else
                                <a href="{{ $cars->previousPageUrl() }}"
                                    class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition text-sm font-semibold">‹</a>
                                @endif

                                @foreach ($pageItems as $pageItem)
                                @if ($pageItem === '...')
                                <span
                                    class="px-3 py-2 rounded-xl border border-transparent text-blue-300 text-sm font-semibold">...</span>
                                @elseif ($pageItem === $currentPage)
                                <span
                                    class="px-3 py-2 rounded-xl bg-[#0B3C9B] text-white text-sm font-semibold">{{ $pageItem }}</span>
                                @else
                                <a href="{{ $cars->url($pageItem) }}"
                                    class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition text-sm font-semibold">{{ $pageItem }}</a>
                                @endif
                                @endforeach

                                @if ($cars->hasMorePages())
                                <a href="{{ $cars->nextPageUrl() }}"
                                    class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition text-sm font-semibold">›</a>
                                @else
                                <span
                                    class="px-3 py-2 rounded-xl border border-blue-100 bg-blue-50 text-blue-300 text-sm font-semibold">›</span>
                                @endif
                            </div>
        </div>
        @endif
        </section>

        </div>
    </main>

    <x-frontliner.footer />
    <x-frontliner.booking-modal />

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
