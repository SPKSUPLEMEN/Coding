<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a1a2e">
    <title>Daftar — SuplemenGym</title>

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

        /* ── Panel Kiri ───────────────────────────────────────────── */
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
        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(124,58,237,.15) 0%, transparent 70%);
            top: -100px; right: -100px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px; border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,.12) 0%, transparent 70%);
            bottom: -50px; left: -50px;
        }

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
            color: #fff; font-size: 1.8rem; font-weight: 800;
            text-align: center; line-height: 1.2;
            margin-bottom: .75rem; position: relative; z-index: 1;
        }
        .left-panel h1 span {
            background: linear-gradient(90deg, #a78bfa, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .left-panel p {
            color: #6b7280; text-align: center;
            font-size: .9rem; line-height: 1.6;
            max-width: 300px; position: relative; z-index: 1;
            margin-bottom: 2.5rem;
        }

        /* Step indicator */
        .step-list {
            display: flex; flex-direction: column; gap: 1rem;
            width: 100%; max-width: 300px;
            position: relative; z-index: 1;
        }
        .step-item {
            display: flex; align-items: flex-start; gap: .85rem;
        }
        .step-num {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff; font-size: .75rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: .1rem;
        }
        .step-text strong { color: #e5e7eb; font-size: .82rem; display: block; }
        .step-text span   { color: #6b7280; font-size: .74rem; }

        .left-footer {
            position: absolute; bottom: 1.5rem;
            color: #374151; font-size: .72rem;
            text-align: center; z-index: 1;
        }

        /* ── Panel Kanan — Form ───────────────────────────────────── */
        .right-panel {
            width: 520px; flex-shrink: 0;
            background: #fff;
            display: flex; flex-direction: column; justify-content: center;
            padding: 3rem 2.5rem;
            overflow-y: auto;
        }

        .form-header { margin-bottom: 1.75rem; }
        .form-header .greeting {
            font-size: .8rem; color: #7c3aed; font-weight: 600;
            text-transform: uppercase; letter-spacing: .08em; margin-bottom: .4rem;
        }
        .form-header h2 { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin-bottom: .4rem; }
        .form-header p  { font-size: .85rem; color: #94a3b8; }

        /* Form label */
        .form-label {
            font-size: .82rem; font-weight: 600;
            color: #374151; margin-bottom: .4rem; display: block;
        }

        /* Input custom */
        .input-group-custom { position: relative; margin-bottom: 1.1rem; }
        .input-icon {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: .85rem;
            pointer-events: none; z-index: 2;
        }
        .form-control-custom {
            width: 100%;
            padding: .75rem 1rem .75rem 2.75rem;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: .9rem; color: #1e293b;
            background: #f8fafc; outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .form-control-custom:focus {
            border-color: #7c3aed; background: #fff;
            box-shadow: 0 0 0 3px rgba(124,58,237,.1);
        }
        .form-control-custom::placeholder { color: #cbd5e1; }
        .form-control-custom.is-invalid   { border-color: #ef4444; background: #fff5f5; }

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

        /* Error message per field */
        .field-error {
            font-size: .75rem; color: #ef4444;
            margin-top: -.7rem; margin-bottom: .75rem;
            display: flex; align-items: center; gap: .3rem;
        }

        /* Password strength bar */
        .strength-wrap { margin-top: -.6rem; margin-bottom: .9rem; }
        .strength-bar {
            height: 4px; border-radius: 2px;
            background: #e2e8f0; overflow: hidden;
        }
        .strength-bar-fill {
            height: 100%; border-radius: 2px;
            transition: width .3s, background .3s;
            width: 0%;
        }
        .strength-label {
            font-size: .72rem; color: #94a3b8;
            margin-top: .25rem; text-align: right;
        }

        /* Submit button */
        .btn-register {
            width: 100%; padding: .85rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none; border-radius: 10px;
            color: #fff; font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: opacity .2s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            margin-bottom: 1.25rem;
        }
        .btn-register:hover   { opacity: .92; }
        .btn-register:active  { transform: scale(.98); }
        .btn-register:disabled { opacity: .6; cursor: not-allowed; }

        /* Terms note */
        .terms-note {
            font-size: .75rem; color: #94a3b8;
            text-align: center; margin-bottom: 1.25rem; line-height: 1.5;
        }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 1.25rem; color: #cbd5e1; font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #e2e8f0;
        }

        /* Login link */
        .login-link { text-align: center; font-size: .83rem; color: #64748b; }
        .login-link a { color: #7c3aed; font-weight: 600; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }

        .form-footer { margin-top: 1.5rem; text-align: center; color: #cbd5e1; font-size: .72rem; }

        /* ── Responsive Mobile ────────────────────────────────────── */
        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { padding: 2.5rem 1.5rem 2rem; min-height: auto; }
            .left-panel h1 { font-size: 1.4rem; }
            .hero-icon { width: 70px; height: 70px; font-size: 1.8rem; margin-bottom: 1.25rem; }
            .step-list { display: none; }
            .left-panel p { margin-bottom: 0; }
            .left-footer { display: none; }
            .right-panel {
                width: 100%; padding: 2rem 1.5rem;
                border-radius: 20px 20px 0 0;
                margin-top: -20px; position: relative; z-index: 2;
            }
        }
        @media (max-width: 400px) { .right-panel { padding: 1.75rem 1.25rem; } }
    </style>
</head>
<body>

    {{-- ── Panel Kiri ──────────────────────────────────────────────── --}}
    <div class="left-panel">
        <div class="hero-icon">
            <i class="fas fa-dumbbell"></i>
        </div>

        <h1>Bergabung dengan<br><span>SuplemenGym</span></h1>

        <p>Buat akun gratis dan dapatkan rekomendasi suplemen gym terbaik yang dipersonalisasi untuk Anda.</p>

        <div class="step-list">
            <div class="step-item">
                <div class="step-num">1</div>
                <div class="step-text">
                    <strong>Buat Akun</strong>
                    <span>Daftar dengan email yang valid</span>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <div class="step-text">
                    <strong>Atur Preferensi</strong>
                    <span>Geser slider sesuai kebutuhan Anda</span>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <div class="step-text">
                    <strong>Dapatkan Rekomendasi</strong>
                    <span>Sistem Fuzzy Mamdani bekerja untuk Anda</span>
                </div>
            </div>
        </div>

        <div class="left-footer">
            SPK Fuzzy Rekomendasi Suplemen &copy; {{ now()->year }}
        </div>
    </div>

    {{-- ── Panel Kanan — Form Register ─────────────────────────────── --}}
    <div class="right-panel">

        <div class="form-header">
            <div class="greeting">Buat Akun Baru</div>
            <h2>Daftar Sekarang</h2>
            <p>Isi data berikut untuk membuat akun Anda</p>
        </div>

        <form method="POST" action="{{ route('register') }}" id="formRegister">
            @csrf

            {{-- Nama --}}
            <label class="form-label" for="name">Nama Lengkap</label>
            <div class="input-group-custom">
                <i class="fas fa-user input-icon"></i>
                <input type="text"
                       id="name" name="name"
                       class="form-control-custom {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name') }}"
                       placeholder="Masukkan nama lengkap"
                       autocomplete="name" autofocus required>
            </div>
            @if($errors->has('name'))
                <div class="field-error">
                    <i class="fas fa-circle-exclamation"></i> {{ $errors->first('name') }}
                </div>
            @endif

            {{-- Email --}}
            <label class="form-label" for="email">Alamat Email</label>
            <div class="input-group-custom">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email"
                       id="email" name="email"
                       class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="contoh@email.com"
                       autocomplete="email" required>
            </div>
            @if($errors->has('email'))
                <div class="field-error">
                    <i class="fas fa-circle-exclamation"></i> {{ $errors->first('email') }}
                </div>
            @endif

            {{-- Password --}}
            <label class="form-label" for="password">Kata Sandi</label>
            <div class="input-group-custom">
                <i class="fas fa-lock input-icon"></i>
                <input type="password"
                       id="password" name="password"
                       class="form-control-custom {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Minimal 8 karakter"
                       autocomplete="new-password"
                       oninput="checkStrength(this.value)"
                       required>
                <button type="button" class="btn-toggle-pass" id="btnTogglePass1" tabindex="-1">
                    <i class="fas fa-eye" id="iconPass1"></i>
                </button>
            </div>
            {{-- Strength bar --}}
            <div class="strength-wrap">
                <div class="strength-bar">
                    <div class="strength-bar-fill" id="strengthFill"></div>
                </div>
                <div class="strength-label" id="strengthLabel">Kekuatan kata sandi</div>
            </div>
            @if($errors->has('password'))
                <div class="field-error">
                    <i class="fas fa-circle-exclamation"></i> {{ $errors->first('password') }}
                </div>
            @endif

            {{-- Konfirmasi Password --}}
            <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
            <div class="input-group-custom">
                <i class="fas fa-lock input-icon"></i>
                <input type="password"
                       id="password_confirmation" name="password_confirmation"
                       class="form-control-custom {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                       placeholder="Ulangi kata sandi"
                       autocomplete="new-password" required>
                <button type="button" class="btn-toggle-pass" id="btnTogglePass2" tabindex="-1">
                    <i class="fas fa-eye" id="iconPass2"></i>
                </button>
            </div>
            @if($errors->has('password_confirmation'))
                <div class="field-error">
                    <i class="fas fa-circle-exclamation"></i> {{ $errors->first('password_confirmation') }}
                </div>
            @endif

            {{-- Terms note --}}
            <div class="terms-note">
                Dengan mendaftar, Anda menyetujui penggunaan data untuk keperluan sistem rekomendasi.
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-register" id="btnRegister">
                <i class="fas fa-user-plus"></i>
                Buat Akun
            </button>

        </form>

        <div class="divider">atau</div>

        <div class="login-link">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk di sini</a>
        </div>

        <div class="form-footer">
            SPK Fuzzy Rekomendasi Suplemen &copy; {{ now()->year }}
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Toggle show/hide password ─────────────────────────────────────────
        function togglePass(btnId, iconId, inputId) {
            document.getElementById(btnId).addEventListener('click', function () {
                const input = document.getElementById(inputId);
                const icon  = document.getElementById(iconId);
                const isPass = input.type === 'password';
                input.type   = isPass ? 'text' : 'password';
                icon.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        }
        togglePass('btnTogglePass1', 'iconPass1', 'password');
        togglePass('btnTogglePass2', 'iconPass2', 'password_confirmation');

        // ── Password strength checker ─────────────────────────────────────────
        function checkStrength(val) {
            const fill  = document.getElementById('strengthFill');
            const label = document.getElementById('strengthLabel');

            let score = 0;
            if (val.length >= 8)  score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { pct: '0%',   color: '#e2e8f0', text: 'Kekuatan kata sandi' },
                { pct: '25%',  color: '#ef4444', text: 'Lemah' },
                { pct: '50%',  color: '#f59e0b', text: 'Cukup' },
                { pct: '75%',  color: '#3b82f6', text: 'Kuat' },
                { pct: '100%', color: '#10b981', text: 'Sangat Kuat' },
            ];

            const lv = val.length === 0 ? levels[0] : levels[score] || levels[1];
            fill.style.width      = lv.pct;
            fill.style.background = lv.color;
            label.textContent     = lv.text;
            label.style.color     = val.length === 0 ? '#94a3b8' : lv.color;
        }

        // ── Loading state saat submit ─────────────────────────────────────────
        document.getElementById('formRegister').addEventListener('submit', function () {
            const btn = document.getElementById('btnRegister');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Membuat Akun...';
        });
    </script>
</body>
</html>