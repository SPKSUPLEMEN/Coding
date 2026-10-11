@extends('layouts.app')
@section('title', 'Kelola Kriteria & Himpunan Fuzzy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Kriteria & Himpunan Fuzzy</h4>
        <p class="text-muted mb-0 small">Kelola kriteria dan batas membership function (fungsi segitiga)</p>
    </div>
    {{-- BUG FIX 1: Hapus data-bs-toggle & data-bs-target dari tombol Tambah.
         Tombol ini sekarang dibuka via JS agar bisa reset form dulu sebelum modal muncul. --}}
    <button class="btn btn-primary btn-sm" id="btnTambahKriteria">
        <i class="fas fa-plus me-1"></i> Tambah Kriteria
    </button>
</div>

{{-- Tabel Kriteria --}}
<div class="card mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list-check me-2 text-primary"></i>Daftar Kriteria</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelKriteria" class="table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Panel Sub Kriteria --}}
<div id="panelSubkriteria" class="card d-none">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-diagram-project me-2 text-warning"></i>
            Himpunan Fuzzy untuk: <strong id="namaKriteriaAktif">-</strong>
        </span>
        <button class="btn btn-warning btn-sm" id="btnTambahSub">
            <i class="fas fa-plus me-1"></i> Tambah Himpunan
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info small py-2 mb-3">
            <i class="fas fa-info-circle me-1"></i>
            Gunakan <strong>fungsi segitiga</strong>: tentukan titik <em>bawah</em>, <em>tengah (puncak)</em>, dan <em>atas</em> pada skala <strong>0–10</strong>.
        </div>
        <div class="table-responsive">
            <table id="tabelSubkriteria" class="table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Himpunan</th>
                        <th>Batas Bawah (a)</th>
                        <th>Batas Tengah (b)</th>
                        <th>Batas Atas (c)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KRITERIA                                               --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalTambahKriteria" tabindex="-1" aria-labelledby="titleModalKriteria" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalKriteria">Tambah Kriteria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kriteriaId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kriteria</label>
                    <input type="text" id="namaKriteria" class="form-control" placeholder="cth: Harga, Protein, Kualitas">
                    {{-- BUG FIX 2: Tambahkan d-block agar error message tampil --}}
                    <div class="invalid-feedback d-block" id="errNamaKriteria"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" id="btnSimpanKriteria">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL SUBKRITERIA                                            --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalSubkriteria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalSub">Tambah Himpunan Fuzzy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="subId">
                <input type="hidden" id="subKriteriaId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Himpunan</label>
                    <input type="text" id="namaSub" class="form-control" placeholder="cth: Murah, Sedang, Mahal">
                    <div class="invalid-feedback d-block" id="errNamaSub"></div>
                </div>
                <div class="row g-2">
                    <div class="col-4">
                        <label class="form-label fw-semibold">Batas Bawah (a)</label>
                        <input type="number" id="batasBawah" class="form-control" min="0" max="10" step="0.1" placeholder="0">
                        <div class="invalid-feedback d-block" id="errBatasBawah"></div>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Batas Tengah (b)</label>
                        <input type="number" id="batasTengah" class="form-control" min="0" max="10" step="0.1" placeholder="5">
                        <div class="invalid-feedback d-block" id="errBatasTengah"></div>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Batas Atas (c)</label>
                        <input type="number" id="batasAtas" class="form-control" min="0" max="10" step="0.1" placeholder="10">
                        <div class="invalid-feedback d-block" id="errBatasAtas"></div>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded small text-muted">
                    <i class="fas fa-chart-line me-1"></i>
                    Preview fungsi segitiga: nilai puncak (μ=1) ada di titik <strong id="previewTengah">b</strong>,
                    keanggotaan = 0 di luar rentang [<strong id="previewBawah">a</strong>, <strong id="previewAtas">c</strong>]
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
                <p class="mb-1 fw-semibold" id="deleteMsg">Yakin ingin menghapus?</p>
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
let activeKriteriaId = null;
let deleteUrl        = null;
let deleteCallback   = null;
let subDatatable     = null;

// Inisialisasi instance modal — dibuat sekali, dipakai berulang
// BUG FIX 3: Gunakan 'new bootstrap.Modal(el)' dengan elemen DOM langsung,
// bukan string selector, dan simpan sebagai variabel agar bisa dipanggil
// .show() / .hide() dengan benar tanpa konflik instance ganda.
const modalKriteria = new bootstrap.Modal(document.getElementById('modalTambahKriteria'));
const modalSub      = new bootstrap.Modal(document.getElementById('modalSubkriteria'));
const modalDelete   = new bootstrap.Modal(document.getElementById('modalDelete'));

// ── DataTable Kriteria ────────────────────────────────────────────────────────
const dtKriteria = $('#tabelKriteria').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("kriteria.datatable") }}',
        // Kirim CSRF token di setiap request datatable
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    },
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
        { data: 'kode',   width: '80px' },
        { data: 'nama' },
        { data: 'action', orderable: false, searchable: false, width: '120px' }
    ],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
});

// ── TAMBAH Kriteria ───────────────────────────────────────────────────────────
$('#btnTambahKriteria').on('click', function () {
    // Reset form sebelum buka modal
    $('#kriteriaId').val('');
    $('#namaKriteria').val('').removeClass('is-invalid');
    $('#errNamaKriteria').text('');
    $('#titleModalKriteria').text('Tambah Kriteria');
    modalKriteria.show();
});

// ── EDIT Kriteria ─────────────────────────────────────────────────────────────
$(document).on('click', '.show-edit-modal', function () {
    $('#kriteriaId').val($(this).data('id'));
    $('#namaKriteria').val($(this).data('nama')).removeClass('is-invalid');
    $('#errNamaKriteria').text('');
    $('#titleModalKriteria').text('Edit Kriteria');
    modalKriteria.show();
});

// ── SIMPAN Kriteria (Tambah & Edit) ───────────────────────────────────────────
$('#btnSimpanKriteria').on('click', function () {
    const id   = $('#kriteriaId').val().trim();
    const nama = $('#namaKriteria').val().trim();

    // Validasi sisi client
    if (!nama) {
        $('#namaKriteria').addClass('is-invalid');
        $('#errNamaKriteria').text('Nama kriteria tidak boleh kosong.');
        return;
    }

    $('#namaKriteria').removeClass('is-invalid');
    $('#errNamaKriteria').text('');

    const $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url:  id ? `/kriteria/${id}` : '{{ route("kriteria.store") }}',
        type: 'POST',
        data: {
            _token:  $('meta[name="csrf-token"]').attr('content'),
            _method: id ? 'PUT' : 'POST',
            nama:    nama
        },
        success: function () {
            modalKriteria.hide();
            dtKriteria.ajax.reload(null, false);
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: id ? 'Kriteria berhasil diperbarui.' : 'Kriteria berhasil ditambahkan.',
                timer: 1800,
                showConfirmButton: false
            });
        },
        error: function (xhr) {
            const resp = xhr.responseJSON;
            if (resp && resp.message && resp.message.nama) {
                $('#namaKriteria').addClass('is-invalid');
                $('#errNamaKriteria').text(resp.message.nama[0]);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: resp?.message || 'Terjadi kesalahan.' });
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
        }
    });
});

// ── HAPUS Kriteria ────────────────────────────────────────────────────────────
$(document).on('click', '.show-delete-modal', function () {
    const id = $(this).data('id');
    deleteUrl = `/kriteria/${id}`;
    deleteCallback = function () {
        dtKriteria.ajax.reload(null, false);
        $('#panelSubkriteria').addClass('d-none');
        activeKriteriaId = null;
    };
    $('#deleteMsg').text('Yakin hapus kriteria ini? Semua himpunan fuzzy terkait akan ikut terhapus.');
    modalDelete.show();
});

// ── KELOLA SubKriteria ────────────────────────────────────────────────────────
$(document).on('click', '.btn-kelola-sub', function () {
    activeKriteriaId = $(this).data('id');
    $('#namaKriteriaAktif').text($(this).data('nama'));
    $('#panelSubkriteria').removeClass('d-none');
    $('html, body').animate({ scrollTop: $('#panelSubkriteria').offset().top - 80 }, 400);
    loadSubDatatable(activeKriteriaId);
});

function loadSubDatatable(kriteriaId) {
    if (subDatatable) {
        subDatatable.destroy();
        $('#tabelSubkriteria').empty().append('<thead class="table-light"><tr><th>#</th><th>Nama Himpunan</th><th>Batas Bawah (a)</th><th>Batas Tengah (b)</th><th>Batas Atas (c)</th><th>Aksi</th></tr></thead>');
        subDatatable = null;
    }
    subDatatable = $('#tabelSubkriteria').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/subkriteria/datatable/${kriteriaId}`,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
            { data: 'nama' },
            { data: 'batas_bawah',  width: '120px' },
            { data: 'batas_tengah', width: '120px' },
            { data: 'batas_atas',   width: '120px' },
            { data: 'action', orderable: false, searchable: false, width: '100px' }
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });
}

// ── TAMBAH SubKriteria ────────────────────────────────────────────────────────
$('#btnTambahSub').on('click', function () {
    $('#subId').val('');
    $('#subKriteriaId').val(activeKriteriaId);
    $('#namaSub').val('').removeClass('is-invalid');
    $('#batasBawah, #batasTengah, #batasAtas').val('').removeClass('is-invalid');
    $('#errNamaSub, #errBatasBawah, #errBatasTengah, #errBatasAtas').text('');
    $('#previewBawah').text('a');
    $('#previewTengah').text('b');
    $('#previewAtas').text('c');
    $('#titleModalSub').text('Tambah Himpunan Fuzzy');
    modalSub.show();
});

// ── EDIT SubKriteria ──────────────────────────────────────────────────────────
$(document).on('click', '.show-edit-subkriteria', function () {
    $('#subId').val($(this).data('id'));
    $('#subKriteriaId').val(activeKriteriaId);
    $('#namaSub').val($(this).data('nama')).removeClass('is-invalid');
    $('#batasBawah').val($(this).data('batas_bawah')).removeClass('is-invalid');
    $('#batasTengah').val($(this).data('batas_tengah')).removeClass('is-invalid');
    $('#batasAtas').val($(this).data('batas_atas')).removeClass('is-invalid');
    $('#errNamaSub, #errBatasBawah, #errBatasTengah, #errBatasAtas').text('');
    // Update preview
    $('#previewBawah').text($(this).data('batas_bawah'));
    $('#previewTengah').text($(this).data('batas_tengah'));
    $('#previewAtas').text($(this).data('batas_atas'));
    $('#titleModalSub').text('Edit Himpunan Fuzzy');
    modalSub.show();
});

// Preview membership bounds saat diketik
$('#batasBawah, #batasTengah, #batasAtas').on('input', function () {
    $('#previewBawah').text($('#batasBawah').val()   || 'a');
    $('#previewTengah').text($('#batasTengah').val() || 'b');
    $('#previewAtas').text($('#batasAtas').val()     || 'c');
});

// ── SIMPAN SubKriteria (Tambah & Edit) ────────────────────────────────────────
$('#btnSimpanSub').on('click', function () {
    const id         = $('#subId').val().trim();
    const kriteriaId = $('#subKriteriaId').val().trim();
    const nama       = $('#namaSub').val().trim();
    const bawah      = $('#batasBawah').val().trim();
    const tengah     = $('#batasTengah').val().trim();
    const atas       = $('#batasAtas').val().trim();

    // Reset error
    $('#namaSub, #batasBawah, #batasTengah, #batasAtas').removeClass('is-invalid');
    $('#errNamaSub, #errBatasBawah, #errBatasTengah, #errBatasAtas').text('');

    // Validasi client-side
    let valid = true;
    if (!nama)   { $('#namaSub').addClass('is-invalid');    $('#errNamaSub').text('Nama himpunan tidak boleh kosong.'); valid = false; }
    if (bawah === '') { $('#batasBawah').addClass('is-invalid');  $('#errBatasBawah').text('Batas bawah wajib diisi.'); valid = false; }
    if (tengah === '') { $('#batasTengah').addClass('is-invalid'); $('#errBatasTengah').text('Batas tengah wajib diisi.'); valid = false; }
    if (atas === '')   { $('#batasAtas').addClass('is-invalid');   $('#errBatasAtas').text('Batas atas wajib diisi.'); valid = false; }
    if (!valid) return;

    if (parseFloat(bawah) > parseFloat(tengah) || parseFloat(tengah) > parseFloat(atas)) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Urutan harus: Batas Bawah ≤ Batas Tengah ≤ Batas Atas.' });
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
            batas_atas:   atas
        },
        success: function () {
            modalSub.hide();
            if (subDatatable) subDatatable.ajax.reload(null, false);
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: id ? 'Himpunan fuzzy berhasil diperbarui.' : 'Himpunan fuzzy berhasil ditambahkan.',
                timer: 1800,
                showConfirmButton: false
            });
        },
        error: function (xhr) {
            const resp = xhr.responseJSON;
            const err  = resp?.message || {};
            if (typeof err === 'object') {
                if (err.nama)         { $('#namaSub').addClass('is-invalid');    $('#errNamaSub').text(err.nama[0]); }
                if (err.batas_bawah)  { $('#batasBawah').addClass('is-invalid'); $('#errBatasBawah').text(err.batas_bawah[0]); }
                if (err.batas_tengah) { $('#batasTengah').addClass('is-invalid');$('#errBatasTengah').text(err.batas_tengah[0]); }
                if (err.batas_atas)   { $('#batasAtas').addClass('is-invalid');  $('#errBatasAtas').text(err.batas_atas[0]); }
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err || 'Terjadi kesalahan.' });
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
        }
    });
});

// ── HAPUS SubKriteria ─────────────────────────────────────────────────────────
$(document).on('click', '.show-delete-subkriteria', function () {
    const id = $(this).data('id');
    deleteUrl = `/subkriteria/${id}`;
    deleteCallback = function () {
        if (subDatatable) subDatatable.ajax.reload(null, false);
    };
    $('#deleteMsg').text('Yakin hapus himpunan fuzzy ini?');
    modalDelete.show();
});

// ── KONFIRMASI HAPUS ──────────────────────────────────────────────────────────
$('#btnConfirmDelete').on('click', function () {
    const $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...');

    $.ajax({
        url:  deleteUrl,
        type: 'POST',
        data: {
            _token:  $('meta[name="csrf-token"]').attr('content'),
            _method: 'DELETE'
        },
        success: function () {
            modalDelete.hide();
            if (typeof deleteCallback === 'function') deleteCallback();
            Swal.fire({
                icon: 'success', title: 'Berhasil Dihapus',
                timer: 1500, showConfirmButton: false
            });
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