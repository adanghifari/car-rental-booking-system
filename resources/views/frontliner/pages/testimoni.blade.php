<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Customer - MD CAR RENTAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F5F7FB] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-10 w-full space-y-8">
        <section
            class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#123C7A] via-[#1E4E9A] to-[#2C6DD5] text-white p-8 shadow-2xl shadow-blue-200/30">
            <div
                class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.24),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(191,219,254,0.18),_transparent_32%)]">
            </div>
            <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-3xl space-y-3">
                    <nav class="text-xs text-blue-200 flex items-center gap-2">
                        <a href="{{ route('frontliner') }}" class="hover:underline">Beranda</a>
                        <span>/</span>
                        <span class="text-white font-medium">Testimoni</span>
                    </nav>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 border border-white/10 text-[11px] font-bold uppercase tracking-[0.2em] text-blue-100">Ulasan
                        Customer</span>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Semua Testimoni Pelanggan</h1>
                        <p class="mt-2 text-sm md:text-base text-slate-200 leading-7">
                            Halaman ini menampilkan ulasan asli dari seluruh customer untuk membantu frontliner
                            memantau pengalaman pelanggan terhadap layanan dan armada kami.
                        </p>
                    </div>
                </div>

                <div class="min-w-[11rem] rounded-2xl bg-white/10 border border-white/10 px-5 py-4 backdrop-blur">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-blue-100">Total testimoni</p>
                    <p class="mt-2 text-3xl font-extrabold">{{ number_format($reviews->total()) }}</p>
                    <p class="mt-1 text-xs text-blue-100">Dari seluruh customer</p>
                </div>
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-[1.5rem] p-4 shadow-sm shadow-slate-200/50">
            <form method="GET" action="{{ route('testimoni') }}" class="space-y-3">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center flex-1">
                        <div class="shrink-0">
                            <p class="text-sm font-bold text-slate-900">Filter</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 flex-1">
                            <select id="vehicle_type" name="vehicle_type"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                                <option value="">Semua kategori mobil</option>
                                @foreach($vehicleTypes as $vehicleType)
                                    <option value="{{ $vehicleType->value }}" {{ $selectedVehicleType === $vehicleType->value ? 'selected' : '' }}>
                                        {{ $vehicleType->label() }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="min_rating" name="min_rating"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                                <option value="0" {{ ($selectedRating ?? 0) === 0 ? 'selected' : '' }}>Semua rating</option>
                                <option value="5" {{ ($selectedRating ?? 0) === 5 ? 'selected' : '' }}>5 bintang</option>
                                <option value="4" {{ ($selectedRating ?? 0) === 4 ? 'selected' : '' }}>4 bintang</option>
                                <option value="3" {{ ($selectedRating ?? 0) === 3 ? 'selected' : '' }}>3 bintang</option>
                                <option value="2" {{ ($selectedRating ?? 0) === 2 ? 'selected' : '' }}>2 bintang</option>
                                <option value="1" {{ ($selectedRating ?? 0) === 1 ? 'selected' : '' }}>1 bintang</option>
                            </select>

                            <select id="sort" name="sort"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                                <option value="latest" {{ $selectedSort === 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                                <option value="oldest" {{ $selectedSort === 'oldest' ? 'selected' : '' }}>Urutkan: Terlama</option>
                                <option value="highest_rating" {{ $selectedSort === 'highest_rating' ? 'selected' : '' }}>Urutkan: Bintang tertinggi</option>
                                <option value="lowest_rating" {{ $selectedSort === 'lowest_rating' ? 'selected' : '' }}>Urutkan: Bintang terendah</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-[#123C7A] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#1E4E9A] transition">
                            Terapkan
                        </button>
                        <a href="{{ route('testimoni') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Reset
                        </a>
                    </div>
                </div>

                @if($selectedVehicleType !== '' || ($selectedRating ?? 0) > 0 || $selectedSort !== 'latest')
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        @if($selectedVehicleType !== '')
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                                {{ collect($vehicleTypes)->firstWhere('value', $selectedVehicleType)?->label() }}
                            </span>
                        @endif
                        @if(($selectedRating ?? 0) > 0)
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 border border-amber-100">
                                {{ $selectedRating }} bintang
                            </span>
                        @endif
                        @if($selectedSort !== 'latest')
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                                {{
                                    match ($selectedSort) {
                                        'oldest' => 'Urutan: Terlama',
                                        'highest_rating' => 'Urutan: Bintang tertinggi',
                                        'lowest_rating' => 'Urutan: Bintang terendah',
                                        default => 'Urutan: Terbaru',
                                    }
                                }}
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($reviews as $review)
                @php
                    $initials = '';
                    $names = explode(' ', $review->user->name);
                    foreach (array_slice($names, 0, 2) as $namePart) {
                        $initials .= strtoupper(substr($namePart, 0, 1));
                    }
                @endphp

                <article
                    class="group bg-white rounded-[1.75rem] border border-slate-200 p-6 shadow-sm shadow-slate-200/50 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200/70">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-slate-100 text-[#123C7A] flex items-center justify-center font-extrabold text-sm shrink-0">
                                {{ $initials ?: 'U' }}
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-bold text-slate-900 truncate">{{ $review->user->name }}</h2>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600 border border-amber-100 shrink-0">
                            ★ {{ $review->rating }}/5
                        </span>
                    </div>

                    <p class="mt-4 text-xs text-slate-500">
                        Mobil: {{ $review->car->name }} ({{ $review->car->brand }})
                    </p>

                    <div class="mt-3 flex items-center gap-1 text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>

                    <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-100 p-4 min-h-[8.5rem]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Ulasan</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600 italic">
                            "{{ $review->comment ?: 'Customer tidak menuliskan ulasan tambahan.' }}"
                        </p>
                    </div>

                    <div class="mt-4 text-xs text-slate-400">
                        <span>{{ optional($review->created_at)->locale('id')->diffForHumans() }}</span>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-[1.75rem] border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h3m5-10H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">Belum ada testimoni</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Testimoni customer akan muncul di halaman ini setelah review mulai masuk.
                    </p>
                </div>
            @endforelse
        </section>

        @if($reviews->hasPages())
            @php
                $currentPage = $reviews->currentPage();
                $lastPage = $reviews->lastPage();
                $windowStart = max(1, $currentPage - 1);
                $windowEnd = min($lastPage, $currentPage + 1);

                if ($lastPage <= 5) {
                    $pageItems = range(1, $lastPage);
                } else {
                    $pageItems = [1];

                    if ($windowStart > 2) {
                        $pageItems[] = '...';
                    }

                    for ($page = $windowStart; $page <= $windowEnd; $page++) {
                        if ($page > 1 && $page < $lastPage) {
                            $pageItems[] = $page;
                        }
                    }

                    if ($windowEnd < $lastPage - 1) {
                        $pageItems[] = '...';
                    }

                    $pageItems[] = $lastPage;
                    $pageItems = array_values(array_unique($pageItems, SORT_REGULAR));
                }
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-4 flex items-center justify-between gap-4 flex-col sm:flex-row">
                <p class="text-sm text-slate-500">
                    Menampilkan {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }} dari {{ $reviews->total() }} testimoni
                </p>

                <div class="flex items-center gap-2">
                    @if ($reviews->onFirstPage())
                        <span class="px-3 py-2 rounded-xl border border-blue-100 bg-blue-50 text-blue-300 text-sm font-semibold">‹</span>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}"
                            class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition text-sm font-semibold">‹</a>
                    @endif

                    @foreach ($pageItems as $pageItem)
                        @if ($pageItem === '...')
                            <span class="px-3 py-2 rounded-xl border border-transparent text-blue-300 text-sm font-semibold">...</span>
                        @elseif ($pageItem === $currentPage)
                            <span class="px-3 py-2 rounded-xl bg-[#0B3C9B] text-white text-sm font-semibold">{{ $pageItem }}</span>
                        @else
                            <a href="{{ $reviews->url($pageItem) }}"
                                class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition text-sm font-semibold">{{ $pageItem }}</a>
                        @endif
                    @endforeach

                    @if ($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}"
                            class="px-3 py-2 rounded-xl border border-blue-200 bg-blue-50 text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition text-sm font-semibold">›</a>
                    @else
                        <span class="px-3 py-2 rounded-xl border border-blue-100 bg-blue-50 text-blue-300 text-sm font-semibold">›</span>
                    @endif
                </div>
            </div>
        @endif
    </main>

    <x-frontliner.footer />

</body>

</html>
