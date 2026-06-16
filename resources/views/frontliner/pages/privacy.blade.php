<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Rental Mobil</title>
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
                    <span class="text-white font-medium">Kebijakan Privasi</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Kebijakan Privasi</h1>
                <p class="text-sm text-blue-100 font-light">
                    Penjelasan mengenai bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi Anda.
                </p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-6 text-sm text-gray-600 leading-relaxed">
                <p>Website MdRentalCar berkomitmen untuk melindungi privasi dan keamanan data pribadi pengguna. Kebijakan Privasi ini menjelaskan bagaimana data pengguna dikumpulkan, digunakan, disimpan, dan dilindungi.</p>
                
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">1. Informasi yang Kami Kumpulkan</h2>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Data Pribadi Pengguna</strong>: Nama lengkap, username, alamat email, dan kata sandi saat mendaftar.</li>
                            <li><strong>Data Verifikasi Identitas (e-KYC)</strong>: Foto Kartu Tanda Penduduk (KTP) serta foto selfie pemohon guna keperluan validasi identitas fisik sebelum pemesanan disetujui.</li>
                            <li><strong>Data Sewa &amp; Transaksi</strong>: Detail mobil yang dipilih, durasi sewa, status pembayaran, serta riwayat pembayaran.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">2. Penggunaan Informasi</h2>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Memproses dan memvalidasi pendaftaran akun serta otentikasi saat masuk.</li>
                            <li>Melakukan verifikasi identitas e-KYC demi keselamatan operasional armada kendaraan kami.</li>
                            <li>Memproses rincian transaksi sewa dan pembayaran secara aman melalui payment gateway resmi.</li>
                            <li>Mengirimkan notifikasi pemesanan, verifikasi dokumen, serta tanda terima pembayaran.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">3. Penyimpanan dan Keamanan Data</h2>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Semua data kata sandi dienkripsi menggunakan metode hashing satu arah yang aman.</li>
                            <li>Berkas verifikasi identitas (KTP dan selfie) disimpan dalam sistem database yang aman and terenkripsi. Berkas identitas ini akan dihapus secara berkala dari penyimpanan lokal setelah periode sewa selesai guna mencegah penyalahgunaan data pribadi.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">4. Pembagian Informasi dengan Pihak Ketiga</h2>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Kami membagikan data transaksi secara aman kepada mitra payment gateway (seperti Midtrans) guna pemrosesan dan verifikasi pembayaran.</li>
                            <li>Kami berkomitmen tidak akan menjual, menyewakan, atau menyebarkan informasi pribadi Anda kepada pihak ketiga mana pun tanpa persetujuan, kecuali diwajibkan oleh peraturan hukum yang berlaku.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">5. Hak Pengguna</h2>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Pengguna berhak memeriksa, mengoreksi, atau memperbarui informasi profil akun pribadi mereka.</li>
                            <li>Pengguna berhak mengajukan permohonan penonaktifan akun serta penghapusan data pribadi apabila memutuskan untuk tidak lagi menggunakan layanan kami.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">6. Perubahan Kebijakan</h2>
                        <p>Kami berhak melakukan perubahan pada Kebijakan Privasi ini sewaktu-waktu. Perubahan kebijakan akan mulai berlaku segera setelah dipublikasikan pada halaman ini.</p>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">7. Cookie dan Teknologi Serupa</h2>
                        <p>Website dapat menggunakan cookie untuk meningkatkan pengalaman pengguna, menyimpan preferensi, dan membantu analisis penggunaan layanan.</p>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">8. Persetujuan</h2>
                        <p>Dengan menggunakan website ini, pengguna dianggap telah membaca, memahami, dan menyetujui Kebijakan Privasi yang berlaku.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-frontliner.footer />

</body>

</html>
