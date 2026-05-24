<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HD Rental Car - Penyewaan Mobil Terpercaya</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Instrument Sans', 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-white text-gray-900">
    <!-- Header -->
    <x-navbar-non-login />

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-50 to-blue-100 py-12 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Left Content -->
                <div>
                    <div class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium mb-4">
                        🚗 MOBIL BERSTANDAR INTERNASIONAL
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                        Presisi dalam Setiap Perjalanan
                    </h1>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        Nikmati pengalaman berkendara kelas dunia dengan layanan penyewaan mobil terpercaya. Kami menyediakan armada kendaraan terbaru dengan standar keamanan internasional.
                    </p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                        JELAJAHI ARMADA
                    </button>
                </div>

                <!-- Right Content - Car Image -->
                <div class="flex justify-center">
                    <div class="bg-blue-400 rounded-2xl w-full h-80 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-white text-2xl font-bold">Gambar Mobil</p>
                            <p class="text-blue-100">(Placeholder)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="bg-white -mt-8 relative z-10 mb-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">📍 Lokasi Jemput</label>
                        <input type="text" placeholder="Pilih lokasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">📅 Tanggal Mulai</label>
                        <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">💰 Harga Maksimal</label>
                        <input type="text" placeholder="Rp 500.000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            🔍 Cari Kendaraan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Vehicles Section -->
    <section id="armada" class="py-12 lg:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Armada Unggulan</h2>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold">Lihat Semua →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Car Card 1 -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="bg-gray-200 h-48 flex items-center justify-center">
                        <p class="text-gray-500">Gambar Mobil</p>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1">Premium S-Series</h3>
                        <p class="text-gray-600 text-sm mb-3">Sedan mewah untuk perjalanan bisnis</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-600">⭐ 4.8/5</span>
                            <span class="text-sm text-gray-600">👥 5 penumpang</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-600 mb-4">Rp 2,5jt</p>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>

                <!-- Car Card 2 -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="bg-gray-200 h-48 flex items-center justify-center">
                        <p class="text-gray-500">Gambar Mobil</p>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1">Velocity SUV GT</h3>
                        <p class="text-gray-600 text-sm mb-3">SUV tangguh untuk petualangan</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-600">⭐ 4.9/5</span>
                            <span class="text-sm text-gray-600">👥 7 penumpang</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-600 mb-4">Rp 3,5jt</p>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>

                <!-- Car Card 3 -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition">
                    <div class="bg-gray-200 h-48 flex items-center justify-center">
                        <p class="text-gray-500">Gambar Mobil</p>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1">Azure Convertible</h3>
                        <p class="text-gray-600 text-sm mb-3">Mobil sport untuk gaya hidup</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-600">⭐ 5.0/5</span>
                            <span class="text-sm text-gray-600">👥 2 penumpang</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-600 mb-4">Rp 5,2jt</p>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="py-12 lg:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 text-center mb-12">Kesan Eksklusif</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Testimonial 1 -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="flex gap-1 mb-4">
                        <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "Layanan HD Rental Car sangat memuaskan. Mobil dalam kondisi prima dan stafnya sangat profesional. Saya akan sewa lagi!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <p class="font-semibold text-gray-900">Budi Santoso</p>
                            <p class="text-sm text-gray-600">Pengusaha Jakarta</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 - Featured -->
                <div class="bg-blue-600 text-white rounded-xl p-6 md:scale-105 shadow-lg">
                    <div class="flex gap-1 mb-4">
                        <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                    </div>
                    <p class="mb-4 text-lg">
                        "Sangat puas dengan harga dan kualitas mobil. Proses booking mudah dan cepat. Rekomendasi untuk semua traveler!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-400 rounded-full"></div>
                        <div>
                            <p class="font-semibold">Siti Maya</p>
                            <p class="text-sm text-blue-100">Travel Blogger</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="flex gap-1 mb-4">
                        <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                    </div>
                    <p class="text-gray-700 mb-4">
                        "Terbaik di kelasnya! Armada selalu terawat, lokasi jemput strategis, dan harganya sangat kompetitif."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <p class="font-semibold text-gray-900">Ahmad Rizki</p>
                            <p class="text-sm text-gray-600">Konsultan Bisnis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Siap Untuk Perjalanan Berikutnya?</h2>
            <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                Dapatkan pengalaman berkendara terbaik dengan HD Rental Car. Pesan sekarang dan nikmati diskon khusus untuk pelanggan baru!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition inline-block">
                    Daftar Sekarang
                </a>
                <a href="#" class="border-2 border-white hover:bg-white hover:text-gray-900 text-white px-8 py-3 rounded-lg font-semibold transition inline-block">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <x-footer />
</body>
</html>
