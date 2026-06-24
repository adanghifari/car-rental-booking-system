<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Rental Mobil</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    </style>
</head>

<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <main class="max-w-[1000px] mx-auto px-4 md:px-8 py-10 w-full flex-grow">
        <div class="space-y-8">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-[#0B3C9B] to-[#1E40AF] rounded-2xl p-6 md:p-8 text-white shadow-md">
                <nav class="text-xs text-blue-200 mb-2 flex items-center space-x-2">
                    <a href="{{ auth()->check() ? route('frontliner') : route('home') }}" class="hover:underline">Beranda</a>
                    <span>/</span>
                    <span class="text-white font-medium">Syarat & Ketentuan</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Syarat & Ketentuan Penggunaan</h1>
                <p class="text-sm text-blue-100 font-light">
                    Harap baca syarat dan ketentuan ini secara seksama sebelum menggunakan layanan kami.
                </p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-6 text-sm text-gray-600 leading-relaxed">
                <p>Selamat datang di Website Rental Mobil. Dengan mengakses dan menggunakan layanan ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.</p>
                
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">1. Ketentuan Umum</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Website ini menyediakan layanan informasi dan pemesanan kendaraan rental secara online.</li>
                            <li>Pengguna wajib memberikan data yang benar, lengkap, dan sesuai dengan identitas yang dimiliki.</li>
                            <li>Pengguna bertanggung jawab atas keamanan akun dan kerahasiaan informasi login.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">2. Registrasi Akun</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Pengguna harus memiliki akun untuk melakukan pemesanan kendaraan.</li>
                            <li>Pengguna dilarang menggunakan identitas palsu atau milik pihak lain tanpa izin.</li>
                            <li>Pengelola berhak menonaktifkan akun yang terbukti memberikan informasi tidak valid atau melakukan penyalahgunaan layanan.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">3. Pemesanan Kendaraan</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Pemesanan kendaraan dilakukan melalui sistem yang tersedia pada website.</li>
                            <li>Ketersediaan kendaraan mengikuti data yang tercantum pada sistem saat proses pemesanan dilakukan.</li>
                            <li>Pemesanan dianggap sah setelah pengguna menyelesaikan proses yang ditentukan oleh penyedia layanan.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">4. Pembayaran</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Pembayaran dilakukan sesuai dengan metode pembayaran yang tersedia pada sistem.</li>
                            <li>Seluruh biaya yang tercantum pada saat pemesanan merupakan biaya yang harus dibayarkan oleh pengguna.</li>
                            <li>Kegagalan pembayaran dapat menyebabkan pemesanan dibatalkan secara otomatis.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">5. Pembatalan dan Pengembalian Dana</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Pengguna dapat mengajukan pembatalan sesuai dengan kebijakan yang berlaku.</li>
                            <li>Pengembalian dana, apabila tersedia, akan diproses sesuai ketentuan penyedia layanan.</li>
                            <li>Waktu pengembalian dana dapat berbeda tergantung metode pembayaran yang digunakan.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">6. Kewajiban Pengguna</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Menggunakan kendaraan sesuai dengan peraturan perundang-undangan yang berlaku.</li>
                            <li>Menjaga kondisi kendaraan selama masa penyewaan.</li>
                            <li>Mengembalikan kendaraan tepat waktu sesuai dengan periode sewa yang telah disepakati.</li>
                            <li>Tidak menggunakan kendaraan untuk kegiatan yang melanggar hukum.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">7. Tanggung Jawab</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Pengelola berupaya menjaga keakuratan informasi yang ditampilkan pada website.</li>
                            <li>Pengelola tidak bertanggung jawab atas kerugian yang timbul akibat kesalahan pengguna dalam menggunakan layanan.</li>
                            <li>Pengelola berhak melakukan perubahan, pembaruan, atau penghentian layanan sewaktu-waktu apabila diperlukan.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">8. Ulasan dan Testimoni</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Pengguna dapat memberikan ulasan dan testimoni setelah menggunakan layanan.</li>
                            <li>Pengguna dilarang mengunggah konten yang mengandung unsur SARA, ujaran kebencian, pornografi, atau informasi yang tidak benar.</li>
                            <li>Pengelola berhak menghapus ulasan yang melanggar ketentuan.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">9. Privasi Data</h2>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Data pengguna akan digunakan untuk keperluan layanan dan pengelolaan transaksi.</li>
                            <li>Pengelola berkomitmen menjaga kerahasiaan data pengguna sesuai dengan kebijakan privasi yang berlaku.</li>
                            <li>Data pengguna tidak akan disebarluaskan kepada pihak lain tanpa persetujuan, kecuali diwajibkan oleh hukum.</li>
                        </ol>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">10. Perubahan Ketentuan</h2>
                        <p>Pengelola berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan berlaku setelah dipublikasikan pada website.</p>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">11. Persetujuan</h2>
                        <p>Dengan menggunakan website ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-frontliner.footer />

</body>

</html>
