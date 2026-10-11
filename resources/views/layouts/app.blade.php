<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a2e">
    <title>@yield('title', 'Sistem Rekomendasi Suplemen Gym')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* ── Reset & Base ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        /* ── Topbar ────────────────────────────────────────────────── */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e8eaed;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            z-index: 1040;
            gap: .75rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .topbar-brand {
            font-weight: 700;
            font-size: 1rem;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: .5rem;
            flex: 1;
            min-width: 0;
        }
        .topbar-brand .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .8rem;
            flex-shrink: 0;
        }
        .topbar-brand .brand-text { min-width: 0; }
        .topbar-brand .brand-name {
            font-size: .9rem; font-weight: 700;
            color: #1a1a2e; line-height: 1.1;
        }
        .topbar-title {
            font-size: .72rem; color: #94a3b8;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* Hamburger */
        .btn-hamburger {
            width: 40px; height: 40px;
            border: none; background: #f1f5f9;
            border-radius: 10px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 5px; cursor: pointer; flex-shrink: 0;
            transition: background .15s; padding: 0;
        }
        .btn-hamburger:active { background: #e2e8f0; }
        .btn-hamburger .bar {
            width: 18px; height: 2px;
            background: #374151; border-radius: 2px;
            transition: all .28s cubic-bezier(.4,0,.2,1);
            transform-origin: center; display: block;
        }
        .btn-hamburger.open .bar:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .btn-hamburger.open .bar:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .btn-hamburger.open .bar:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* User avatar */
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; flex-shrink: 0;
            cursor: pointer;
        }

        /* Desktop topbar */
        @media (min-width: 992px) {
            .topbar { left: 270px; }
            .btn-hamburger { display: none !important; }
            .topbar-brand .brand-icon,
            .topbar-brand .brand-name { display: none !important; }
        }

        /* ── Overlay ───────────────────────────────────────────────── */
        .sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1045; opacity: 0;
            pointer-events: none; transition: opacity .3s;
        }
        .sidebar-overlay.show { opacity: 1; pointer-events: all; }
        @media (min-width: 992px) { .sidebar-overlay { display: none !important; } }

        /* ── Sidebar ───────────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 270px; height: 100dvh;
            background: #1a1a2e;
            z-index: 1050;
            display: flex; flex-direction: column;
            overflow-y: auto; overflow-x: hidden;
            transform: translateX(-100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            -webkit-overflow-scrolling: touch;
        }
        .sidebar.open { transform: translateX(0); }
        @media (min-width: 992px) { .sidebar { transform: translateX(0) !important; } }

        /* Sidebar header */
        .sidebar-header {
            padding: 1rem 1rem .9rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center;
            justify-content: space-between; flex-shrink: 0;
        }
        .sidebar-logo { display: flex; align-items: center; gap: .65rem; }
        .sidebar-logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .9rem; flex-shrink: 0;
        }
        .sidebar-logo .logo-text { color: #fff; font-weight: 700; font-size: .95rem; line-height: 1.2; }
        .sidebar-logo .logo-sub  { color: #6b7280; font-size: .68rem; display: block; }

        .btn-sidebar-close {
            width: 32px; height: 32px; border: none;
            background: rgba(255,255,255,.07); border-radius: 8px;
            color: #9ca3af;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background .2s; flex-shrink: 0;
        }
        .btn-sidebar-close:hover { background: rgba(255,255,255,.14); color: #fff; }
        @media (min-width: 992px) { .btn-sidebar-close { display: none; } }

        /* Nav section label */
        .nav-section {
            font-size: .63rem; text-transform: uppercase;
            letter-spacing: .1em; color: #4b5563;
            padding: 1rem 1rem .35rem; font-weight: 600;
        }

        /* Nav links */
        .sidebar .nav-link {
            color: #9ca3af; padding: .65rem .85rem;
            border-radius: 10px; margin: 1px .5rem;
            display: flex; align-items: center; gap: .65rem;
            font-size: .875rem; font-weight: 500;
            transition: all .18s; position: relative; text-decoration: none;
        }
        .sidebar .nav-link .nav-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; background: rgba(255,255,255,.05);
            flex-shrink: 0; transition: all .18s;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #e5e7eb; }
        .sidebar .nav-link:hover .nav-icon { background: rgba(255,255,255,.1); }
        .sidebar .nav-link.active { background: rgba(124,58,237,.2); color: #c4b5fd; }
        .sidebar .nav-link.active .nav-icon { background: rgba(124,58,237,.35); color: #c4b5fd; }
        .sidebar .nav-link.active::before {
            content: ''; position: absolute;
            left: -.5rem; top: 50%; transform: translateY(-50%);
            width: 3px; height: 55%;
            background: #7c3aed; border-radius: 0 3px 3px 0;
        }

        /* Nav link danger (logout) */
        .sidebar .nav-link.nav-danger { color: #f87171; }
        .sidebar .nav-link.nav-danger .nav-icon { background: rgba(248,113,113,.1); }
        .sidebar .nav-link.nav-danger:hover { background: rgba(248,113,113,.12); color: #fca5a5; }
        .sidebar .nav-link.nav-danger:hover .nav-icon { background: rgba(248,113,113,.2); }

        /* Divider */
        .sidebar-divider {
            height: 1px; background: rgba(255,255,255,.07);
            margin: .5rem 1rem;
        }

        /* ── Sidebar Footer ────────────────────────────────────────── */
        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }

        /* User info card di footer */
        .sidebar-user-card {
            display: flex; align-items: center; gap: .65rem;
            padding: .85rem 1rem;
            background: rgba(255,255,255,.04);
        }
        .sidebar-user-card .user-info { min-width: 0; flex: 1; }
        .sidebar-user-card .user-name {
            color: #e5e7eb; font-size: .84rem;
            font-weight: 600; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-card .user-role {
            font-size: .68rem; font-weight: 500;
            display: inline-flex; align-items: center; gap: .3rem;
            margin-top: .1rem;
        }
        .sidebar-user-card .user-role.role-admin { color: #a78bfa; }
        .sidebar-user-card .user-role.role-user  { color: #6b7280; }

        /* Footer nav links */
        .sidebar-footer-nav { padding: .4rem .5rem .6rem; }
        .sidebar-footer-nav .nav-link { padding: .5rem .85rem; margin: 1px 0; font-size: .82rem; }
        .sidebar-footer-nav .nav-link .nav-icon { width: 28px; height: 28px; font-size: .75rem; }

        /* ── Main wrapper ──────────────────────────────────────────── */
        .main-wrapper { padding-top: 56px; min-height: 100vh; transition: margin-left .3s; }
        @media (min-width: 992px) { .main-wrapper { margin-left: 270px; } }

        .main-content { padding: 1.1rem 1rem; }
        @media (min-width: 576px) { .main-content { padding: 1.25rem 1.25rem; } }
        @media (min-width: 768px) { .main-content { padding: 1.5rem 1.75rem; } }
        @media (min-width: 992px) { .main-content { padding: 1.75rem 2rem; } }

        /* ── Cards ─────────────────────────────────────────────────── */
        .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .card-header {
            background: #fff; border-bottom: 1px solid #f0f0f0;
            border-radius: 14px 14px 0 0 !important; font-weight: 600;
        }

        /* ── Utilities ─────────────────────────────────────────────── */
        .badge-fuzzy { font-size: .75rem; padding: .35em .65em; border-radius: 20px; }
        .skor-bar { height: 8px; border-radius: 4px; background: #e2e8f0; }
        .skor-bar-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#4f46e5,#7c3aed); }
        .rank-badge {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: 700; font-size: .9rem;
        }
        .rank-1 { background: #fef3c7; color: #d97706; }
        .rank-2 { background: #e2e8f0; color: #475569; }
        .rank-3 { background: #fce7f3; color: #db2777; }
        .rank-other { background: #f1f5f9; color: #64748b; }

        /* Safe area */
        @supports (padding: env(safe-area-inset-bottom)) {
            .sidebar { padding-bottom: env(safe-area-inset-bottom); }
            .topbar  { padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── OVERLAY ──────────────────────────────────────────────────────────── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── SIDEBAR ───────────────────────────────────────────────────────────── --}}
<aside class="sidebar" id="sidebar" role="navigation" aria-label="Navigasi utama">

    {{-- Logo --}}
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-dumbbell"></i></div>
            <div>
                <div class="logo-text">SuplemenGym</div>
                <span class="logo-sub">Sistem Rekomendasi Fuzzy</span>
            </div>
        </div>
        <button class="btn-sidebar-close" id="btnSidebarClose" aria-label="Tutup menu">
            <i class="fas fa-xmark fa-sm"></i>
        </button>
    </div>

    {{-- ── MENU UTAMA (semua role) ── --}}
    <span class="nav-section">Menu Utama</span>
    <nav class="nav flex-column px-1">
        <a href="{{ route('rekomendasi.create') }}"
           class="nav-link {{ request()->routeIs('rekomendasi.create') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-magnifying-glass"></i></span>
            Cari Rekomendasi
        </a>
        <a href="{{ route('rekomendasi.index') }}"
           class="nav-link {{ request()->routeIs('rekomendasi.index') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-trophy"></i></span>
            Hasil Rekomendasi
        </a>
    </nav>

    {{-- ── MENU ADMIN (hanya role admin) ── --}}
    @if(Auth::check() && Auth::user()->role === 'admin')
        <span class="nav-section">Admin</span>
        <nav class="nav flex-column px-1">
            <a href="{{ route('kriteria.index') }}"
               class="nav-link {{ request()->routeIs('kriteria.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-list-check"></i></span>
                Kriteria
            </a>
            <a href="{{ route('subkriteria.index') }}"
               class="nav-link {{ request()->routeIs('subkriteria.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-diagram-project"></i></span>
                Sub Kriteria
            </a>
            <a href="{{ route('alternatif.index') }}"
               class="nav-link {{ request()->routeIs('alternatif.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-box"></i></span>
                Produk Suplemen
            </a>
            <a href="{{ route('fuzzy-rule.index') }}"
               class="nav-link {{ request()->routeIs('fuzzy-rule.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-code-branch"></i></span>
                Fuzzy Rules
            </a>
        </nav>
    @endif

    {{-- ── SIDEBAR FOOTER ── --}}
    <div class="sidebar-footer">

        {{-- Info user --}}
        <div class="sidebar-user-card">
            <div class="user-avatar" style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;cursor:default;">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <div class="user-role role-admin">
                        <i class="fas fa-shield-halved" style="font-size:.6rem"></i> Administrator
                    </div>
                @else
                    <div class="user-role role-user">
                        <i class="fas fa-user" style="font-size:.6rem"></i> Member
                    </div>
                @endif
            </div>
        </div>

        {{-- Edit profil & Logout --}}
        <div class="sidebar-footer-nav">
            <a href="{{ route('profil') }}"
               class="nav-link {{ request()->routeIs('profil') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-user-pen"></i></span>
                Edit Profil
            </a>

            <div class="sidebar-divider"></div>

            {{-- Logout via form (POST) --}}
            <form method="POST" action="{{ route('logout') }}" id="formLogout">
                @csrf
                <a href="#" class="nav-link nav-danger"
                   onclick="event.preventDefault(); confirmLogout();">
                    <span class="nav-icon"><i class="fas fa-right-from-bracket"></i></span>
                    Keluar
                </a>
            </form>
        </div>

    </div>

</aside>

{{-- ── TOPBAR ────────────────────────────────────────────────────────────── --}}
<header class="topbar" role="banner">

    <button class="btn-hamburger" id="btnHamburger"
            aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>

    <div class="topbar-brand">
        <div class="brand-icon d-lg-none">
            <i class="fas fa-dumbbell"></i>
        </div>
        <div class="brand-text">
            <div class="brand-name d-lg-none">SuplemenGym</div>
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
        </div>
    </div>

    {{-- Mobile: avatar + badge role --}}
    <div class="d-flex d-lg-none align-items-center gap-2">
        @if(Auth::check() && Auth::user()->role === 'admin')
            <span class="badge" style="background:rgba(124,58,237,.15);color:#7c3aed;font-size:.65rem;">Admin</span>
        @endif
        <div class="user-avatar" title="{{ Auth::user()->name ?? 'User' }}">
            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
        </div>
    </div>

    {{-- Desktop: nama + role badge + avatar --}}
    <div class="d-none d-lg-flex align-items-center gap-2 ms-auto">
        @if(Auth::check() && Auth::user()->role === 'admin')
            <span class="badge" style="background:rgba(124,58,237,.12);color:#7c3aed;font-size:.7rem;padding:.3em .7em;border-radius:20px;">
                <i class="fas fa-shield-halved me-1" style="font-size:.6rem"></i>Admin
            </span>
        @endif
        <span class="text-muted small">{{ Auth::user()->name ?? 'User' }}</span>
        <div class="user-avatar" title="Edit Profil"
             onclick="window.location='{{ route('profil') }}'">
            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
        </div>
    </div>

</header>

{{-- ── MAIN ─────────────────────────────────────────────────────────────── --}}
<main class="main-wrapper" id="mainWrapper">
    <div class="main-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show rounded-3 small mb-3" role="alert">
                <i class="fas fa-triangle-exclamation me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3" role="alert">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const btnOpen  = document.getElementById('btnHamburger');
    const btnClose = document.getElementById('btnSidebarClose');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        btnOpen.classList.add('open');
        btnOpen.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        btnOpen.classList.remove('open');
        btnOpen.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    btnOpen.addEventListener('click', () =>
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
    );
    btnClose.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('.nav-link').forEach(a =>
        a.addEventListener('click', () => { if (window.innerWidth < 992) closeSidebar(); })
    );
    window.addEventListener('resize', () => { if (window.innerWidth >= 992) closeSidebar(); });

    // Swipe gesture
    let swipeStartX = 0, swipeStartY = 0;
    document.addEventListener('touchstart', e => {
        swipeStartX = e.touches[0].clientX;
        swipeStartY = e.touches[0].clientY;
    }, { passive: true });
    document.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - swipeStartX;
        const dy = Math.abs(e.changedTouches[0].clientY - swipeStartY);
        if (swipeStartX < 20 && dx > 60 && dy < 60) openSidebar();
    }, { passive: true });
    sidebar.addEventListener('touchstart', e => {
        swipeStartX = e.touches[0].clientX;
        swipeStartY = e.touches[0].clientY;
    }, { passive: true });
    sidebar.addEventListener('touchend', e => {
        const dx = swipeStartX - e.changedTouches[0].clientX;
        const dy = Math.abs(e.changedTouches[0].clientY - swipeStartY);
        if (dx > 60 && dy < 60) closeSidebar();
    }, { passive: true });

    // CSRF AJAX
    if (typeof $ !== 'undefined') {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
    }
})();

// ── Konfirmasi Logout ─────────────────────────────────────────────────────────
function confirmLogout() {
    Swal.fire({
        title: 'Keluar dari sistem?',
        text: 'Anda akan diarahkan ke halaman login.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-right-from-bracket me-1"></i> Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formLogout').submit();
        }
    });
}

// SweetAlert dari session
@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session("success") }}', timer: 2500, showConfirmButton: false });
@endif
</script>

@stack('scripts')
</body>
</html>