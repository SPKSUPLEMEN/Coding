@extends('layouts.app')
@section('title', 'Kelola Sub Kriteria')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Sub Kriteria</h4>
        <p class="text-muted mb-0 small">Kelola himpunan fuzzy (membership function) untuk setiap kriteria</p>
    </div>
    <button class="btn btn-primary btn-sm" id="btnTambahSub">
        <i class="fas fa-plus me-1"></i> Tambah Sub Kriteria
    </button>
</div>

{{-- Filter Kriteria --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center g-2">
            <div class="col-auto">
                <label class="form-label mb-0 fw-semibold small text-muted">Filter Kriteria:</label>
            </div>
            <div class="col-sm-4">
                <select id="filterKriteria" class="form-select form-select-sm">
                    <option value="">— Semua Kriteria —</option>
                    @foreach($kriteria as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto ms-auto">
                <span class="badge bg-primary" id="totalSub">-</span>
                <span class="text-muted small ms-1">total himpunan</span>
            </div>
        </div>
    </div>
</div>

{{-- Info membership function --}}
<div class="alert alert-info d-flex align-items-start gap-3 py-3 mb-4">
    <i class="fas fa-circle-info fs-5 mt-1 flex-shrink-0"></i>
    <div class="small">
        <strong>Fungsi Keanggotaan Segitiga (trimf):</strong>
        Setiap himpunan fuzzy didefinisikan oleh tiga titik pada skala <strong>0–10</strong>.
        Titik <strong>Bawah (a)</strong> dan <strong>Atas (c)</strong> adalah batas keanggotaan nol,
        sedangkan titik <strong>Tengah (b)</strong> adalah puncak dengan keanggotaan 1.
        Contoh: Harga "Murah" = (0, 1, 4) artinya keanggotaan penuh di nilai 1, dan nol di luar rentang 0–4.
    </div>
</div>

{{-- Tabel Sub Kriteria --}}
<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-diagram-project me-2 text-warning"></i>
            Daftar Himpunan Fuzzy
            <span class="badge bg-warning text-dark ms-2" id="labelKriteriaAktif" style="display:none"></span>
        </span>
        <button class="btn btn-outline-secondary btn-sm d-none" id="btnResetFilter">
            <i class="fas fa-xmark me-1"></i> Reset Filter
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelSubkriteria" class="table table-hover mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Kriteria</th>
                        <th>Nama Himpunan</th>
                        <th style="width:120px" class="text-center">Bawah (a)</th>
                        <th style="width:120px" class="text-center">Tengah (b)</th>
                        <th style="width:120px" class="text-center">Atas (c)</th>
                        <th style="width:160px" class="text-center">Visualisasi</th>
                        <th style="width:100px" class="text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL TAMBAH / EDIT SUB KRITERIA                             --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalSubkriteria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalSub">Tambah Sub Kriteria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="subId">

                {{-- Pilih Kriteria --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kriteria <span class="text-danger">*</span></label>
                    <select id="subKriteriaId" class="form-select">
                        <option value="">— Pilih Kriteria —</option>
                        @foreach($kriteria as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" id="errKriteriaId"></div>
                </div>

                {{-- Nama Himpunan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Himpunan <span class="text-danger">*</span></label>
                    <input type="text" id="namaSub" class="form-control"
                           placeholder="cth: Murah, Sedang, Mahal, Tinggi, Sangat Tinggi">
                    <div class="invalid-feedback d-block" id="errNamaSub"></div>
                </div>

                {{-- Batas Membership --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Batas Fungsi Keanggotaan <span class="text-danger">*</span></label>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label small text-muted">Batas Bawah (a)</label>
                            <input type="number" id="batasBawah" class="form-control"
                                   min="0" max="10" step="0.1" placeholder="0">
                            <div class="form-text small">μ = 0 di titik ini</div>
                            <div class="invalid-feedback d-block" id="errBatasBawah"></div>
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-muted">Batas Tengah (b)</label>
                            <input type="number" id="batasTengah" class="form-control"
                                   min="0" max="10" step="0.1" placeholder="5">
                            <div class="form-text small">μ = 1 (puncak)</div>
                            <div class="invalid-feedback d-block" id="errBatasTengah"></div>
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-muted">Batas Atas (c)</label>
                            <input type="number" id="batasAtas" class="form-control"
                                   min="0" max="10" step="0.1" placeholder="10">
                            <div class="form-text small">μ = 0 di titik ini</div>
                            <div class="invalid-feedback d-block" id="errBatasAtas"></div>
                        </div>
                    </div>
                </div>

                {{-- Preview segitiga --}}
                <div class="p-3 rounded-3" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                    <div class="small fw-semibold text-muted mb-2">
                        <i class="fas fa-chart-line me-1"></i> Preview Fungsi Segitiga
                    </div>
                    <canvas id="canvasPreview" height="80"
                            style="width:100%; background:#fff; border-radius:6px; border:1px solid #e2e8f0;"></canvas>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">0</small>
                        <small class="text-primary fw-semibold" id="previewLabel">a = ?, b = ?, c = ?</small>
                        <small class="text-muted">10</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" id="btnSimpanSub">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI DELETE                                      --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-triangle-exclamation text-danger fs-2 mb-2 d-block"></i>
                <p class="mb-1 fw-semibold">Yakin hapus himpunan fuzzy ini?</p>
                <small class="text-muted">Data tidak bisa dikembalikan.</small>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm" id="btnConfirmDelete">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── State ─────────────────────────────────────────────────────────────────────
let deleteId     = null;
let activeFilter = null;

const modalSub    = new bootstrap.Modal(document.getElementById('modalSubkriteria'));
const modalDelete = new bootstrap.Modal(document.getElementById('modalDelete'));

// ── DataTable ─────────────────────────────────────────────────────────────────
const dt = $('#tabelSubkriteria').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("subkriteria.datatableAll") }}',
        data: function (d) {
            d.kriteria_id = activeFilter ?? '';
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    },
    columns: [
        { data: 'DT_RowIndex',   orderable: false, searchable: false },
        { data: 'nama_kriteria', searchable: true },
        { data: 'nama',          searchable: true },
        { data: 'batas_bawah',   className: 'text-center' },
        { data: 'batas_tengah',  className: 'text-center' },
        { data: 'batas_atas',    className: 'text-center' },
        { data: 'visualisasi',   orderable: false, searchable: false, className: 'text-center' },
        { data: 'action',        orderable: false, searchable: false, className: 'text-center' },
    ],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    drawCallback: function () {
        $('#totalSub').text(this.api().page.info().recordsTotal);
    }
});

// ── Filter Kriteria ───────────────────────────────────────────────────────────
$('#filterKriteria').on('change', function () {
    activeFilter    = $(this).val() || null;
    const namaLabel = $(this).find('option:selected').text();

    if (activeFilter) {
        $('#labelKriteriaAktif').text(namaLabel).show();
        $('#btnResetFilter').removeClass('d-none');
    } else {
        $('#labelKriteriaAktif').hide();
        $('#btnResetFilter').addClass('d-none');
    }
    dt.ajax.reload();
});

$('#btnResetFilter').on('click', function () {
    $('#filterKriteria').val('');
    activeFilter = null;
    $('#labelKriteriaAktif').hide();
    $(this).addClass('d-none');
    dt.ajax.reload();
});

// ── Preview Canvas ────────────────────────────────────────────────────────────
function drawTriangle(a, b, c) {
    const canvas = document.getElementById('canvasPreview');
    const ctx    = canvas.getContext('2d');
    const W      = canvas.offsetWidth || 500;
    const H      = 80;
    canvas.width  = W;
    canvas.height = H;

    ctx.clearRect(0, 0, W, H);

    // Grid
    ctx.strokeStyle = '#f1f5f9';
    ctx.lineWidth   = 1;
    for (let i = 0; i <= 10; i++) {
        const x = (i / 10) * W;
        ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
    }

    // Baseline
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth   = 1.5;
    ctx.beginPath(); ctx.moveTo(0, H - 10); ctx.lineTo(W, H - 10); ctx.stroke();

    if (isNaN(a) || isNaN(b) || isNaN(c)) return;

    const toX = v => (v / 10) * W;
    const toY = v => (H - 10) - v * (H - 20);

    // Fill segitiga
    ctx.beginPath();
    ctx.moveTo(toX(a), toY(0));
    ctx.lineTo(toX(b), toY(1));
    ctx.lineTo(toX(c), toY(0));
    ctx.closePath();
    ctx.fillStyle = 'rgba(124,58,237,0.15)';
    ctx.fill();

    // Garis segitiga
    ctx.beginPath();
    ctx.moveTo(toX(a), toY(0));
    ctx.lineTo(toX(b), toY(1));
    ctx.lineTo(toX(c), toY(0));
    ctx.strokeStyle = '#7c3aed';
    ctx.lineWidth   = 2;
    ctx.stroke();

    // Label titik
    ctx.fillStyle   = '#7c3aed';
    ctx.font        = 'bold 10px Arial';
    ctx.textAlign   = 'center';
    ctx.fillText('b='+b, toX(b), toY(1) - 5);

    ctx.fillStyle = '#94a3b8';
    ctx.font      = '10px Arial';
    ctx.fillText('a='+a, toX(a), H - 1);
    ctx.fillText('c='+c, toX(c), H - 1);

    $('#previewLabel').text(`a = ${a}, b = ${b}, c = ${c}`);
}

$('#batasBawah, #batasTengah, #batasAtas').on('input', function () {
    drawTriangle(
        parseFloat($('#batasBawah').val()),
        parseFloat($('#batasTengah').val()),
        parseFloat($('#batasAtas').val())
    );
});

// Redraw saat modal terbuka (ukuran canvas sudah benar)
document.getElementById('modalSubkriteria').addEventListener('shown.bs.modal', function () {
    drawTriangle(
        parseFloat($('#batasBawah').val()),
        parseFloat($('#batasTengah').val()),
        parseFloat($('#batasAtas').val())
    );
});

// ── Helper reset form ─────────────────────────────────────────────────────────
function resetFormSub(kriteriaId = '') {
    $('#subId').val('');
    $('#subKriteriaId').val(kriteriaId).removeClass('is-invalid');
    $('#namaSub').val('').removeClass('is-invalid');
    $('#batasBawah, #batasTengah, #batasAtas').val('').removeClass('is-invalid');
    $('#errKriteriaId, #errNamaSub, #errBatasBawah, #errBatasTengah, #errBatasAtas').text('');
    $('#previewLabel').text('a = ?, b = ?, c = ?');
    const canvas = document.getElementById('canvasPreview');
    const ctx    = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// ── TAMBAH Sub Kriteria ───────────────────────────────────────────────────────
$('#btnTambahSub').on('click', function () {
    resetFormSub(activeFilter ?? '');
    $('#titleModalSub').text('Tambah Sub Kriteria');
    modalSub.show();
});

// ── EDIT Sub Kriteria ─────────────────────────────────────────────────────────
$(document).on('click', '.btn-edit-sub', function () {
    const d = $(this).data();
    resetFormSub();
    $('#subId').val(d.id);
    $('#subKriteriaId').val(d.kriteria_id);
    $('#namaSub').val(d.nama);
    $('#batasBawah').val(d.batas_bawah);
    $('#batasTengah').val(d.batas_tengah);
    $('#batasAtas').val(d.batas_atas);
    $('#titleModalSub').text('Edit Sub Kriteria');
    modalSub.show();
});

// ── SIMPAN Sub Kriteria ───────────────────────────────────────────────────────
$('#btnSimpanSub').on('click', function () {
    const id         = $('#subId').val().trim();
    const kriteriaId = $('#subKriteriaId').val().trim();
    const nama       = $('#namaSub').val().trim();
    const bawah      = $('#batasBawah').val().trim();
    const tengah     = $('#batasTengah').val().trim();
    const atas       = $('#batasAtas').val().trim();

    // Reset validasi
    $('#subKriteriaId, #namaSub, #batasBawah, #batasTengah, #batasAtas').removeClass('is-invalid');
    $('#errKriteriaId, #errNamaSub, #errBatasBawah, #errBatasTengah, #errBatasAtas').text('');

    // Validasi client-side
    let valid = true;
    if (!kriteriaId) { $('#subKriteriaId').addClass('is-invalid'); $('#errKriteriaId').text('Kriteria wajib dipilih.'); valid = false; }
    if (!nama)       { $('#namaSub').addClass('is-invalid');       $('#errNamaSub').text('Nama himpunan tidak boleh kosong.'); valid = false; }
    if (bawah === '') { $('#batasBawah').addClass('is-invalid');   $('#errBatasBawah').text('Batas bawah wajib diisi.'); valid = false; }
    if (tengah === '') { $('#batasTengah').addClass('is-invalid'); $('#errBatasTengah').text('Batas tengah wajib diisi.'); valid = false; }
    if (atas === '')   { $('#batasAtas').addClass('is-invalid');   $('#errBatasAtas').text('Batas atas wajib diisi.'); valid = false; }
    if (!valid) return;

    if (parseFloat(bawah) > parseFloat(tengah) || parseFloat(tengah) > parseFloat(atas)) {
        Swal.fire({ icon: 'warning', title: 'Urutan Salah', text: 'Harus: Batas Bawah ≤ Batas Tengah ≤ Batas Atas.' });
        return;
    }

    const $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url:  id ? `/subkriteria/${id}` : '{{ route("subkriteria.store") }}',
        type: 'POST',
        data: {
            _token:       $('meta[name="csrf-token"]').attr('content'),
            _method:      id ? 'PUT' : 'POST',
            kriteria_id:  kriteriaId,
            nama:         nama,
            batas_bawah:  bawah,
            batas_tengah: tengah,
            batas_atas:   atas,
        },
        success: function () {
            modalSub.hide();
            dt.ajax.reload(null, false);
            Swal.fire({
                icon: 'success', title: 'Berhasil',
                text: id ? 'Sub kriteria berhasil diperbarui.' : 'Sub kriteria berhasil ditambahkan.',
                timer: 1800, showConfirmButton: false
            });
        },
        error: function (xhr) {
            const err = xhr.responseJSON?.message || {};
            if (typeof err === 'object') {
                if (err.kriteria_id)  { $('#subKriteriaId').addClass('is-invalid'); $('#errKriteriaId').text(err.kriteria_id[0]); }
                if (err.nama)         { $('#namaSub').addClass('is-invalid');        $('#errNamaSub').text(err.nama[0]); }
                if (err.batas_bawah)  { $('#batasBawah').addClass('is-invalid');    $('#errBatasBawah').text(err.batas_bawah[0]); }
                if (err.batas_tengah) { $('#batasTengah').addClass('is-invalid');   $('#errBatasTengah').text(err.batas_tengah[0]); }
                if (err.batas_atas)   { $('#batasAtas').addClass('is-invalid');     $('#errBatasAtas').text(err.batas_atas[0]); }
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err || 'Terjadi kesalahan.' });
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
        }
    });
});

// ── HAPUS Sub Kriteria ────────────────────────────────────────────────────────
$(document).on('click', '.btn-delete-sub', function () {
    deleteId = $(this).data('id');
    modalDelete.show();
});

$('#btnConfirmDelete').on('click', function () {
    const $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...');

    $.ajax({
        url:  `/subkriteria/${deleteId}`,
        type: 'POST',
        data: {
            _token:  $('meta[name="csrf-token"]').attr('content'),
            _method: 'DELETE'
        },
        success: function () {
            modalDelete.hide();
            dt.ajax.reload(null, false);
            Swal.fire({ icon: 'success', title: 'Berhasil Dihapus', timer: 1500, showConfirmButton: false });
        },
        error: function (xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan.' });
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fas fa-trash me-1"></i> Hapus');
        }
    });
});
</script>
@endpush
