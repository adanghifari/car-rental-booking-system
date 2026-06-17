<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Rental Mobil</title>
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
            overflow: hidden;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            overflow: hidden;
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

        .back-to-login {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: #6f7687;
        }

        .back-to-login a {
            color: #2f58c1;
            text-decoration: none;
            font-weight: 600;
        }

        .hero,
        .form-area {
            transition: transform 980ms cubic-bezier(0.2, 1, 0.22, 1);
            will-change: transform;
        }

        .hero-copy,
        .card,
        .back-link {
            transition: opacity 520ms ease, transform 760ms cubic-bezier(0.2, 1, 0.22, 1);
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
            .hero { min-height: 300px; }
            .card h2 { font-size: 32px; }
            .back-link {
                top: 22px;
                left: 22px;
            }
        }
    </style>
</head>
<body class="auth-stage-enter">
    <div class="page">
        <aside class="hero">
            <div class="brand">Rental Mobil</div>
            <div class="hero-copy">
                <h1>Atur Ulang<br>Kata Sandi</h1>
                <p>Masukkan alamat email Anda untuk menerima tautan khusus penyetelan ulang kata sandi.</p>
                <div class="stats">
                    <div class="stat">
                        <strong>500+</strong>
                        <span>Armada Mewah</span>
                    </div>
                    <div class="stat">
                        <strong>24/7</strong>
                        <span>Layanan Concierge</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="form-area">
            <a href="{{ route('login') }}" class="back-link auth-panel-link" aria-label="Kembali ke halaman masuk">
                <span class="back-link-icon" aria-hidden="true">←</span>
                <span>Kembali ke Halaman Masuk</span>
            </a>
            <section class="card">
                <a href="{{ route('home') }}" class="form-brand auth-panel-link" aria-label="MD CAR RENTAL">
                    <img src="{{ asset('images/logo.png') }}" alt="MD CAR RENTAL">
                </a>
                <h2>Lupa Kata Sandi?</h2>
                <p class="subtitle">Kami akan mengirimkan instruksi penyetelan ulang melalui email Anda.</p>

                <div id="feedback" class="feedback"></div>

                <form id="forgotForm" autocomplete="off">
                    <div class="field">
                        <div class="field-row">
                            <label for="email">Alamat Email</label>
                        </div>
                        <input id="email" name="email" type="email" placeholder="Masukkan email terdaftar Anda" autocomplete="off" autocapitalize="none" spellcheck="false" required>
                    </div>

                    <button id="submitBtn" type="submit" class="submit-btn">Kirim Tautan Reset</button>
                </form>

                <p class="back-to-login">Ingat kata sandi Anda? <a href="{{ route('login') }}" class="auth-panel-link">Masuk</a></p>
            </section>
        </main>
    </div>

    <script>
        const form = document.getElementById('forgotForm');
        const feedback = document.getElementById('feedback');
        const submitBtn = document.getElementById('submitBtn');
        let authPanelNavigating = false;

        function openAuthPanels() {
            requestAnimationFrame(() => {
                document.body.classList.add('auth-panels-open');
                document.body.classList.remove('auth-stage-enter');
            });
        }

        function navigateWithPanels(url, delay = 760) {
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
            submitBtn.textContent = 'Mengirim...';
            feedback.className = 'feedback';
            feedback.textContent = '';

            const email = document.getElementById('email').value.trim();

            try {
                const response = await fetch('/api/v1/auth/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email }),
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
                    let message = result.message || (response.status >= 500 ? 'Server sedang bermasalah. Coba lagi.' : 'Gagal mengirim email.');

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

                setFeedback('success', result.message || 'Tautan penyetelan ulang berhasil dikirim.');
                form.reset();
            } catch (error) {
                setFeedback('error', 'Tidak bisa menghubungi server. Coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Tautan Reset';
            }
        });
    </script>
</body>
</html>
