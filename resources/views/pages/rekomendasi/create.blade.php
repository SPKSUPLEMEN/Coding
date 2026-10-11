@extends('layouts.app')
@section('title', 'Cari Rekomendasi Suplemen')

@push('styles')
<style>
    /* ── Kriteria Card ──────────────────────────────────────────── */
    .kriteria-card {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .kriteria-card:hover { border-color: #7c3aed; box-shadow: 0 4px 16px rgba(124,58,237,.08); }
    .kriteria-card:focus-within { border-color: #7c3aed; }

    .kriteria-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: .9rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .kriteria-num {
        width: 22px; height: 22px;
        background: #7c3aed;
        color: #fff;
        border-radius: 50%;
        font-size: .7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ── Slider ──────────────────────────────────────────────────── */
    .slider-wrap { position: relative; }

    input[type=range] {
        -webkit-appearance: none;
        width: 100%; height: 6px;
        border-radius: 3px;
        background: #e2e8f0;
        outline: none;
        cursor: pointer;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #7c3aed;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(124,58,237,.4);
        transition: transform .15s;
    }
    input[type=range]::-webkit-slider-thumb:active { transform: scale(1.2); }
    input[type=range]::-moz-range-thumb {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #7c3aed;
        border: none;
        cursor: pointer;
    }

    /* Track fill warna */
    input[type=range] { background: linear-gradient(to right, #7c3aed 0%, #e2e8f0 0%); }

    .slider-labels {
        display: flex;
        justify-content: space-between;
        margin-top: .3rem;
        padding: 0 2px;
    }
    .slider-labels span { font-size: .7rem; color: #94a3b8; }

    /* Nilai display besar */
    .nilai-display {
        font-size: 1.8rem;
        font-weight: 800;
        color: #7c3aed;
        min-width: 52px;
        text-align: center;
        line-height: 1;
    }
    .nilai-display small { font-size: .65rem; font-weight: 400; color: #94a3b8; display: block; }

    /* ── Himpunan badges ─────────────────────────────────────────── */
    .himpunan-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .75rem; }
    .himpunan-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .28rem .75rem;
        border-radius: 20px;
        font-size: .78rem;
        border: 1.5px solid #e2e8f0;
        color: #94a3b8;
        background: #f8fafc;
        transition: all .15s;
    }
    .himpunan-badge .mu-val { font-weight: 700; font-size: .82rem; }
    .himpunan-badge.mu-active-0 { background:#dbeafe; border-color:#3b82f6; color:#1d4ed8; }
    .himpunan-badge.mu-active-1 { background:#fef9c3; border-color:#ca8a04; color:#854d0e; }
    .himpunan-badge.mu-active-2 { background:#dcfce7; border-color:#16a34a; color:#166534; }
    .himpunan-badge.mu-active-3 { background:#fce7f3; border-color:#db2777; color:#9d174d; }
    .himpunan-badge.mu-active-4 { background:#ede9fe; border-color:#7c3aed; color:#5b21b6; }

    /* ── Panel kanan ─────────────────────────────────────────────── */
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .5rem .75rem;
        border-radius: 8px;
        margin-bottom: .25rem;
        background: #f8fafc;
    }
    .summary-item .label { font-size: .8rem; color: #64748b; }
    .summary-item .val   { font-weight: 700; color: #7c3aed; font-size: .9rem; }

    /* ── Tombol submit ───────────────────────────────────────────── */
    .btn-cari {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border: none;
        color: #fff;
        padding: .75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: .95rem;
        width: 100%;
        transition: opacity .2s, transform .1s;
    }
    .btn-cari:hover    { opacity: .9; color: #fff; }
    .btn-cari:active   { transform: scale(.98); }
    .btn-cari:disabled { opacity: .6; cursor: not-allowed; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Cari Rekomendasi Suplemen</h4>
        <p class="text-muted mb-0 small">
            Geser slider sesuai preferensi Anda — sistem akan menghitung rekomendasi menggunakan metode Fuzzy Mamdani
        </p>
    </div>
    @if($rekomendasi->count() > 0)
        <a href="{{ route('rekomendasi.store') }}" class="btn btn-outline-primary btn-sm d-none d-md-inline-flex">
            <i class="fas fa-trophy me-1"></i> Lihat Hasil Terakhir
        </a>
    @endif
</div>

{{-- Tampilkan error validasi jika ada --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
        <i class="fas fa-circle-exclamation me-2"></i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tampilkan flash message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show small mb-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show small mb-4">
        <i class="fas fa-triangle-exclamation me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show small mb-4">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($kriteria->isEmpty())
    {{-- Belum ada data kriteria --}}
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-triangle-exclamation text-warning fs-1 mb-3 d-block"></i>
            <h5 class="text-muted">Belum Ada Kriteria</h5>
            <p class="small text-muted mb-3">
                Sistem belum memiliki data kriteria dan sub kriteria.<br>
                Silakan tambahkan melalui menu Admin terlebih dahulu.
            </p>
            <a href="{{ route('kriteria.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Kelola Kriteria
            </a>
        </div>
    </div>
@else

<form action="{{ route('rekomendasi.cari') }}" method="POST" id="formRekomendasi">
    @csrf

    <div class="row g-4">

        {{-- ── Kolom kiri: slider kriteria ─────────────────────────── --}}
        <div class="col-lg-8">

            @foreach($kriteria as $k)
            <div class="kriteria-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="kriteria-label">
                        <span class="kriteria-num">{{ $loop->iteration }}</span>
                        {{ $k->nama }}
                    </div>
                    <div class="text-end">
                        <div class="nilai-display" id="display_{{ $k->id }}">
                            5.0
                            <small>/ 10</small>
                        </div>
                    </div>
                </div>

                {{-- Slider --}}
                <div class="slider-wrap">
                    <input type="range"
                           name="{{ $k->id }}"
                           id="slider_{{ $k->id }}"
                           min="0" max="10" step="0.1"
                           value="{{ old($k->id, 5) }}"
                           data-kriteria="{{ $k->id }}"
                           class="slider-input w-100">
                </div>
                <div class="slider-labels">
                    <span>0 — Sangat Rendah</span>
                    <span>5</span>
                    <span>10 — Sangat Tinggi</span>
                </div>

                {{-- Himpunan fuzzy badges --}}
                <div class="himpunan-list" id="badges_{{ $k->id }}">
                    @foreach($k->subKriteria as $sub)
                        <span class="himpunan-badge"
                              id="badge_{{ $sub->id }}"
                              data-a="{{ $sub->batas_bawah }}"
                              data-b="{{ $sub->batas_tengah }}"
                              data-c="{{ $sub->batas_atas }}"
                              data-idx="{{ $loop->index }}">
                            {{ $sub->nama }}
                            <span class="mu-val" id="mu_{{ $sub->id }}">μ=0.00</span>
                        </span>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        {{-- ── Kolom kanan: ringkasan & tombol ────────────────────── --}}
        <div class="col-lg-4">
            <div class="card" style="position: sticky; top: 70px;">
                <div class="card-header py-3">
                    <i class="fas fa-sliders me-2 text-primary"></i>
                    Ringkasan Preferensi
                </div>
                <div class="card-body">
                    <div id="summaryList">
                        @foreach($kriteria as $k)
                        <div class="summary-item">
                            <span class="label">{{ $k->nama }}</span>
                            <span class="val" id="summary_{{ $k->id }}">5.0</span>
                        </div>
                        @endforeach
                    </div>

                    <hr class="my-3">

                    {{-- Info metode --}}
                    <div class="small text-muted mb-3 p-2 rounded" style="background:#f8fafc;">
                        <i class="fas fa-circle-info me-1 text-primary"></i>
                        Sistem menggunakan <strong>Fuzzy Mamdani</strong> dengan
                        {{ $kriteria->count() }} kriteria dan
                        {{ $kriteria->sum(fn($k) => $k->subKriteria->count()) }} himpunan fuzzy.
                    </div>

                    <button type="submit" class="btn-cari" id="btnCari">
                        <i class="fas fa-magnifying-glass me-2"></i>
                        Cari Rekomendasi
                    </button>

                    @if($rekomendasi->count() > 0)
                    <a href="{{ route('rekomendasi.store') }}"
                       class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                        <i class="fas fa-trophy me-1"></i> Lihat Hasil Terakhir
                    </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</form>

@endif
@endsection

@push('scripts')
<script>
// ── Fungsi keanggotaan segitiga (trimf) ───────────────────────────────────────
// Sama persis dengan implementasi PHP di RekomendasiController
function trimf(x, a, b, c) {
    a = parseFloat(a); b = parseFloat(b); c = parseFloat(c);
    if (isNaN(a) || isNaN(b) || isNaN(c)) return 0;
    if (x <= a || x >= c) return 0.0;
    if (x <= b) return (b - a) > 0 ? (x - a) / (b - a) : 1.0;
    return (c - b) > 0 ? (c - x) / (c - b) : 1.0;
}

// ── Update slider saat digeser ────────────────────────────────────────────────
function updateSlider(kriteriaId, value) {
    value = parseFloat(value);

    // Update angka display
    document.getElementById('display_' + kriteriaId).innerHTML =
        value.toFixed(1) + '<small>/ 10</small>';

    // Update summary panel
    const summaryEl = document.getElementById('summary_' + kriteriaId);
    if (summaryEl) summaryEl.textContent = value.toFixed(1);

    // Update warna track slider
    const slider = document.getElementById('slider_' + kriteriaId);
    const pct    = (value / 10) * 100;
    slider.style.background =
        `linear-gradient(to right, #7c3aed ${pct}%, #e2e8f0 ${pct}%)`;

    // Update derajat keanggotaan tiap himpunan
    const badges = document.querySelectorAll('#badges_' + kriteriaId + ' .himpunan-badge');
    badges.forEach(function(badge) {
        const a   = badge.dataset.a;
        const b   = badge.dataset.b;
        const c   = badge.dataset.c;
        const idx = parseInt(badge.dataset.idx);
        const mu  = trimf(value, a, b, c);

        // Update teks nilai μ
        badge.querySelector('.mu-val').textContent = 'μ=' + mu.toFixed(2);

        // Reset kelas warna
        badge.className = 'himpunan-badge';

        // Beri warna jika aktif (μ > 0)
        if (mu > 0) {
            badge.classList.add('mu-active-' + (idx % 5));
        }
    });
}

// ── Inisialisasi semua slider ─────────────────────────────────────────────────
document.querySelectorAll('.slider-input').forEach(function(slider) {
    const kriteriaId = slider.dataset.kriteria;

    // Set nilai awal
    updateSlider(kriteriaId, slider.value);

    // Event listener saat digeser
    slider.addEventListener('input', function() {
        updateSlider(kriteriaId, this.value);
    });
});

// ── Loading state saat submit ─────────────────────────────────────────────────
document.getElementById('formRekomendasi')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('btnCari');

    // Validasi: pastikan semua slider ada nilainya
    const sliders = document.querySelectorAll('.slider-input');
    let valid = true;
    sliders.forEach(function(s) {
        if (s.value === '' || s.value === null) valid = false;
    });

    if (!valid) {
        e.preventDefault();
        alert('Harap isi semua kriteria terlebih dahulu.');
        return;
    }

    // Loading state
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses Fuzzy...';
});
</script>
@endpush