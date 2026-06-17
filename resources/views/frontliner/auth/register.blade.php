<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Rental Mobil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #e9ebf1;
            --text: #1f2635;
            --muted: #6f7687;
            --line: #d6dbe6;
            --input: #edf0f6;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
        .page { min-height: 100vh; display: grid; grid-template-columns: 1.05fr 1fr; overflow: hidden; }
        .hero {
            position: relative;
            overflow: hidden;
            padding: 28px 26px;
            color: #fff;
            background:
                linear-gradient(180deg, rgba(5, 16, 58, 0.9), rgba(8, 42, 123, 0.9)),
                url('https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .brand { font-size: 28px; font-weight: 600; }
        .eyebrow { color: #6afad0; letter-spacing: 3px; text-transform: uppercase; font-size: 11px; }
        .hero-copy h1 { margin: 10px 0 0; font-size: clamp(40px, 5vw, 68px); line-height: 1.02; }
        .hero-copy p { margin-top: 16px; max-width: 490px; color: rgba(255,255,255,.84); font-weight: 300; line-height: 1.5; }
        .stats { display: flex; gap: 34px; margin-top: 28px; }
        .stat strong { display: block; font-size: 34px; line-height: 1; }
        .stat span { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,.78); }
        .form-area { position: relative; display: flex; align-items: center; justify-content: center; padding: 38px 28px; }
        .card { width: min(480px, 100%); }
        .back-link {
            position: absolute;
            top: 28px;
            left: 28px;
            display: inline-flex;
            align-items: center;
            color: #5d6680;
            text-decoration: none;
            font-size: 24px;
            font-weight: 600;
            line-height: 1;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .back-link:hover {
            color: #1a51d6;
            transform: translateX(-1px);
        }
        .form-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }
        .form-brand img {
            width: 182px;
            max-width: 100%;
            height: auto;
            display: block;
        }
        .card h2 { margin: 0; font-size: 42px; font-weight: 500; }
        .subtitle { margin-top: 8px; font-size: 14px; color: var(--muted); }
        .feedback { margin-top: 14px; padding: 11px 12px; border-radius: 10px; font-size: 13px; display: none; }
        .feedback.error { display: block; background: #ffe8ea; color: #8d1f2e; }
        .feedback.success { display: block; background: #e6f8eb; color: #1b6b3a; }
        form { margin-top: 20px; }
        .field { margin-bottom: 14px; }
        .field label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 500; }
        .field input {
            width: 100%; border: 1px solid transparent; background: var(--input); border-radius: 10px;
            padding: 12px 14px; outline: none; font-family: inherit; font-size: 14px;
        }
        .field input:focus { border-color: #86a5eb; }
        .tos { display: flex; align-items: start; gap: 10px; margin: 12px 0 2px; font-size: 12px; color: #5f6780; }
        .tos input { margin-top: 2px; }
        .tos a { color: #284fb8; text-decoration: none; }
        .submit-btn {
            margin-top: 14px; width: 100%; border: 0; border-radius: 999px; padding: 14px;
            background: linear-gradient(180deg, #1a51d6, #0c3db4); color: #fff; font-size: 15px; font-weight: 600; cursor: pointer;
        }
        .submit-btn:disabled { opacity: .72; cursor: not-allowed; }
        .divider { margin: 22px 0 14px; display: flex; align-items: center; gap: 12px; color: #7c8497; font-size: 12px; }
        .divider::before, .divider::after { content: ""; height: 1px; flex: 1; background: var(--line); }
        .socials { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .social { border: 0; border-radius: 999px; padding: 11px; background: #d8dff0; color: #1f2635; opacity: .8; cursor: not-allowed; }
        .social.apple { background: #17213d; color: #fff; }
        .switch-link { margin-top: 18px; text-align: center; font-size: 13px; color: #6f7687; }
        .switch-link a { color: #2f58c1; text-decoration: none; font-weight: 600; }
        .footer { grid-column: 1 / -1; border-top: 1px solid var(--line); padding: 14px 24px; text-align: right; font-size: 10px; color: #687086; letter-spacing: 1px; text-transform: uppercase; }
        .hero,
        .form-area {
            transition: transform 680ms cubic-bezier(0.2, 1, 0.22, 1);
            will-change: transform;
        }
        .hero-copy,
        .card,
        .back-link {
            transition: opacity 360ms ease, transform 520ms cubic-bezier(0.2, 1, 0.22, 1);
        }
        body.auth-stage-enter .hero { transform: translateX(-100%); }
        body.auth-stage-enter .form-area { transform: translateX(100%); }
        body.auth-stage-enter .hero-copy,
        body.auth-stage-enter .card,
        body.auth-stage-enter .back-link {
            opacity: 0;
            transform: translateY(16px);
        }
        body.auth-panels-open .hero,
        body.auth-panels-open .form-area { transform: translateX(0); }
        body.auth-panels-open .hero-copy,
        body.auth-panels-open .card,
        body.auth-panels-open .back-link {
            opacity: 1;
            transform: translateY(0);
        }
        body.auth-panels-closing .hero { transform: translateX(-100%); }
        body.auth-panels-closing .form-area { transform: translateX(100%); }
        body.auth-panels-closing .hero-copy,
        body.auth-panels-closing .card,
        body.auth-panels-closing .back-link {
            opacity: 0;
            transform: translateY(12px);
        }
        @media (max-width: 980px) {
            .page { grid-template-columns: 1fr; }
            .hero { min-height: 360px; }
            .card h2 { font-size: 34px; }
            .footer { text-align: center; }
            .back-link { top: 22px; left: 22px; }
        }

        /* Modal Styles */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-container {
            background: #fff;
            width: min(650px, 90%);
            max-height: 80vh;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .modal-backdrop.active .modal-container {
            transform: scale(1);
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text);
        }
        .modal-close {
            background: none;
            border: 0;
            font-size: 28px;
            color: var(--muted);
            cursor: pointer;
            line-height: 1;
            padding: 0;
            transition: color 0.2s;
        }
        .modal-close:hover {
            color: var(--text);
        }
        .modal-body {
            padding: 24px;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.6;
            color: #3f4756;
            text-align: left;
        }
        .modal-body h4 {
            margin: 18px 0 8px;
            color: var(--text);
            font-size: 16px;
            font-weight: 600;
            border-bottom: 1px solid var(--line);
            padding-bottom: 6px;
        }
        .modal-body h4:first-of-type {
            margin-top: 0;
        }
        .modal-body p {
            margin: 0 0 12px;
        }
        .modal-body ol {
            margin: 0 0 16px;
            padding-left: 20px;
        }
        .modal-body ol li {
            margin-bottom: 8px;
        }
        .modal-body ul {
            margin: 0 0 16px;
            padding-left: 20px;
        }
        .modal-body ul li {
            margin-bottom: 6px;
        }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: flex-end;
        }
        .modal-btn {
            border: 0;
            border-radius: 999px;
            padding: 10px 24px;
            background: #0d3fb8;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .modal-btn:hover {
            background: #0b3499;
        }
    </style>
</head>
<body class="auth-stage-enter">
    <div class="page">
        <aside class="hero">
            <div class="brand">Rental Mobil</div>
            <div class="hero-copy">
                <div class="eyebrow">Experience Luxury</div>
                <h1>Bergabunglah<br>dengan Eksklusivitas</h1>
                <p>Akses armada kendaraan premium terbaik dunia dengan layanan concierge pribadi yang mengerti standar Anda.</p>
                <div class="stats">
                    <div class="stat"><strong>500+</strong><span>Luxury Fleet</span></div>
                    <div class="stat"><strong>24/7</strong><span>Support</span></div>
                </div>
            </div>
        </aside>

        <main class="form-area">
            <a href="{{ route('login') }}" class="back-link auth-panel-link" aria-label="Kembali ke halaman login">
                <span aria-hidden="true">←</span>
            </a>
            <section class="card">
                <a href="{{ route('home') }}" class="form-brand auth-panel-link" aria-label="MD CAR RENTAL">
                    <img src="{{ asset('images/logo.png') }}" alt="MD CAR RENTAL">
                </a>
                <h2>Buat Akun Baru</h2>
                <p class="subtitle">Mulai perjalanan kemewahan Anda hari ini.</p>

                <div id="feedback" class="feedback"></div>

                <form id="registerForm" autocomplete="off">
                    <input id="redirect" name="redirect" type="hidden" value="{{ request('redirect') }}">
                    <div class="field">
                        <label for="name">Nama Lengkap</label>
                        <input id="name" name="name" type="text" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                    </div>

                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" placeholder="Pilih username" autocomplete="off" autocapitalize="none" spellcheck="false" required minlength="3">
                    </div>

                    <div class="field">
                        <label for="email">Alamat Email</label>
                        <input id="email" name="email" type="email" placeholder="Masukkan alamat email" autocomplete="email" autocapitalize="none" spellcheck="false" required>
                    </div>

                    <div class="field">
                        <label for="phone">Nomor Telepon</label>
                        <input id="phone" name="phone" type="text" placeholder="Masukkan nomor telepon" autocomplete="tel" required maxlength="15">
                    </div>

                    <div class="field">
                        <label for="password">Kata Sandi</label>
                        <input id="password" name="password" type="password" placeholder="Buat kata sandi" autocomplete="new-password" required minlength="8">
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required minlength="8">
                    </div>

                    <label class="tos">
                        <input id="agreement" type="checkbox" required>
                        <span>Saya menyetujui <a href="#" onclick="openModal('tosModal'); return false;">Syarat &amp; Ketentuan</a> serta <a href="#" onclick="openModal('privacyModal'); return false;">Kebijakan Privasi</a> Rental Mobil.</span>
                    </label>

                    <button id="submitBtn" type="submit" class="submit-btn">Daftar Sekarang</button>
                </form>

                <p class="switch-link">Sudah memiliki akun? <a href="{{ route('login') }}" class="auth-panel-link">Masuk Sekarang</a></p>
            </section>
        </main>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const feedback = document.getElementById('feedback');
        const submitBtn = document.getElementById('submitBtn');
        let authPanelNavigating = false;

        function openAuthPanels() {
            requestAnimationFrame(() => {
                document.body.classList.add('auth-panels-open');
                document.body.classList.remove('auth-stage-enter');
            });
        }

        function navigateWithPanels(url, delay = 540) {
            if (authPanelNavigating) return;
            authPanelNavigating = true;
            document.body.classList.remove('auth-panels-open');
            document.body.classList.add('auth-panels-closing');
            setTimeout(() => {
                window.location.href = url;
            }, delay);
        }

        function setFeedback(type, message) {
            feedback.className = `feedback ${type}`;
            feedback.textContent = message;
        }

        document.querySelectorAll('.auth-panel-link').forEach((link) => {
            link.addEventListener('click', (event) => {
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#')) return;
                event.preventDefault();
                navigateWithPanels(href);
            });
        });

        openAuthPanels();

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            feedback.className = 'feedback';
            feedback.textContent = '';

            const name = document.getElementById('name').value.trim();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const redirect = document.getElementById('redirect').value.trim();

            try {
                const response = await fetch('/api/v1/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name,
                        username,
                        email,
                        phone,
                        password,
                        password_confirmation: passwordConfirmation,
                        redirect,
                    }),
                    credentials: 'include',
                });

                const raw = await response.text();
                let result = null;

                try {
                    result = raw ? JSON.parse(raw) : {};
                } catch (e) {
                    result = {};
                }

                if (!response.ok || !result.success) {
                    let message = result.message || (response.status >= 500 ? 'Server sedang bermasalah. Coba lagi.' : 'Registrasi gagal.');

                    if (result.errors) {
                        const firstErrorKey = Object.keys(result.errors)[0];
                        const firstError = result.errors[firstErrorKey];
                        if (Array.isArray(firstError) && firstError.length > 0) {
                            message = firstError[0];
                        }
                    }

                    setFeedback('error', message);
                    return;
                }

                const nextUrl = result?.data?.redirect_to || '/login';

                setFeedback('success', 'Registrasi berhasil. Mengarahkan ke halaman login...');
                setTimeout(() => { navigateWithPanels(nextUrl, 540); }, 220);
            } catch (error) {
                setFeedback('error', 'Tidak bisa menghubungi server. Coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Daftar Sekarang';
            }
        });
    </script>

    <!-- Modal Syarat & Ketentuan -->
    <div id="tosModal" class="modal-backdrop">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Syarat &amp; Ketentuan</h3>
                <button class="modal-close" onclick="closeModal('tosModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Selamat datang di Website Rental Mobil. Dengan mengakses dan menggunakan layanan ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.</p>
                
                <h4>1. Ketentuan Umum</h4>
                <ol>
                    <li>Website ini menyediakan layanan informasi dan pemesanan kendaraan rental secara online.</li>
                    <li>Pengguna wajib memberikan data yang benar, lengkap, dan sesuai dengan identitas yang dimiliki.</li>
                    <li>Pengguna bertanggung jawab atas keamanan akun dan kerahasiaan informasi login.</li>
                </ol>

                <h4>2. Registrasi Akun</h4>
                <ol>
                    <li>Pengguna harus memiliki akun untuk melakukan pemesanan kendaraan.</li>
                    <li>Pengguna dilarang menggunakan identitas palsu atau milik pihak lain tanpa izin.</li>
                    <li>Pengelola berhak menonaktifkan akun yang terbukti memberikan informasi tidak valid atau melakukan penyalahgunaan layanan.</li>
                </ol>

                <h4>3. Pemesanan Kendaraan</h4>
                <ol>
                    <li>Pemesanan kendaraan dilakukan melalui sistem yang tersedia pada website.</li>
                    <li>Ketersediaan kendaraan mengikuti data yang tercantum pada sistem saat proses pemesanan dilakukan.</li>
                    <li>Pemesanan dianggap sah setelah pengguna menyelesaikan proses yang ditentukan oleh penyedia layanan.</li>
                </ol>

                <h4>4. Pembayaran</h4>
                <ol>
                    <li>Pembayaran dilakukan sesuai dengan metode pembayaran yang tersedia pada sistem.</li>
                    <li>Seluruh biaya yang tercantum pada saat pemesanan merupakan biaya yang harus dibayarkan oleh pengguna.</li>
                    <li>Kegagalan pembayaran dapat menyebabkan pemesanan dibatalkan secara otomatis.</li>
                </ol>

                <h4>5. Pembatalan dan Pengembalian Dana</h4>
                <ol>
                    <li>Pengguna dapat mengajukan pembatalan sesuai dengan kebijakan yang berlaku.</li>
                    <li>Pengembalian dana, apabila tersedia, akan diproses sesuai ketentuan penyedia layanan.</li>
                    <li>Waktu pengembalian dana dapat berbeda tergantung metode pembayaran yang digunakan.</li>
                </ol>

                <h4>6. Kewajiban Pengguna</h4>
                <ol>
                    <li>Menggunakan kendaraan sesuai dengan peraturan perundang-undangan yang berlaku.</li>
                    <li>Menjaga kondisi kendaraan selama masa penyewaan.</li>
                    <li>Mengembalikan kendaraan tepat waktu sesuai dengan periode sewa yang telah disepakati.</li>
                    <li>Tidak menggunakan kendaraan untuk kegiatan yang melanggar hukum.</li>
                </ol>

                <h4>7. Tanggung Jawab</h4>
                <ol>
                    <li>Pengelola berupaya menjaga keakuratan informasi yang ditampilkan pada website.</li>
                    <li>Pengelola tidak bertanggung jawab atas kerugian yang timbul akibat kesalahan pengguna dalam menggunakan layanan.</li>
                    <li>Pengelola berhak melakukan perubahan, pembaruan, atau penghentian layanan sewaktu-waktu apabila diperlukan.</li>
                </ol>

                <h4>8. Ulasan dan Testimoni</h4>
                <ol>
                    <li>Pengguna dapat memberikan ulasan dan testimoni setelah menggunakan layanan.</li>
                    <li>Pengguna dilarang mengunggah konten yang mengandung unsur SARA, ujaran kebencian, pornografi, atau informasi yang tidak benar.</li>
                    <li>Pengelola berhak menghapus ulasan yang melanggar ketentuan.</li>
                </ol>

                <h4>9. Privasi Data</h4>
                <ol>
                    <li>Data pengguna akan digunakan untuk keperluan layanan dan pengelolaan transaksi.</li>
                    <li>Pengelola berkomitmen menjaga kerahasiaan data pengguna sesuai dengan kebijakan privasi yang berlaku.</li>
                    <li>Data pengguna tidak akan disebarluaskan kepada pihak lain tanpa persetujuan, kecuali diwajibkan oleh hukum.</li>
                </ol>

                <h4>10. Perubahan Ketentuan</h4>
                <p>Pengelola berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan berlaku setelah dipublikasikan pada website.</p>

                <h4>11. Persetujuan</h4>
                <p>Dengan menggunakan website ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn" onclick="closeModal('tosModal')">Saya Mengerti</button>
            </div>
        </div>
    </div>

    <!-- Modal Kebijakan Privasi -->
    <div id="privacyModal" class="modal-backdrop">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Kebijakan Privasi</h3>
                <button class="modal-close" onclick="closeModal('privacyModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Website MdRentalCar berkomitmen untuk melindungi privasi dan keamanan data pribadi pengguna. Kebijakan Privasi ini menjelaskan bagaimana data pengguna dikumpulkan, digunakan, disimpan, dan dilindungi.</p>

                <h4>1. Informasi yang Kami Kumpulkan</h4>
                <ul>
                    <li><strong>Data Pribadi Pengguna</strong>: Nama lengkap, username, alamat email, dan kata sandi saat mendaftar.</li>
                    <li><strong>Data Verifikasi Identitas (e-KYC)</strong>: Foto Kartu Tanda Penduduk (KTP) serta foto selfie pemohon guna keperluan validasi identitas fisik sebelum pemesanan disetujui.</li>
                    <li><strong>Data Sewa &amp; Transaksi</strong>: Detail mobil yang dipilih, durasi sewa, status pembayaran, serta riwayat pembayaran.</li>
                </ul>

                <h4>2. Penggunaan Informasi</h4>
                <ul>
                    <li>Memproses dan memvalidasi pendaftaran akun serta otentikasi saat masuk.</li>
                    <li>Melakukan verifikasi identitas e-KYC demi keselamatan operasional armada kendaraan kami.</li>
                    <li>Memproses rincian transaksi sewa dan pembayaran secara aman melalui payment gateway resmi.</li>
                    <li>Mengirimkan notifikasi pemesanan, verifikasi dokumen, serta tanda terima pembayaran.</li>
                </ul>

                <h4>3. Penyimpanan dan Keamanan Data</h4>
                <ul>
                    <li>Semua data kata sandi dienkripsi menggunakan metode hashing satu arah yang aman.</li>
                    <li>Berkas verifikasi identitas (KTP dan selfie) disimpan dalam sistem database yang aman dan terenkripsi. Berkas identitas ini akan dihapus secara berkala dari penyimpanan lokal setelah periode sewa selesai guna mencegah penyalahgunaan data pribadi.</li>
                </ul>

                <h4>4. Pembagian Informasi dengan Pihak Ketiga</h4>
                <ul>
                    <li>Kami membagikan data transaksi secara aman kepada mitra payment gateway (seperti Midtrans) guna pemrosesan dan verifikasi pembayaran.</li>
                    <li>Kami berkomitmen tidak akan menjual, menyewakan, atau menyebarkan informasi pribadi Anda kepada pihak ketiga mana pun tanpa persetujuan, kecuali diwajibkan oleh peraturan hukum yang berlaku.</li>
                </ul>

                <h4>5. Hak Pengguna</h4>
                <ul>
                    <li>Pengguna berhak memeriksa, mengoreksi, atau memperbarui informasi profil akun pribadi mereka.</li>
                    <li>Pengguna berhak mengajukan permohonan penonaktifan akun serta penghapusan data pribadi apabila memutuskan untuk tidak lagi menggunakan layanan kami.</li>
                </ul>

                <h4>6. Perubahan Kebijakan</h4>
                <p>Kami berhak melakukan perubahan pada Kebijakan Privasi ini sewaktu-waktu. Perubahan kebijakan akan mulai berlaku segera setelah dipublikasikan pada halaman ini.</p>
                <h4>7. Cookie dan Teknologi Serupa</h4>
                <p>Website dapat menggunakan cookie untuk meningkatkan pengalaman pengguna, menyimpan preferensi, dan membantu analisis penggunaan layanan.</p>
                <h4>8. Persetujuan</h4>
                <p>Dengan menggunakan website ini, pengguna dianggap telah membaca, memahami, dan menyetujui Kebijakan Privasi yang berlaku.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn" onclick="closeModal('privacyModal')">Saya Mengerti</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
        }
        // Close modal when clicking backdrop area
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    closeModal(backdrop.id);
                }
            });
        });
    </script>
</body>
</html>
