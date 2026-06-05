<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil - Presisi dalam Setiap Perjalanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8F9FC] text-[#1E293B] antialiased">
    <x-frontliner.navbar-non-login />

    <header class="relative bg-gradient-to-r from-[#0B1528] via-[#111C31] to-[#0A1120] text-white overflow-hidden min-h-[600px] flex items-center">
        <div class="absolute right-0 bottom-0 top-0 w-full md:w-2/3 h-full z-0 opacity-80 md:opacity-100">
            <img src="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1200&q=80" alt="Teal Luxury Car" class="w-full h-full object-cover object-center scale-x-[-1]">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0B1528] via-transparent to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 py-20 w-full">
            <div class="max-w-xl">
                <span class="inline-block bg-[#10B981] text-[#042F2E] text-xs font-bold tracking-wider uppercase px-3 py-1 rounded-full mb-6">
                    THE PRECISION CONCIERGE
                </span>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
                    Presisi dalam Setiap Perjalanan
                </h1>
                <p class="text-gray-300 text-base md:text-lg mb-8 leading-relaxed font-light">
                    Rasakan kemewahan tanpa kompromi dengan layanan kurasi kendaraan kelas atas kami. Didesain untuk mereka yang menghargai ketepatan dan performa.
                </p>
                <a href="#armada" class="inline-flex items-center justify-center bg-[#0B3C9B] hover:bg-[#082D76] text-white px-8 py-3.5 rounded-xl font-medium transition shadow-lg shadow-blue-900/40">
                    Jelajahi Armada
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-6 -mt-10 relative z-20">
        <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <div class="relative">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Lokasi Penjemputan</label>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#0B3C9B" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <input type="text" placeholder="Cari kota atau bandara..." class="bg-transparent text-sm text-gray-700 focus:outline-none w-full">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tanggal Mulai</label>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                    <input type="date" class="bg-transparent text-sm text-gray-600 focus:outline-none w-full">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tanggal Berakhir</label>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                    <input type="date" class="bg-transparent text-sm text-gray-600 focus:outline-none w-full">
                </div>
            </div>
            <div class="pt-6">
                <button class="w-full bg-[#0B3C9B] hover:bg-[#082D76] text-white text-sm font-semibold py-3.5 px-6 rounded-xl transition flex items-center justify-center space-x-2 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                    </svg>
                    <span>Cari Kendaraan</span>
                </button>
            </div>
        </div>
    </div>

    <section id="armada" class="max-w-7xl mx-auto px-6 py-24">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-3">Armada Unggulan</h2>
                <p class="text-gray-500 max-w-xl">Kurasi eksklusif kendaraan performa tinggi dan SUV mewah untuk pengalaman berkendara terbaik.</p>
            </div>
            <a href="#" class="text-[#0B3C9B] font-semibold text-sm flex items-center hover:underline mt-4 sm:mt-0">
                Lihat Semua 
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                <div class="relative bg-gray-900 rounded-xl overflow-hidden h-48 mb-5 flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80" alt="Precision X-Series" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 bg-[#10B981] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide">Tersedia</span>
                </div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Precision X-Series</h3>
                        <p class="text-xs text-gray-400">Luxury Executive Sedan</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-gray-900">Rp 2.4jt</span>
                        <p class="text-[10px] text-gray-400">/ hari</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[11px] text-gray-500 font-medium mb-6 border-t pt-4 border-gray-50">
                    <span class="flex items-center">👥 4 Kursi</span>
                    <span class="flex items-center">⚙️ Auto</span>
                    <span class="flex items-center">⚡ EV Hybrid</span>
                </div>
                <button class="w-full border border-[#0B3C9B] text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition py-3 rounded-xl font-semibold text-sm">
                    Pesan Sekarang
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                <div class="relative bg-gray-900 rounded-xl overflow-hidden h-48 mb-5 flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=500&q=80" alt="Velocity SUV GT" class="w-full h-full object-cover">
                    <span class="absolute top-3 right-3 bg-[#0B3C9B] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide">Premium</span>
                </div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Velocity SUV GT</h3>
                        <p class="text-xs text-gray-400">High-Performance SUV</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-gray-900">Rp 3.8jt</span>
                        <p class="text-[10px] text-gray-400">/ hari</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[11px] text-gray-500 font-medium mb-6 border-t pt-4 border-gray-50">
                    <span class="flex items-center">👥 7 Kursi</span>
                    <span class="flex items-center">⛽ Bensin</span>
                    <span class="flex items-center">⛓️ AWD</span>
                </div>
                <button class="w-full border border-[#0B3C9B] text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition py-3 rounded-xl font-semibold text-sm">
                    Pesan Sekarang
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                <div class="relative bg-gray-900 rounded-xl overflow-hidden h-48 mb-5 flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1525609004556-c46c7d6cf0a3?auto=format&fit=crop&w=500&q=80" alt="Azure Convertible" class="w-full h-full object-cover">
                </div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Azure Convertible</h3>
                        <p class="text-xs text-gray-400">Luxury Sports Tourer</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-gray-900">Rp 5.2jt</span>
                        <p class="text-[10px] text-gray-400">/ hari</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[11px] text-gray-500 font-medium mb-6 border-t pt-4 border-gray-50">
                    <span class="flex items-center">👥 2 Kursi</span>
                    <span class="flex items-center">⚙️ Auto</span>
                    <span class="flex items-center">🏎️ Sport Mode</span>
                </div>
                <button class="w-full border border-[#0B3C9B] text-[#0B3C9B] hover:bg-[#0B3C9B] hover:text-white transition py-3 rounded-xl font-semibold text-sm">
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-b from-[#F8F9FC] to-[#EEF2F6] py-24">
        <div class="max-w-7xl mx-auto px-6 text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-3">Kesan Eksklusif</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Apa yang dikatakan oleh para pelanggan setia kami tentang standar pelayanan Azure Velocity.</p>
        </div>

        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full min-h-[250px]">
                <div>
                    <div class="flex text-[#10B981] space-x-1 mb-4">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                    <p class="text-gray-600 italic text-sm leading-relaxed mb-6">
                        "Pelayanan yang benar-benar presisi. Mobil dalam kondisi sempurna saat dikirim ke hotel saya di Bali. Tidak ada yang menandingi standar ini."
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Adrian" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Adrian Prasetya</h4>
                        <p class="text-[10px] text-gray-400">CEO, Tech Global</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#0B3C9B] text-white p-8 rounded-2xl shadow-xl flex flex-col justify-between h-full min-h-[280px] md:-translate-y-4 transition">
                <div>
                    <div class="flex text-[#10B981] space-x-1 mb-4">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                    <p class="text-blue-100 italic text-sm leading-relaxed mb-6">
                        "Azure Velocity memahami arti dari 'Precision Concierge'. Setiap detail dari proses pemesanan hingga pengembalian berjalan sangat mulus."
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Siska" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <h4 class="text-sm font-bold text-white">Siska Wijaya</h4>
                        <p class="text-[10px] text-blue-300">Lifestyle Influencer</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full min-h-[250px]">
                <div>
                    <div class="flex text-[#10B981] space-x-1 mb-4">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                    <p class="text-gray-600 italic text-sm leading-relaxed mb-6">
                        "Menyewa supercar biasanya rumit, tapi di sini sangat mudah. Verifikasi cepat dan unit selalu terbaru. Sangat direkomendasikan!"
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=100&q=80" alt="Bima" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Bima Arya</h4>
                        <p class="text-[10px] text-gray-400">Entrepreneur</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="bg-[#1E2640] rounded-3xl overflow-hidden relative shadow-xl grid grid-cols-1 md:grid-cols-2 items-center min-h-[300px]">
            <div class="p-10 md:p-16 z-10 text-white">
                <h2 class="text-3xl font-bold mb-4">Siap Untuk Perjalanan Berikutnya?</h2>
                <p class="text-gray-300 text-sm mb-8 max-w-md font-light leading-relaxed">
                    Dapatkan penawaran khusus untuk penyewaan jangka panjang atau reservasi event spesial Anda hari ini.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white px-6 py-3 rounded-xl font-medium text-sm transition">
                        Hubungi Konsultan Kami
                    </a>
                    <a href="#" class="border border-gray-500 hover:border-white text-white px-6 py-3 rounded-xl font-medium text-sm transition">
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
</body>
</html>