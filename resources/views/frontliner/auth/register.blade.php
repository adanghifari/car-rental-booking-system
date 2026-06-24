<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
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

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Outfit', sans-serif;
        background: var(--bg);
        color: var(--text);
        /* overflow: hidden; Removed to allow scrolling on small/medium heights */
    }

    .page {
        height: 100vh;
        height: 100dvh;
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
            linear-gradient(180deg, rgba(5, 16, 58, 0.9), rgba(8, 42, 123, 0.9)),
            url('https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .brand {
        font-size: 28px;
        font-weight: 600;
    }

    .eyebrow {
        color: #6afad0;
        letter-spacing: 3px;
        text-transform: uppercase;
        font-size: 11px;
    }

    .hero-copy h1 {
        margin: 10px 0 0;
        font-size: clamp(40px, 5vw, 68px);
        line-height: 1.02;
    }

    .hero-copy p {
        margin-top: 16px;
        max-width: 490px;
        color: rgba(255, 255, 255, .84);
        font-weight: 300;
        line-height: 1.5;
    }

    .stats {
        display: flex;
        gap: 34px;
        margin-top: 28px;
    }

    .stat strong {
        display: block;
        font-size: 34px;
        line-height: 1;
    }

    .stat span {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, .78);
    }

    .form-area {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 38px 28px;
        height: 100%;
        justify-content: center;
        overflow-y: auto;
    }

    .card {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .card h2 {
        margin: 0 0 8px;
        font-size: 40px;
        font-weight: 600;
        letter-spacing: -1px;
    }

    .card p {
        margin: 0 0 32px;
        color: var(--muted);
        font-size: 15px;
    }

    .auth-btn {
        width: 100%;
        padding: 14px;
        background: #1f2635;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 500;
        font-size: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 24px;
        transition: all .2s;
    }

    .auth-btn:hover {
        background: #000;
        transform: translateY(-1px);
    }

    .auth-btn svg {
        width: 18px;
        height: 18px;
    }

    .links {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        font-size: 14px;
    }

    .links a {
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
    }

    .links a:hover {
        text-decoration: underline;
    }

    .back-link {
        position: absolute;
        top: 40px;
        left: 40px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }

    .grecaptcha-badge {
        visibility: hidden !important;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-danger {
        background: #fdf2f2;
        color: #9b1c1c;
        border: 1px solid #fbd5d5;
    }

    .alert-success {
        background: #f3faf7;
        color: #03543f;
        border: 1px solid #def7ec;
    }

    .alert svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }

    .form-group.full {
        grid-column: span 2;
        margin-bottom: 0;
    }

    .form-group.half-margin {
        margin-bottom: 0;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper input,
    .input-wrapper textarea {
        width: 100%;
        padding: 12px 14px;
        background: var(--input);
        border: 1px solid transparent;
        border-radius: 10px;
        font-family: inherit;
        font-size: 14px;
        color: var(--text);
        transition: all .2s;
    }

    .input-wrapper input:focus,
    .input-wrapper textarea:focus {
        outline: none;
        background: #fff;
        border-color: var(--line);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }

    .input-wrapper input.has-icon {
        padding-left: 40px;
    }

    .input-wrapper svg.field-icon {
        position: absolute;
        left: 14px;
        width: 16px;
        height: 16px;
        color: var(--muted);
        pointer-events: none;
    }

    .invalid-feedback {
        color: #e02424;
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }

    .wizard-progress {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 32px;
        background: var(--line);
        height: 2px;
        border-radius: 2px;
    }

    .wizard-progress-bar {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background: #1f2635;
        width: 0%;
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .wizard-step {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: -15px;
    }

    .wizard-step-node {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        transition: all 0.4s;
    }

    .wizard-step.active .wizard-step-node {
        border-color: #1f2635;
        background: #1f2635;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(31, 38, 53, 0.1);
    }

    .wizard-step.completed .wizard-step-node {
        border-color: #10b981;
        background: #10b981;
        color: #fff;
    }

    .wizard-step-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 6px;
        color: var(--muted);
        transition: color 0.4s;
    }

    .wizard-step.active .wizard-step-label {
        color: var(--text);
    }

    .wizard-step.completed .wizard-step-label {
        color: #10b981;
    }

    .wizard-pane {
        display: none;
    }

    .wizard-pane.active {
        display: block;
        animation: fadeIn .4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-row {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-secondary {
        flex: 1;
        padding: 14px;
        background: #fff;
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: 10px;
        font-weight: 500;
        font-size: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all .2s;
    }

    .btn-secondary:hover {
        background: var(--input);
    }

    .btn-primary {
        flex: 2;
        margin-top: 0;
    }

    .file-upload-box {
        border: 2px dashed var(--line);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: var(--input);
        cursor: pointer;
        transition: all .2s;
    }

    .file-upload-box:hover {
        border-color: var(--text);
        background: #fff;
    }

    .file-upload-box svg {
        width: 32px;
        height: 32px;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .file-upload-box p {
        margin: 0;
        font-size: 14px;
        color: var(--muted);
    }

    .file-upload-box strong {
        color: var(--text);
        font-weight: 500;
    }

    .file-preview {
        display: none;
        margin-top: 12px;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        max-height: 150px;
        border: 1px solid var(--line);
    }

    .file-preview img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .file-preview-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .2s;
    }

    .file-preview-remove:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    .form-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 16px;
        font-size: 13px;
        color: var(--muted);
        line-height: 1.4;
    }

    .form-checkbox input {
        margin-top: 3px;
    }

    .form-checkbox a {
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
    }

    .form-checkbox a:hover {
        text-decoration: underline;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 100;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-box {
        background: #fff;
        border-radius: 20px;
        max-width: 600px;
        width: 100%;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        animation: modalIn .3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes modalIn {
        from {
            transform: scale(0.95);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 24px 32px;
        border-bottom: 1px solid var(--line);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--muted);
    }

    .modal-body {
        padding: 32px;
        overflow-y: auto;
        font-size: 14px;
        line-height: 1.6;
        color: var(--muted);
    }

    .modal-body h4 {
        color: var(--text);
        margin: 24px 0 8px;
        font-size: 15px;
        font-weight: 600;
    }

    .modal-body h4:first-child {
        margin-top: 0;
    }

    .modal-body ol {
        padding-left: 20px;
        margin: 0 0 16px;
    }

    .modal-body li {
        margin-bottom: 8px;
    }

    .modal-footer {
        padding: 20px 32px;
        border-top: 1px solid var(--line);
        display: flex;
        justify-content: flex-end;
    }

    .modal-btn {
        padding: 12px 24px;
        background: #1f2635;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
    }

    .modal-btn:hover {
        background: #000;
    }

    @media (max-width: 980px) {
        .page {
            grid-template-columns: 1fr;
        }

        .hero {
            min-height: 360px;
        }

        .card h2 {
            font-size: 34px;
        }

        .footer {
            text-align: center;
        }

        .back-link {
            top: 22px;
            left: 22px;
        }
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
                <p>Akses armada kendaraan premium terbaik dunia dengan layanan concierge pribadi yang mengerti standar
                    Anda.</p>
                <div class="stats">
                    <div class="stat"><strong>{{ $cars }}</strong><span>Armada</span></div>
                    <div class="stat"><strong>24/7</strong><span>Layanan Pelanggan</span></div>
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
                        <input id="name" name="name" type="text" placeholder="Masukkan nama lengkap" autocomplete="name"
                            required>
                    </div>

                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" placeholder="Pilih username" autocomplete="off"
                            autocapitalize="none" spellcheck="false" required minlength="3">
                    </div>

                    <div class="field">
                        <label for="email">Alamat Email</label>
                        <input id="email" name="email" type="email" placeholder="Masukkan alamat email"
                            autocomplete="email" autocapitalize="none" spellcheck="false" required>
                    </div>

                    <div class="field">
                        <label for="phone">Nomor Telepon</label>
                        <input id="phone" name="phone" type="text" placeholder="Masukkan nomor telepon"
                            autocomplete="tel" required maxlength="15">
                    </div>

                    <div class="field">
                        <label for="password">Kata Sandi</label>
                        <div class="password-field">
                            <input id="password" name="password" type="password" placeholder="Buat kata sandi"
                                autocomplete="new-password" required minlength="8">
                            <button type="button" class="password-toggle" data-password-target="password"
                                aria-label="Tampilkan kata sandi">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="password-field">
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                placeholder="Ulangi kata sandi" autocomplete="new-password" required minlength="8">
                            <button type="button" class="password-toggle" data-password-target="password_confirmation"
                                aria-label="Tampilkan konfirmasi kata sandi">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="tos">
                        <input id="agreement" type="checkbox" required>
                        <span>Saya menyetujui <a href="#" onclick="openModal('tosModal'); return false;">Syarat &amp;
                                Ketentuan</a> serta <a href="#"
                                onclick="openModal('privacyModal'); return false;">Kebijakan Privasi</a> Rental
                            Mobil.</span>
                    </label>

                    <button id="submitBtn" type="submit" class="submit-btn">Daftar Sekarang</button>
                </form>

                <p class="switch-link">Sudah memiliki akun? <a href="{{ route('login') }}" class="auth-panel-link">Masuk
                        Sekarang</a></p>
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

    document.querySelectorAll('.password-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordTarget);
            if (!input) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' :
                'Tampilkan kata sandi');
        });
    });

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
                let message = result.message || (response.status >= 500 ?
                    'Server sedang bermasalah. Coba lagi.' : 'Registrasi gagal.');

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
            setTimeout(() => {
                navigateWithPanels(nextUrl, 540);
            }, 220);
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
                <p>Selamat datang di Website Rental Mobil. Dengan mengakses dan menggunakan layanan ini, pengguna
                    dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.</p>

                <h4>1. Ketentuan Umum</h4>
                <ol>
                    <li>Website ini menyediakan layanan informasi dan pemesanan kendaraan rental secara online.</li>
                    <li>Pengguna wajib memberikan data yang benar, lengkap, dan sesuai dengan identitas yang dimiliki.
                    </li>
                    <li>Pengguna bertanggung jawab atas keamanan akun dan kerahasiaan informasi login.</li>
                </ol>

                <h4>2. Registrasi Akun</h4>
                <ol>
                    <li>Pengguna harus memiliki akun untuk melakukan pemesanan kendaraan.</li>
                    <li>Pengguna dilarang menggunakan identitas palsu atau milik pihak lain tanpa izin.</li>
                    <li>Pengelola berhak menonaktifkan akun yang terbukti memberikan informasi tidak valid atau
                        melakukan penyalahgunaan layanan.</li>
                </ol>

                <h4>3. Pemesanan Kendaraan</h4>
                <ol>
                    <li>Pemesanan kendaraan dilakukan melalui sistem yang tersedia pada website.</li>
                    <li>Ketersediaan kendaraan mengikuti data yang tercantum pada sistem saat proses pemesanan
                        dilakukan.</li>
                    <li>Pemesanan dianggap sah setelah pengguna menyelesaikan proses yang ditentukan oleh penyedia
                        layanan.</li>
                </ol>

                <h4>4. Pembayaran</h4>
                <ol>
                    <li>Pembayaran dilakukan sesuai dengan metode pembayaran yang tersedia pada sistem.</li>
                    <li>Seluruh biaya yang tercantum pada saat pemesanan merupakan biaya yang harus dibayarkan oleh
                        pengguna.</li>
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
                    <li>Pengelola tidak bertanggung jawab atas kerugian yang timbul akibat kesalahan pengguna dalam
                        menggunakan layanan.</li>
                    <li>Pengelola berhak melakukan perubahan, pembaruan, atau penghentian layanan sewaktu-waktu apabila
                        diperlukan.</li>
                </ol>

                <h4>8. Ulasan dan Testimoni</h4>
                <ol>
                    <li>Pengguna dapat memberikan ulasan dan testimoni setelah menggunakan layanan.</li>
                    <li>Pengguna dilarang mengunggah konten yang mengandung unsur SARA, ujaran kebencian, pornografi,
                        atau informasi yang tidak benar.</li>
                    <li>Pengelola berhak menghapus ulasan yang melanggar ketentuan.</li>
                </ol>

                <h4>9. Privasi Data</h4>
                <ol>
                    <li>Data pengguna akan digunakan untuk keperluan layanan dan pengelolaan transaksi.</li>
                    <li>Pengelola berkomitmen menjaga kerahasiaan data pengguna sesuai dengan kebijakan privasi yang
                        berlaku.</li>
                    <li>Data pengguna tidak akan disebarluaskan kepada pihak lain tanpa persetujuan, kecuali diwajibkan
                        oleh hukum.</li>
                </ol>

                <h4>10. Perubahan Ketentuan</h4>
                <p>Pengelola berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan berlaku setelah
                    dipublikasikan pada website.</p>

                <h4>11. Persetujuan</h4>
                <p>Dengan menggunakan website ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh
                    syarat dan ketentuan yang berlaku.</p>
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
                <p>Website MdRentalCar berkomitmen untuk melindungi privasi dan keamanan data pribadi pengguna.
                    Kebijakan Privasi ini menjelaskan bagaimana data pengguna dikumpulkan, digunakan, disimpan, dan
                    dilindungi.</p>

                <h4>1. Informasi yang Kami Kumpulkan</h4>
                <ul>
                    <li><strong>Data Pribadi Pengguna</strong>: Nama lengkap, username, alamat email, dan kata sandi
                        saat mendaftar.</li>
                    <li><strong>Data Verifikasi Identitas (e-KYC)</strong>: Foto Kartu Tanda Penduduk (KTP) serta foto
                        selfie pemohon guna keperluan validasi identitas fisik sebelum pemesanan disetujui.</li>
                    <li><strong>Data Sewa &amp; Transaksi</strong>: Detail mobil yang dipilih, durasi sewa, status
                        pembayaran, serta riwayat pembayaran.</li>
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
                    <li>Berkas verifikasi identitas (KTP dan selfie) disimpan dalam sistem database yang aman dan
                        terenkripsi. Berkas identitas ini akan dihapus secara berkala dari penyimpanan lokal setelah
                        periode sewa selesai guna mencegah penyalahgunaan data pribadi.</li>
                </ul>

                <h4>4. Pembagian Informasi dengan Pihak Ketiga</h4>
                <ul>
                    <li>Kami membagikan data transaksi secara aman kepada mitra payment gateway (seperti Midtrans) guna
                        pemrosesan dan verifikasi pembayaran.</li>
                    <li>Kami berkomitmen tidak akan menjual, menyewakan, atau menyebarkan informasi pribadi Anda kepada
                        pihak ketiga mana pun tanpa persetujuan, kecuali diwajibkan oleh peraturan hukum yang berlaku.
                    </li>
                </ul>

                <h4>5. Hak Pengguna</h4>
                <ul>
                    <li>Pengguna berhak memeriksa, mengoreksi, atau memperbarui informasi profil akun pribadi mereka.
                    </li>
                    <li>Pengguna berhak mengajukan permohonan penonaktifan akun serta penghapusan data pribadi apabila
                        memutuskan untuk tidak lagi menggunakan layanan kami.</li>
                </ul>

                <h4>6. Perubahan Kebijakan</h4>
                <p>Kami berhak melakukan perubahan pada Kebijakan Privasi ini sewaktu-waktu. Perubahan kebijakan akan
                    mulai berlaku segera setelah dipublikasikan pada halaman ini.</p>
                <h4>7. Cookie dan Teknologi Serupa</h4>
                <p>Website dapat menggunakan cookie untuk meningkatkan pengalaman pengguna, menyimpan preferensi, dan
                    membantu analisis penggunaan layanan.</p>
                <h4>8. Persetujuan</h4>
                <p>Dengan menggunakan website ini, pengguna dianggap telah membaca, memahami, dan menyetujui Kebijakan
                    Privasi yang berlaku.</p>
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