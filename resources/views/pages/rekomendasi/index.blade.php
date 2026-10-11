@extends('layouts.app')
@section('title', 'Hasil Rekomendasi')

@push('styles')
<style>
    .result-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,.08);
        transition: transform .2s, box-shadow .2s;
        overflow: hidden;
    }
    .result-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,.12);
    }

    .result-card.rank-1 { border-top: 4px solid #f59e0b; }
    .result-card.rank-2 { border-top: 4px solid #94a3b8; }
    .result-card.rank-3 { border-top: 4px solid #ec4899; }
    .result-card.rank-other { border-top: 4px solid #e2e8f0; }

    .skor-wrap { position: relative; }
    .skor-circle {
        width: 72px; height: 72px;
        border-radius: 50%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        font-weight: 700;
    }
    .skor-circle .val  { font-size: 1.2rem; line-height: 1; }
    .skor-circle .lbl  { font-size: .6rem; opacity: .7; }

    .rank-1 .skor-circle { background: #fef3c7; color: #d97706; }
    .rank-2 .skor-circle { background: #f1f5f9; color: #475569; }
    .rank-3 .skor-circle { background: #fce7f3; color: #db2777; }
    .rank-other .skor-circle { background: #f8fafc; color: #64748b; }

    .progress-fuzzy {
        height: 10px;
        border-radius: 5px;
        background: #f1f5f9;
    }
    .progress-fuzzy .progress-bar { border-radius: 5px; }

    .empty-state {
        text-align: center; padding: 4rem 2rem;
        color: #94a3b8;
    }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }

    .global-rank-table th { font-size: .8rem; color: #64748b; font-weight: 600; }
    .global-rank-table td { vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Hasil Rekomendasi</h4>
        <p class="text-muted mb-0 small">Suplemen diurutkan berdasarkan skor kesesuaian Fuzzy Mamdani</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('rekomendasi.create') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-redo me-1"></i> Cari Ulang
        </a>
        <!-- <a href="{{ route('rekomendasi.cetak') }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
        </a> -->
    </div>
</div>

@if($rekomendasi->isEmpty())
    <div class="card">
        <div class="card-body empty-state">
            <i class="fas fa-box-open"></i>
            <h5 class="text-muted">Belum Ada Rekomendasi</h5>
            <p class="small text-muted">Silakan isi form preferensi terlebih dahulu untuk mendapatkan rekomendasi suplemen.</p>
            <a href="{{ route('rekomendasi.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-search me-2"></i>Mulai Cari Rekomendasi
            </a>
        </div>
    </div>
@else

    {{-- Rekomendasi User --}}
    <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="letter-spacing:.05em; font-size:.75rem">
        <i class="fas fa-star me-1 text-warning"></i> Rekomendasi Untuk Anda
    </h6>

    <div class="row g-3 mb-5">
        @foreach($rekomendasi as $item)
        @php
            $rankClass = match($item->ranking) { 1 => 'rank-1', 2 => 'rank-2', 3 => 'rank-3', default => 'rank-other' };
            $rankIcon  = match($item->ranking) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => '#'.$item->ranking };
            $barColor  = match($item->ranking) { 1 => 'bg-warning', 2 => 'bg-secondary', 3 => 'bg-pink', default => 'bg-info' };
            $pct = round($item->skor * 100, 1);
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card result-card {{ $rankClass }} h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small mb-1">Peringkat</div>
                            <div class="fs-4 fw-bold">{{ $rankIcon }}</div>
                        </div>
                        <div class="skor-circle">
                            <span class="val">{{ $pct }}%</span>
                            <span class="lbl">skor</span>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $item->nama }}</h5>
                    @if($item->alternatif && $item->alternatif->deskripsi)
                        <p class="text-muted small mb-3">{{ $item->alternatif->deskripsi }}</p>
                    @endif

                    <div class="mt-auto">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Kesesuaian Fuzzy</small>
                            <small class="fw-semibold">{{ number_format($item->skor, 4) }}</small>
                        </div>
                        <div class="progress-fuzzy">
                            <div class="progress-bar {{ $barColor }}"
                                 style="width: {{ $pct }}%; background: {{ $item->ranking == 1 ? 'linear-gradient(90deg,#f59e0b,#fbbf24)' : ($item->ranking == 2 ? '#94a3b8' : ($item->ranking == 3 ? '#ec4899' : '#6366f1')) }} !important">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel Perankingan Global --}}
    <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="letter-spacing:.05em; font-size:.75rem">
        <i class="fas fa-table me-1"></i> Perankingan Global (Semua Suplemen)
    </h6>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 global-rank-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:60px">#</th>
                        <th>Nama Suplemen</th>
                        <th>Skor Fuzzy</th>
                        <th style="width:200px">Visualisasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perankingan as $i => $p)
                    <tr>
                        <td class="ps-4">
                            @php $ri = $i+1; @endphp
                            <span class="rank-badge {{ $ri <= 3 ? 'rank-'.$ri : 'rank-other' }}">
                                {{ $ri }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $p->alternatif->nama ?? '-' }}</strong>
                        </td>
                        <td>
                            <code class="text-primary">{{ number_format($p->nilai, 6) }}</code>
                        </td>
                        <td>
                            <div class="progress-fuzzy">
                                <div class="progress-bar"
                                     style="width:{{ round($p->nilai*100,1) }}%; background: linear-gradient(90deg,#4f46e5,#7c3aed) !important;">
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($ri == 1)
                                <span class="badge bg-warning text-dark">Sangat Direkomendasikan</span>
                            @elseif($ri == 2)
                                <span class="badge bg-secondary">Direkomendasikan</span>
                            @elseif($ri == 3)
                                <span class="badge" style="background:#ec4899">Cukup Sesuai</span>
                            @else
                                <span class="badge bg-light text-muted">Kurang Sesuai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif
@endsection
