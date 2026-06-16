<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rental Mobil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #e9ebf1;
            --text: #1f2635;
            --muted: #6f7687;
            --primary: #0d3fb8;
            --primary-dark: #0b3499;
            --line: #d6dbe6;
            --input: #edf0f6;
            --panel-blue: #0d2f8f;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 28px 26px;
            color: #fff;
            background:
                linear-gradient(180deg, rgba(8, 22, 64, 0.9), rgba(7, 31, 111, 0.92)),
                url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand {
            font-size: 26px;
            font-weight: 500;
            letter-spacing: 0.4px;
        }

        .hero-copy h1 {
            margin: 0;
            font-size: clamp(34px, 4vw, 56px);
            line-height: 1.03;
            font-weight: 500;
        }

        .hero-copy p {
            margin-top: 14px;
            max-width: 460px;
            color: rgba(255, 255, 255, 0.82);
            font-weight: 300;
            line-height: 1.5;
            font-size: 15px;
        }

        .stats {
            display: flex;
            gap: 38px;
            margin-top: 36px;
        }

        .stat strong {
            display: block;
            font-size: 26px;
            font-weight: 600;
        }

        .stat span {
            font-size: 11px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.78);
        }

        .form-area {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 46px 28px;
        }

        .card {
            width: min(460px, 100%);
            background: transparent;
        }

        .back-link {
            position: absolute;
            top: 28px;
            left: 28px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #5d6680;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link-icon {
            font-size: 24px;
            font-weight: 600;
            line-height: 1;
        }

        .back-link:hover {
            color: #1a51d6;
            transform: translateX(-1px);
        }

        .form-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .form-brand img {
            width: 178px;
            max-width: 100%;
            height: auto;
            display: block;
        }

        .card h2 {
            margin: 0;
            font-size: 38px;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .card .subtitle {
            margin-top: 8px;
            color: var(--muted);
            line-height: 1.5;
            font-size: 14px;
        }

        .feedback {
            margin-top: 16px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            display: none;
        }

        .feedback.error {
            display: block;
            background: #ffe8ea;
            color: #8d1f2e;
        }

        .feedback.success {
            display: block;
            background: #e6f8eb;
            color: #1b6b3a;
        }

        form { margin-top: 22px; }

        .field { margin-bottom: 15px; }

        .field-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .field label {
            font-size: 13px;
            color: #2c3343;
            font-weight: 500;
        }

        .field a {
            font-size: 12px;
            color: #3059c0;
            text-decoration: none;
            font-weight: 500;
        }

        .field input {
            width: 100%;
            border: 1px solid transparent;
            background: var(--input);
            border-radius: 9px;
            padding: 12px 14px;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            color: #2e3342;
            transition: border-color 0.2s ease;
        }

        .field input:focus { border-color: #85a3ec; }

        .submit-btn {
            margin-top: 10px;
            width: 100%;
            border: 0;
            border-radius: 999px;
            padding: 14px;
            background: linear-gradient(180deg, #1a51d6, #0c3db4);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s ease, filter 0.2s ease;
        }

        .submit-btn:hover { filter: brightness(0.98); }
        .submit-btn:active { transform: translateY(1px); }
        .submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }

        .divider {
            margin: 24px 0 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #7c8497;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: var(--line);
        }

        .socials {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .social {
            border: 0;
            background: #d8dff0;
            color: #1f2635;
            border-radius: 999px;
            padding: 11px;
            font-family: inherit;
            font-weight: 500;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .register-link {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: #6f7687;
        }

        .register-link a {
            color: #2f58c1;
            text-decoration: none;
            font-weight: 600;
        }

        .footer {
            grid-column: 1 / -1;
            border-top: 1px solid var(--line);
            padding: 14px 24px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px 24px;
            color: #687086;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .footer .links {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        @media (max-width: 980px) {
            .page { grid-template-columns: 1fr; }
            .hero { min-height: 390px; }
            .card h2 { font-size: 32px; }
            .footer { font-size: 10px; }
            .back-link {
                top: 22px;
                left: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <aside class="hero">
            <div class="brand"></div>
            <div class="hero-copy">
                <h1>Selamat<br>Datang Kembali</h1>
                <p>Rasakan kebebasan berkendara dengan armada eksklusif kami. Perjalanan premium Anda dimulai dari sini.</p>
                <div class="stats">
                    <div class="stat">
                        <strong>{{ $cars - 1 }}+</strong>
                        <span>Armada</span>
                    </div>
                    <div class="stat">
                        <strong>24/7</strong>
                        <span>Layanan Pelanggan</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="form-area">
            <a href="{{ route('home') }}" class="back-link" aria-label="Kembali ke beranda">
                <span class="back-link-icon" aria-hidden="true">←</span>
                <span>Kembali ke Beranda</span>
            </a>
            <section class="card">
                <a href="{{ route('home') }}" class="form-brand" aria-label="MD CAR RENTAL">
                    <img src="{{ asset('images/logo.png') }}" alt="MD CAR RENTAL">
                </a>
                <h2>Masuk ke Akun</h2>
                <p class="subtitle">Masuk untuk mengelola reservasi dan akses layanan concierge.</p>

                <div id="feedback" class="feedback"></div>

                <form id="loginForm" autocomplete="off">
                    <input id="redirect" name="redirect" type="hidden" value="{{ request('redirect') }}">
                    <input type="text" name="fake_username" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">
                    <input type="password" name="fake_password" autocomplete="current-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">
                    <div class="field">
                        <div class="field-row">
                            <label for="login">Username atau Email</label>
                        </div>
                        <input id="login" name="account_login" type="text" placeholder="Masukkan username atau email" autocomplete="off" autocapitalize="none" spellcheck="false" required>
                    </div>

                    <div class="field">
                        <div class="field-row">
                            <label for="password">Kata Sandi</label>
                            <a href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
                        </div>
                        <input id="password" name="account_password" type="password" placeholder="Masukkan kata sandi" autocomplete="off" required>
                    </div>

                    <button id="submitBtn" type="submit" class="submit-btn">Masuk</button>
                </form>

                <p class="register-link">Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a></p>
            </section>
        </main>
    </div>

    <script>
        const form = document.getElementById('loginForm');
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

            const login = document.getElementById('login').value.trim();
            const password = document.getElementById('password').value;
            const redirect = document.getElementById('redirect').value.trim();

            try {
                const response = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ login, password, redirect }),
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
                    let message = result.message || (response.status >= 500 ? 'Server sedang bermasalah. Coba lagi.' : 'Login gagal.');

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

                setFeedback('success', 'Login berhasil. Mengarahkan...');

                const nextUrl = result?.data?.redirect_to || (result?.data?.user?.role === 'admin' ? '/dashboard' : '/frontliner');

                setTimeout(() => {
                    window.location.replace(nextUrl);
                }, 700);
            } catch (error) {
                setFeedback('error', 'Tidak bisa menghubungi server. Coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Masuk';
            }
        });
    </script>
</body>
</html>
