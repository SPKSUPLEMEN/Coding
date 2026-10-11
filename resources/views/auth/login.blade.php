<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a1a2e">
    <title>Masuk — GymSuplemen</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f0f1a;
            display: flex;
            overflow: hidden;
        }

        /* ── Panel Kiri — Ilustrasi ───────────────────────────────── */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Lingkaran dekoratif di background */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124,58,237,.15) 0%, transparent 70%);
            top: -100px; right: -100px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,.12) 0%, transparent 70%);
            bottom: -50px; left: -50px;
        }

        /* Ikon dumbbell besar */
        .hero-icon {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.4rem; color: #fff;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(124,58,237,.4);
            position: relative; z-index: 1;
        }

        .left-panel h1 {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            line-height: 1.2;
            margin-bottom: .75rem;
            position: relative; z-index: 1;
        }
        .left-panel h1 span {
            background: linear-gradient(90deg, #a78bfa, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .left-panel p {
            color: #6b7280;
            text-align: center;
            font-size: .9rem;
            line-height: 1.6;
            max-width: 300px;
            position: relative; z-index: 1;
            margin-bottom: 2.5rem;
        }

        /* Fitur cards kecil */
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            width: 100%;
            max-width: 320px;
            position: relative; z-index: 1;
        }
        .feature-item {
            display: flex; align-items: center; gap: .85rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 12px;
            padding: .75rem 1rem;
        }
        .feature-item .fi-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; flex-shrink: 0;
        }
        .feature-item .fi-text strong { color: #e5e7eb; font-size: .82rem; display: block; }
        .feature-item .fi-text span  { color: #6b7280; font-size: .74rem; }

        /* Footer kiri */
        .left-footer {
            position: absolute; bottom: 1.5rem;
            color: #374151; font-size: .72rem;
            text-align: center; z-index: 1;
        }

        /* ── Panel Kanan — Form ───────────────────────────────────── */
        .right-panel {
            width: 480px;
            flex-shrink: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            overflow-y: auto;
        }

        .form-header { margin-bottom: 2rem; }
        .form-header .greeting {
            font-size: .8rem; color: #7c3aed;
            font-weight: 600; text-transform: uppercase;
            letter-spacing: .08em; margin-bottom: .4rem;
        }
        .form-header h2 {
            font-size: 1.6rem; font-weight: 800;
            color: #1e293b; margin-bottom: .4rem;
        }
        .form-header p { font-size: .85rem; color: #94a3b8; }

        /* Form fields */
        .form-label {
            font-size: .82rem; font-weight: 600;
            color: #374151; margin-bottom: .4rem;
        }

        .input-group-custom {
            position: relative; margin-bottom: 1.25rem;
        }
        .input-icon {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: .85rem;
            pointer-events: none; z-index: 2;
        }
        .form-control-custom {
            width: 100%;
            padding: .75rem 1rem .75rem 2.75rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: .9rem;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .form-control-custom:focus {
            border-color: #7c3aed;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(124,58,237,.1);
        }
        .form-control-custom::placeholder { color: #cbd5e1; }
        .form-control-custom.is-invalid { border-color: #ef4444; }

        /* Toggle password */
        .btn-toggle-pass {
            position: absolute; right: .85rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8; cursor: pointer;
            padding: .25rem; font-size: .85rem;
            transition: color .15s; z-index: 2;
        }
        .btn-toggle-pass:hover { color: #7c3aed; }

        /* Error */
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: .65rem;
            font-size: .82rem; color: #dc2626;
        }

        /* Remember me */
        .remember-row {
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row input[type=checkbox] {
            width: 16px; height: 16px;
            accent-color: #7c3aed; cursor: pointer;
        }
        .remember-row label { font-size: .82rem; color: #64748b; cursor: pointer; }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none; border-radius: 10px;
            color: #fff; font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: opacity .2s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .btn-login:hover  { opacity: .92; }
        .btn-login:active { transform: scale(.98); }
        .btn-login:disabled { opacity: .6; cursor: not-allowed; }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 1.25rem; color: #cbd5e1; font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #e2e8f0;
        }

        /* Register link */
        .register-link {
            text-align: center; font-size: .83rem; color: #64748b;
        }
        .register-link a {
            color: #7c3aed; font-weight: 600; text-decoration: none;
        }
        .register-link a:hover { text-decoration: underline; }

        /* Form footer */
        .form-footer {
            margin-top: 2rem; text-align: center;
            color: #cbd5e1; font-size: .72rem;
        }

        /* ── Responsive — Mobile ──────────────────────────────────── */
        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }

            .left-panel {
                padding: 2.5rem 1.5rem 2rem;
                min-height: auto;
            }
            .left-panel h1  { font-size: 1.4rem; }
            .hero-icon      { width: 70px; height: 70px; font-size: 1.8rem; margin-bottom: 1.25rem; }
            .feature-list   { display: none; }
            .left-panel p   { margin-bottom: 0; }
            .left-footer    { display: none; }

            .right-panel {
                width: 100%;
                padding: 2rem 1.5rem;
                border-radius: 20px 20px 0 0;
                margin-top: -20px;
                position: relative;
                z-index: 2;
            }
        }

        @media (max-width: 400px) {
            .right-panel { padding: 1.75rem 1.25rem; }
        }
    </style>
</head>
<body>

    {{-- ── Panel Kiri ─────────────────────────────────────────────── --}}
    <div class="left-panel">
        <div class="hero-icon">
            <i class="fas fa-dumbbell"></i>
        </div>

        <h1>GymSuplemen<br><span>Rekomendasi Cerdas</span></h1>

        <p>Temukan suplemen gym yang paling sesuai dengan kebutuhan Anda menggunakan sistem kecerdasan berbasis Logika Fuzzy Mamdani.</p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="fi-icon" style="background:rgba(79,70,229,.2);">
                    <i class="fas fa-brain" style="color:#818cf8"></i>
                </div>
                <div class="fi-text">
                    <strong>Fuzzy Mamdani</strong>
                    <span>Algoritma rekomendasi cerdas</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="fi-icon" style="background:rgba(124,58,237,.2);">
                    <i class="fas fa-sliders" style="color:#a78bfa"></i>
                </div>
                <div class="fi-text">
                    <strong>Kriteria Penilaian</strong>
                    <span>Harga, Isi, Protein, Gizi</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="fi-icon" style="background:rgba(16,185,129,.15);">
                    <i class="fas fa-trophy" style="color:#34d399"></i>
                </div>
                <div class="fi-text">
                    <strong>Hasil Terperingkat</strong>
                    <span>Suplemen terbaik sesuai preferensi</span>
                </div>
            </div>
        </div>

        <div class="left-footer">
            SPK Fuzzy Rekomendasi Suplemen &copy; {{ now()->year }}
        </div>
    </div>

    {{-- ── Panel Kanan — Form Login ────────────────────────────────── --}}
    <div class="right-panel">

        <div class="form-header">
            <div class="greeting">Selamat Datang</div>
            <h2>Masuk ke Akun</h2>
            <p>Masukkan email dan kata sandi Anda untuk melanjutkan</p>
        </div>

        {{-- Error --}}
        @error('email')
        <div class="error-box">
            <i class="fas fa-circle-exclamation flex-shrink-0"></i>
            <span>Email atau kata sandi yang Anda masukkan salah.</span>
        </div>
        @enderror

        <form method="POST" action="{{ route('login') }}" id="formLogin">
            @csrf

            {{-- Email --}}
            <div class="mb-1">
                <label class="form-label" for="email">Alamat Email</label>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="contoh@email.com"
                       autocomplete="email"
                       autofocus
                       required>
            </div>

            {{-- Password --}}
            <div class="mb-1">
                <label class="form-label" for="password">Kata Sandi</label>
            </div>
            <div class="input-group-custom" style="margin-bottom:1rem;">
                <i class="fas fa-lock input-icon"></i>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       placeholder="Masukkan kata sandi"
                       autocomplete="current-password"
                       required>
                <button type="button" class="btn-toggle-pass" id="btnTogglePass" tabindex="-1">
                    <i class="fas fa-eye" id="iconTogglePass"></i>
                </button>
            </div>

            {{-- Remember me --}}
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya di perangkat ini</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login" id="btnLogin">
                <i class="fas fa-right-to-bracket"></i>
                Masuk
            </button>

        </form>

        <div class="divider">atau</div>

        <div class="register-link">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>

        <div class="form-footer">
            SPK Fuzzy Rekomendasi Suplemen &copy; {{ now()->year }}
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle show/hide password
        const passInput      = document.getElementById('password');
        const btnToggle      = document.getElementById('btnTogglePass');
        const iconToggle     = document.getElementById('iconTogglePass');

        btnToggle.addEventListener('click', function () {
            const isPass = passInput.type === 'password';
            passInput.type      = isPass ? 'text' : 'password';
            iconToggle.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
        });

        // Loading state saat submit
        document.getElementById('formLogin').addEventListener('submit', function () {
            const btn = document.getElementById('btnLogin');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
        });
    </script>
</body>
</html>