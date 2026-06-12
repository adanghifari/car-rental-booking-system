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
        body { margin: 0; font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); }
        .page { min-height: 100vh; display: grid; grid-template-columns: 1.05fr 1fr; }
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
        .form-area { display: flex; align-items: center; justify-content: center; padding: 38px 28px; }
        .card { width: min(480px, 100%); }
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
        @media (max-width: 980px) {
            .page { grid-template-columns: 1fr; }
            .hero { min-height: 360px; }
            .card h2 { font-size: 34px; }
            .footer { text-align: center; }
        }
    </style>
</head>
<body>
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
            <section class="card">
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
                        <label for="password">Kata Sandi</label>
                        <input id="password" name="password" type="password" placeholder="Buat kata sandi" autocomplete="new-password" required minlength="8">
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required minlength="8">
                    </div>

                    <label class="tos">
                        <input id="agreement" type="checkbox" required>
                        <span>Saya menyetujui <a href="#" onclick="return false;">Syarat &amp; Ketentuan</a> serta <a href="#" onclick="return false;">Kebijakan Privasi</a> Rental Mobil.</span>
                    </label>

                    <button id="submitBtn" type="submit" class="submit-btn">Daftar Sekarang</button>
                </form>

                <p class="switch-link">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk Sekarang</a></p>
            </section>
        </main>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const feedback = document.getElementById('feedback');
        const submitBtn = document.getElementById('submitBtn');

        function setFeedback(type, message) {
            feedback.className = `feedback ${type}`;
            feedback.textContent = message;
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            feedback.className = 'feedback';
            feedback.textContent = '';

            const name = document.getElementById('name').value.trim();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
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
                setTimeout(() => { window.location.replace(nextUrl); }, 900);
            } catch (error) {
                setFeedback('error', 'Tidak bisa menghubungi server. Coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Daftar Sekarang';
            }
        });
    </script>
</body>
</html>
