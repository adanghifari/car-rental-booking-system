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
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-navbar />

    <main class="max-w-[1400px] mx-auto px-4 md:px-8 py-10 w-full flex-grow">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
            
            <aside class="w-full bg-white p-6 rounded-2xl border border-gray-100 shadow-sm md:sticky md:top-24">
                <h3 class="text-base font-bold text-gray-900 mb-6">Filter Pencarian</h3>
                
                <div class="mb-6">
                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Tipe Mobil</h4>
                    <div class="space-y-3">
                        <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                            <span>Sedan Luxury</span>
                        </label>
                        <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                            <span>SUV Premium</span>
                        </label>
                        <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                            <span>Electric Vehicle</span>
                        </label>
                        <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                            <span>Sportscar</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6 border-t pt-5 border-gray-50">
                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Harga Per Hari</h4>
                    <div class="h-1 bg-blue-100 rounded-full relative mb-2">
                        <div class="absolute h-full bg-[#0B3C9B] w-full rounded-full"></div>
                    </div>
                    <div class="flex justify-between text-[11px] font-semibold text-gray-500">
                        <span>Rp 1jt</span>
                        <span>Rp 10jt+</span>
                    </div>
                </div>

                <div class="mb-6 border-t pt-5 border-gray-50">
                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Kapasitas</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-xs font-semibold hover:border-[#0B3C9B]">2 Kursi</button>
                        <button type="button" class="bg-[#0B3C9B] text-white py-2 px-3 rounded-xl text-xs font-semibold">4-5 Kursi</button>
                        <button type="button" class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-xs font-semibold hover:border-[#0B3C9B]">7 Kursi</button>
                        <button type="button" class="bg-gray-50 text-gray-700 border border-gray-200 py-2 px-3 rounded-xl text-xs font-semibold hover:border-[#0B3C9B]">Lainnya</button>
                    </div>
                </div>

                <div class="mb-6 border-t pt-5 border-gray-50">
                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Fasilitas</h4>
                    <div class="space-y-3">
                        <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                            <span>Dengan Sopir</span>
                        </label>
                        <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded text-[#0B3C9B] border-gray-300 focus:ring-[#0B3C9B] mr-3">
                            <span>Asuransi All-Risk</span>
                        </label>
                    </div>
                </div>

                <button type="button" class="w-full bg-blue-50 hover:bg-blue-100 text-[#0B3C9B] font-semibold py-3 rounded-xl text-sm transition">
                    Reset Filter
                </button>
            </aside>


            <div class="w-full md:col-span-3 space-y-10">
                
                <div class="bg-gradient-to-r from-[#0B3C9B] to-[#1E40AF] rounded-2xl p-6 md:p-8 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                    <div>
                        <nav class="text-xs text-blue-200 mb-2 flex items-center space-x-2">
                            <a href="#" class="hover:underline">Beranda</a>
                            <span>/</span>
                            <span class="text-white font-medium">Hasil Pencarian</span>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Pencarian untuk "Jakarta"</h1>
                        <p class="text-sm text-blue-100 font-light flex flex-wrap items-center gap-2">
                            <span>12 Mei - 15 Mei</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span>
                            <span>4 Orang</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span>
                            <span>SUV Premium</span>
                        </p>
                    </div>
                    <button class="bg-white text-[#0B3C9B] hover:bg-blue-50 transition px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center space-x-2 shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        <span>Ubah Detail</span>
                    </button>
                </div>

                <section>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Rekomendasi untuk Kamu</h2>
                        <a href="#" class="text-[#0B3C9B] font-semibold text-xs flex items-center hover:underline">
                            Lihat Semua
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 bg-slate-900 rounded-2xl overflow-hidden relative group min-h-[300px] flex flex-col justify-end p-6 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=800&q=80" alt="Porsche Taycan" class="absolute inset-0 w-full h-full object-cover opacity-70 group-hover:scale-105 transition duration-500">
                            <div class="absolute top-6 right-6 bg-black/40 backdrop-blur-sm border border-white/10 rounded-xl p-3 text-right text-white">
                                <p class="text-[10px] text-gray-300 uppercase tracking-wider">Mulai Dari</p>
                                <p class="text-lg font-bold">Rp 8.5jt<span class="text-xs font-light text-gray-300">/hari</span></p>
                            </div>
                            <div class="relative z-10 text-white">
                                <span class="inline-block bg-[#10B981] text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide mb-2">BEST EV 2024</span>
                                <h3 class="text-2xl font-bold mb-2">Porsche Taycan 4S</h3>
                                <div class="flex items-center space-x-4 text-xs text-gray-300 font-medium">
                                    <span>🔌 Electric Performance</span>
                                    <span class="text-yellow-400">★ <span class="text-white">4.9 (120+ Reviews)</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-900 rounded-2xl overflow-hidden relative group min-h-[300px] flex flex-col justify-end p-6 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1520050206274-a1ae446cb3cc?auto=format&fit=crop&w=500&q=80" alt="Mercedes G63" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition duration-500">
                            <button type="button" class="absolute top-6 right-6 w-9 h-9 bg-black/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white border border-white/10 hover:bg-white hover:text-red-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </button>
                            <div class="relative z-10 text-white">
                                <h3 class="text-lg font-bold mb-0.5">Mercedes-Benz G63</h3>
                                <p class="text-[11px] text-gray-300 mb-3">Off-road Mastery</p>
                                <p class="text-lg font-bold">Rp 12jt<span class="text-xs font-light text-gray-300">/hari</span></p>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex justify-between items-center pb-4 border-b border-gray-200 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Hasil Armada</h2>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="text-gray-400">Urutkan:</span>
                            <select class="bg-transparent font-bold text-[#0B3C9B] focus:outline-none cursor-pointer">
                                <option>Harga Terendah</option>
                                <option>Harga Tertinggi</option>
                                <option>Terpopuler</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                        
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between">
                            <div>
                                <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                    <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80" alt="BMW M4" class="w-full h-full object-cover">
                                    <span class="absolute top-3 left-3 bg-[#10B981] text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase">Tersedia</span>
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">BMW M4 Competition</h4>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">Sportscars</p>
                                    </div>
                                    <span class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center">
                                        ★ 4.8
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">
                                    <span>👤 4 Penumpang</span>
                                    <span>⚙️ Automatic</span>
                                    <span>❄️ Dual Zone AC</span>
                                    <span>🧳 2 Koper Besar</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center border-t pt-3 border-gray-50">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Rp 5.2jt<span class="text-[10px] font-normal text-gray-400">/hari</span></p>
                                </div>
                                <button type="button" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white px-4 py-2 rounded-xl text-xs font-semibold transition">Pesan Sekarang</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between">
                            <div>
                                <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                    <img src="https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=500&q=80" alt="Range Rover" class="w-full h-full object-cover">
                                    <span class="absolute top-3 left-3 bg-[#10B981] text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase">TERLARIS</span>
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Range Rover Vogue</h4>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">Luxury SUV</p>
                                    </div>
                                    <span class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center">
                                        ★ 4.9
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">
                                    <span>👤 5 Penumpang</span>
                                    <span>⚙️ Automatic</span>
                                    <span>📶 Wi-Fi Hotspot</span>
                                    <span>💺 Massage Seats</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center border-t pt-3 border-gray-50">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Rp 7.8jt<span class="text-[10px] font-normal text-gray-400">/hari</span></p>
                                </div>
                                <button type="button" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white px-4 py-2 rounded-xl text-xs font-semibold transition">Pesan Sekarang</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm p-4 flex flex-col justify-between">
                            <div>
                                <div class="relative bg-gray-100 rounded-xl overflow-hidden h-40 mb-4">
                                    <img src="https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=500&q=80" alt="Tesla Model S" class="w-full h-full object-cover">
                                    <span class="absolute top-3 left-3 bg-blue-600 text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase">INSTANT BOOK</span>
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Tesla Model S Plaid</h4>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-0.5">Electric Luxury</p>
                                    </div>
                                    <span class="bg-blue-50 text-[#0B3C9B] text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center">
                                        ★ 4.7
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[10px] text-gray-500 border-t pt-3 border-gray-50 mb-4">
                                    <span>👤 5 Penumpang</span>
                                    <span>⚡ Supercharged</span>
                                    <span>🖥️ Autopilot Tech</span>
                                    <span>🎵 Premium Audio</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center border-t pt-3 border-gray-50">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Rp 6.5jt<span class="text-[10px] font-normal text-gray-400">/hari</span></p>
                                </div>
                                <button type="button" class="bg-[#0B3C9B] hover:bg-[#082D76] text-white px-4 py-2 rounded-xl text-xs font-semibold transition">Pesan Sekarang</button>
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-center items-center space-x-2 border-t border-gray-100 pt-6">
                        <button type="button" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-[#0B3C9B] hover:text-[#0B3C9B] transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        </button>
                        <button type="button" class="w-9 h-9 rounded-full bg-[#0B3C9B] text-white flex items-center justify-center font-bold text-sm">1</button>
                        <button type="button" class="w-9 h-9 rounded-full text-gray-600 hover:bg-gray-100 flex items-center justify-center font-medium text-sm">2</button>
                        <button type="button" class="w-9 h-9 rounded-full text-gray-600 hover:bg-gray-100 flex items-center justify-center font-medium text-sm">3</button>
                        <button type="button" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600 hover:border-[#0B3C9B] hover:text-[#0B3C9B] transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </button>
                    </div>
                </section>

            </div>
        </div>
    </main>

    <x-footer />

</body>
</html>