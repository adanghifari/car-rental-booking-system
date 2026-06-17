<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil - Presisi dalam Setiap Perjalanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="landing-non-login bg-[#F8F9FC] text-[#1E293B] antialiased overflow-x-hidden">
    <div class="page-enter-nav">
        <x-frontliner.navbar />
    </div>

    <header class="relative bg-gradient-to-r from-[#0B1528] via-[#111C31] to-[#0A1120] text-white overflow-hidden min-h-[600px] flex items-center">
        <!-- Hero Background Slider -->
        <div class="hero-bg-animate absolute right-0 bottom-0 top-0 w-full md:w-2/3 h-full z-0 opacity-80 md:opacity-100" id="hero-slider">
            @if(isset($cars) && $cars->count() > 0)
                @foreach($cars as $index => $car)
                    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide-index="{{ $index }}">
                        <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1200&q=80' }}" 
                             alt="{{ $car->name }}" 
                             class="w-full h-full object-cover object-center scale-x-[-1]">
                    </div>
                @endforeach
            @else
                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1200&q=80" alt="Default Car" class="w-full h-full object-cover object-center scale-x-[-1]">
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-[#0B1528] via-transparent to-transparent z-10"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 py-20 w-full">
            <div class="max-w-xl">
                <span class="animate-on-load inline-block bg-[#10B981] text-[#042F2E] text-xs font-bold tracking-wider uppercase px-3 py-1 rounded-full mb-6" style="--delay: 120ms">
                    THE PRECISION CONCIERGE
                </span>
                <h1 class="animate-on-load text-4xl md:text-6xl font-bold leading-tight mb-6" style="--delay: 220ms">
                    Rental Mobil Terpercaya untuk Segala Kebutuhan
                </h1>
                <p class="animate-on-load text-gray-300 text-base md:text-lg mb-8 leading-relaxed font-light" style="--delay: 320ms">
                    Mulai dari perjalanan keluarga hingga perjalanan bisnis, temukan kendaraan terbaik dengan pemesanan yang cepat dan praktis.
                </p>
                <a href="#armada" class="animate-on-load hover-lift inline-flex items-center justify-center bg-[#0B3C9B] hover:bg-[#082D76] text-white px-8 py-3.5 rounded-xl font-medium transition shadow-lg shadow-blue-900/40" style="--delay: 420ms">
                    Jelajahi Armada
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-6 -mt-10 relative z-20">
        @php
            $today = now()->toDateString();
        @endphp
        <form method="GET" action="{{ route('search-result') }}" class="animate-on-load bg-white p-6 rounded-2xl shadow-xl border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4 items-center" style="--delay: 560ms">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tanggal Mulai</label>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" min="{{ $today }}" class="bg-transparent text-sm text-gray-600 focus:outline-none w-full" oninput="if(this.form.end_date.value && this.form.end_date.value < this.value){this.form.end_date.value=this.value} this.form.end_date.min=this.value;">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tanggal Selesai</label>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" min="{{ request('start_date', $today) }}" class="bg-transparent text-sm text-gray-600 focus:outline-none w-full">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Budget Harian Maksimal</label>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                    <span class="text-sm text-gray-400 mr-2">Rp</span>
                    <input type="number" name="max_price" placeholder="Contoh: 500000" value="{{ request('max_price') }}" class="bg-transparent text-sm text-gray-700 focus:outline-none w-full">
                </div>
            </div>
            <div class="pt-6">
                <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] text-white text-sm font-semibold py-3.5 px-6 rounded-xl transition flex items-center justify-center space-x-2 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                    </svg>
                    <span>Cari Kendaraan</span>
                </button>
            </div>
        </form>
    </div>

    <section id="armada" class="max-w-7xl mx-auto px-6 py-24">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12">
            <div class="reveal">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-3">Armada Unggulan</h2>
                <p class="text-gray-500 max-w-xl">Kurasi eksklusif kendaraan performa tinggi dan SUV mewah untuk pengalaman berkendara terbaik.</p>
            </div>
            <a href="{{ route('armada') }}" class="reveal hover-lift text-[#0B3C9B] font-semibold text-sm flex items-center hover:underline mt-4 sm:mt-0" style="--delay: 120ms">
                Lihat Semua 
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse(($featuredCars ?? collect()) as $index => $car)
                <div class="reveal stagger-item hover-lift bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between" style="--delay: {{ 90 * $index }}ms">
                    <div>
                        <div class="relative bg-gray-900 rounded-xl overflow-hidden h-48 mb-5 flex items-center justify-center">
                            <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $car->name }}</h3>
                                <p class="text-xs text-gray-400">{{ $car->brand }} - {{ $car->vehicle_type->label() }}</p>
                                <div class="mt-2 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-600 border border-amber-100">
                                    <span>★</span>
                                    <span>{{ number_format($car->average_rating, 1) }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($car->daily_rate, 0, ',', '.') }}</span>
                                <p class="text-[10px] text-gray-400">/ hari</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-[11px] text-gray-500 font-medium mb-6 border-t pt-4 border-gray-50">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ $car->seat_count }} Kursi</span>
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
                                <span>{{ $car->cc }} cc</span>
                            </span>
                        </div>
                    </div>
                    @php
                        $isAvailable = $car->status->value === 'available';
                        $isRented = !$isAvailable && ($car->active_rentals_count ?? 0) > 0;
                    @endphp
                    @if($isAvailable)
                        <button type="button" onclick="openBookingModal({ id: {{ $car->id }}, name: '{{ addslashes($car->name) }}', image: '{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80' }}', dailyRate: {{ $car->daily_rate }}, status: '{{ $car->status->value ?? $car->status }}', selfDriveAvailable: {{ $car->self_drive_available ? 'true' : 'false' }}, driverAvailable: {{ $car->driver_available ? 'true' : 'false' }} })" class="hover-lift w-full text-center block border border-[#0B3C9B] text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition py-3 rounded-xl font-semibold text-sm cursor-pointer">
                            Pesan Sekarang
                        </button>
                    @else
                        <button type="button" disabled class="w-full text-center block bg-gray-100 border border-gray-200 text-gray-400 py-3 rounded-xl font-semibold text-sm cursor-not-allowed">
                            {{ $isRented ? 'Armada Sedang di Sewa' : 'Sedang Maintenance' }}
                        </button>
                    @endif
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-500">Belum ada armada unggulan yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-gradient-to-b from-[#F8F9FC] to-[#EEF2F6] py-24">
        <div class="reveal max-w-7xl mx-auto px-6 text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-3">Kesan Eksklusif</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Apa yang dikatakan oleh para pelanggan setia kami tentang standar pelayanan Azure Velocity.</p>
        </div>

        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
            @forelse($reviews as $index => $review)
                @php
                    $initials = '';
                    $names = explode(' ', $review->user->name);
                    foreach (array_slice($names, 0, 2) as $n) {
                        $initials .= strtoupper(substr($n, 0, 1));
                    }
                @endphp

                <article class="reveal stagger-item hover-lift group bg-white rounded-[1.75rem] border border-slate-200 p-6 shadow-sm shadow-slate-200/50 flex flex-col justify-between min-h-[280px] transition hover:shadow-xl hover:shadow-slate-200/70" style="--delay: {{ 90 * $index }}ms">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-slate-100 text-[#123C7A] flex items-center justify-center font-extrabold text-sm shrink-0">
                                    {{ $initials ?: 'U' }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-base font-bold text-slate-900 truncate">{{ $review->user->name }}</h4>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600 border border-amber-100 shrink-0">
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
                    </div>

                    <div class="mt-4 text-xs text-slate-400">
                        <span>{{ optional($review->created_at)->locale('id')->diffForHumans() }}</span>
                    </div>
                </article>
            @empty
                <div class="md:col-span-3 rounded-[1.75rem] border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h3m5-10H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">Belum ada testimoni</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Testimoni customer akan tampil di sini setelah ulasan mulai masuk.
                    </p>
                </div>
            @endforelse
        </div>

    </section>

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="reveal cta-gradient-animate bg-[#1E2640] rounded-3xl overflow-hidden relative shadow-xl grid grid-cols-1 md:grid-cols-2 items-center min-h-[300px]">
            <div class="p-10 md:p-16 z-10 text-white">
                <h2 class="text-3xl font-bold mb-4">Siap Untuk Perjalanan Berikutnya?</h2>
                <p class="text-gray-300 text-sm mb-8 max-w-md font-light leading-relaxed">
                    Dapatkan pengalaman yang menyenangkan dengan layanan sewa mobil kami yang cepat, mudah, dan terpercaya. Hubungi kami untuk konsultasi atau unduh katalog PDF armada lengkap kami untuk menemukan kendaraan yang sempurna untuk kebutuhan Anda.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="https://wa.me/6282155402629" target="_blank" rel="noopener noreferrer" class="hover-lift bg-[#0B3C9B] hover:bg-[#082D76] text-white px-6 py-3 rounded-xl font-medium text-sm transition">
                        Hubungi Konsultan Kami
                    </a>
                    <a href="{{ route('armada.export') }}" class="hover-lift border border-gray-500 hover:border-white text-white px-6 py-3 rounded-xl font-medium text-sm transition">
                        Lihat Katalog PDF
                    </a>
                </div>
            </div>
            <div class="h-full w-full relative hidden md:block bg-[#1A2035] flex items-center justify-center overflow-hidden">
                <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=600&q=80" alt="Abstract Art" class="w-full h-full object-cover opacity-40 mix-blend-lighten">
                <div class="absolute inset-0 bg-gradient-to-r from-[#1E2640] via-transparent to-transparent"></div>
            </div>
        </div>
    </section>
    @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif

        <x-frontliner.footer />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.hero-slide');
            if (slides.length > 1) {
                let currentSlide = 0;
                setInterval(() => {
                    slides[currentSlide].classList.remove('opacity-100');
                    slides[currentSlide].classList.add('opacity-0');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.remove('opacity-0');
                    slides[currentSlide].classList.add('opacity-100');
                }, 5000); // Ganti gambar setiap 5 detik
            }
        });
    </script>
    <x-frontliner.booking-modal />
</body>
</html>
