<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Porsche Taycan Turbo S - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-navbar-non-login />

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-10 w-full flex-grow">
        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start mb-12">
            
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm aspect-[16/10]">
                    <img src="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=1000&q=80" alt="Porsche Taycan Side" class="w-full h-full object-cover">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl overflow-hidden border-2 border-[#0B3C9B] p-1 cursor-pointer shadow-sm aspect-[16/10]">
                        <img src="https://images.unsplash.com/photo-1611245620453-403ddb27692e?auto=format&fit=crop&w=400&q=80" alt="Porsche Taycan Front" class="w-full h-full object-cover rounded-lg">
                    </div>
                    <div class="bg-white rounded-xl overflow-hidden border border-gray-100 p-1 cursor-pointer shadow-sm hover:border-gray-300 aspect-[16/10]">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=400&q=80" alt="Porsche Taycan Grey" class="w-full h-full object-cover rounded-lg">
                    </div>
                    <div class="bg-white rounded-xl overflow-hidden border border-gray-100 p-1 cursor-pointer shadow-sm hover:border-gray-300 aspect-[16/10]">
                        <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=400&q=80" alt="Porsche Taycan Interior" class="w-full h-full object-cover rounded-lg">
                    </div>
                </div>

                <div class="pt-4">
                    <span class="inline-block bg-blue-50 text-[#0B3C9B] text-xs font-semibold px-3 py-1 rounded-md mb-3">Premium Sedan</span>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-4">Porsche Taycan Turbo S</h1>
                    <p class="text-gray-500 text-sm leading-relaxed font-light text-justify">
                        Nikmati puncak teknologi otomotif dengan Porsche Taycan Turbo S. Kendaraan listrik murni yang menggabungkan performa legendaris Porsche dengan kemewahan futuristis. Sempurna untuk perjalanan bisnis prestisius atau pengalaman berkendara tiada duanya di ibu kota.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">⚡ Tenaga</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">750 HP</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">⏱️ 0-100 km/h</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">2.8s</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">🔋 Baterai</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">93.4 kWh</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">🛣️ Jarak Tempuh</p>
                        <p class="text-sm font-bold text-[#0B3C9B]">412 km</p>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-md md:sticky md:top-24">
                <div class="rounded-xl overflow-hidden h-32 mb-4 bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=500&q=80" alt="Porsche Side Mini" class="w-full h-full object-cover">
                </div>
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mulai Dari</p>
                        <p class="text-xl font-bold text-[#0B3C9B]">Rp 4.500.000 <span class="text-xs font-normal text-gray-400">/hari</span></p>
                    </div>
                    <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">Tersedia</span>
                </div>

                <form action="#" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Sewa</label>
                        <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-3">
                            <span class="text-gray-400 mr-2">📅</span>
                            <input type="text" value="12 Okt - 15 Okt 2024" readonly class="bg-transparent text-xs font-semibold text-gray-700 w-full focus:outline-none cursor-default">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Pilihan Layanan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" class="border-2 border-[#0B3C9B] bg-white text-[#0B3C9B] rounded-xl py-3 text-xs font-bold flex flex-col items-center justify-center space-y-1">
                                <span>👤</span>
                                <span>Lepas Kunci</span>
                            </button>
                            <button type="button" class="border border-gray-200 bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 hover:bg-gray-100 transition">
                                <span>📍</span>
                                <span>Dengan Sopir</span>
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span>Sewa 3 Hari</span>
                            <span class="font-semibold text-gray-800">Rp 13.500.000</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Biaya Layanan & Asuransi</span>
                            <span class="font-semibold text-gray-800">Rp 250.000</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-dashed text-sm font-bold text-gray-900">
                            <span>Total Harga</span>
                            <span class="text-[#0B3C9B] text-base">Rp 13.750.000</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold py-3.5 rounded-xl text-xs transition shadow-md tracking-wider uppercase">
                        Booking Sekarang
                    </button>
                    <p class="text-[9px] text-center text-gray-400">Pembatalan gratis hingga 24 jam sebelum pengambilan</p>
                </form>
            </aside>
        </div>

        <section class="border-t border-gray-200 pt-10 mb-12">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Fitur Utama</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-8 text-sm text-gray-700 font-medium">
                <div class="flex items-center space-x-3"><span class="text-blue-500 text-lg">❄️</span> <span>Climate Control 4-Zone</span></div>
                <div class="flex items-center space-x-3"><span class="text-blue-500 text-lg">🧭</span> <span>GPS Navigasi Aktif</span></div>
                <div class="flex items-center space-x-3"><span class="text-blue-500 text-lg">📱</span> <span>Apple CarPlay Wireless</span></div>
                <div class="flex items-center space-x-3"><span class="text-blue-500 text-lg">🔊</span> <span>Sistem Audio Bose</span></div>
                <div class="flex items-center space-x-3"><span class="text-blue-500 text-lg">📸</span> <span>Kamera 360 Derajat</span></div>
                <div class="flex items-center space-x-3"><span class="text-blue-500 text-lg">🔌</span> <span>Fast Charging 800V</span></div>
            </div>
        </section>

        <section class="border-t border-gray-200 pt-10 mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-900">Ulasan Pengguna</h2>
                <span class="text-xs font-bold text-amber-500">★ 4.9 <span class="text-gray-400 font-normal">(124 Ulasan)</span></span>
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
                        "Pengalaman luar biasa dengan Taycan. Akselerasi instan tanpa suara dan handling yang sangat tajam. Sangat direkomendasikan!"
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
                        "Pelayanan RentalMobil sangat profesional. Mobil diantar tepat waktu dan dalam kondisi sempurna."
                    </p>
                </div>
            </div>
        </section>

        <section class="border-t border-gray-200 pt-10">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-[10px] font-bold text-[#0B3C9B] uppercase tracking-wider">Rekomendasi</p>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Mobil Serupa</h2>
                </div>
                <a href="#" class="text-[#0B3C9B] font-semibold text-xs flex items-center hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between">
                    <div>
                        <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                            <img src="https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=500&q=80" alt="Tesla Model S" class="w-full h-full object-cover">
                            <button type="button" class="absolute top-3 right-3 w-8 h-8 bg-black/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/10 hover:bg-white hover:text-red-500 transition">🤍</button>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Tesla Model S Plaid</h4>
                                <p class="text-[10px] text-gray-400">Sedan Eksekutif Listrik</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Rp 4.2jt<span class="text-[10px] font-normal text-gray-400">/hari</span></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-[11px] text-gray-500 border-t pt-3 border-gray-50 mt-2">
                        <span>⚡ Listrik</span>
                        <span>👥 5 Kursi</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between">
                    <div>
                        <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                            <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80" alt="Audi RS e-tron" class="w-full h-full object-cover">
                            <button type="button" class="absolute top-3 right-3 w-8 h-8 bg-black/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/10 hover:bg-white hover:text-red-500 transition">🤍</button>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Audi RS e-tron GT</h4>
                                <p class="text-[10px] text-gray-400">Grand Tourer Listrik</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Rp 4.8jt<span class="text-[10px] font-normal text-gray-400">/hari</span></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-[11px] text-gray-500 border-t pt-3 border-gray-50 mt-2">
                        <span>⚡ Listrik</span>
                        <span>👥 4 Kursi</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between">
                    <div>
                        <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                            <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=500&q=80" alt="BMW i7" class="w-full h-full object-cover">
                            <button type="button" class="absolute top-3 right-3 w-8 h-8 bg-black/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/10 hover:bg-white hover:text-red-500 transition">🤍</button>
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">BMW i7 xDrive60</h4>
                                <p class="text-[10px] text-gray-400">Sedan Mewah Listrik</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Rp 5.2jt<span class="text-[10px] font-normal text-gray-400">/hari</span></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-[11px] text-gray-500 border-t pt-3 border-gray-50 mt-2">
                        <span>⚡ Listrik</span>
                        <span>👥 5 Kursi</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <x-footer />

</body>
</html>