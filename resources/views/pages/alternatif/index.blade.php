@extends('layouts.app')
@section('title', 'Kelola Produk Suplemen')

@push('styles')
<style>
    .product-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        transition: transform .2s, box-shadow .2s;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,.1);
    }
    .product-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .char-counter { font-size: .75rem; color: #94a3b8; text-align: right; }
    .char-counter.warn { color: #f59e0b; }
    .char-counter.over { color: #ef4444; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Produk Suplemen</h4>
        <p class="text-muted mb-0 small">Kelola daftar produk suplemen gym yang dapat direkomendasikan</p>
    </div>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus me-1"></i> Tambah Produk
    </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-primary" id="statTotal">{{ $totalProduk }}</div>
            <div class="text-muted small">Total Produk</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-success" id="statDenganRule">-</div>
            <div class="text-muted small">Punya Rule</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-warning" id="statTanpaRule">-</div>
            <div class="text-muted small">Tanpa Rule</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-info" id="statTotalRule">-</div>
            <div class="text-muted small">Total Rules</div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header py-3">
        <i class="fas fa-box me-2 text-primary"></i> Daftar Produk Suplemen Gym
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelAlternatif" class="table table-hover mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th style="width:110px" class="text-center">Fuzzy Rules</th>
                        <th style="width:110px" class="text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL TAMBAH / EDIT                                          --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="produkId">

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nama Produk <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="namaProduk" class="form-control"
                           placeholder="cth: Whey Protein, BCAA, Creatine, Mass Gainer"
                           maxlength="150">
                    <div class="d-flex justify-content-between mt-1">
                        <div class="invalid-feedback d-block" id="errNama"></div>
                        <span class="char-counter" id="counterNama">0/150</span>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea id="deskripsiProduk" class="form-control" rows="4"
                              placeholder="Jelaskan manfaat, kandungan utama, dan keunggulan produk ini..."
                              maxlength="500"></textarea>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="invalid-feedback d-block" id="errDeskripsi"></div>
                        <span class="char-counter" id="counterDesc">0/500</span>
                    </div>
                </div>

                {{-- Tips --}}
                <div class="p-3 rounded-3 small" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                    <i class="fas fa-lightbulb text-warning me-1"></i>
                    <strong>Tips:</strong> Nama produk akan muncul sebagai pilihan saat membuat Fuzzy Rule.
                    Pastikan nama unik dan mudah dikenali.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" id="btnSimpan">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL DETAIL                                                 --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-box me-2 text-primary"></i>
                    Detail Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
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
                <p class="mb-1 fw-semibold" id="deleteMsg">Yakin hapus produk ini?</p>
                <small class="text-muted" id="deleteSubMsg">Data tidak bisa dikembalikan.</small>
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
let deleteId = null;

const modalForm   = new bootstrap.Modal(document.getElementById('modalForm'));
const modalDetail = new bootstrap.Modal(document.getElementById('modalDetail'));
const modalDelete = new bootstrap.Modal(document.getElementById('modalDelete'));

// ── DataTable ─────────────────────────────────────────────────────────────────
const dt = $('#tabelAlternatif').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("alternatif.datatable") }}',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    },
    columns: [
        { data: 'DT_RowIndex',  orderable: false, searchable: false },
        { data: 'nama' },
        { data: 'deskripsi',    orderable: false },
        { data: 'jumlah_rule',  orderable: false, searchable: false, className: 'text-center' },
        { data: 'action',       orderable: false, searchable: false, className: 'text-center' },
    ],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    drawCallback: function () {
        updateStats();
    }
});

// ── Hitung stats dari data tabel ──────────────────────────────────────────────
function updateStats() {
    $.get('{{ route("alternatif.datatable") }}', { draw:1, start:0, length:999 }, function(res) {
        const rows        = res.data || [];
        const total       = rows.length;
        const denganRule  = rows.filter(r => r.jumlah_rule && r.jumlah_rule.includes('bg-primary')).length;
        const tanpaRule   = total - denganRule;
        const totalRule   = rows.reduce((acc, r) => {
            const m = r.jumlah_rule ? r.jumlah_rule.match(/(\d+) rule/) : null;
            return acc + (m ? parseInt(m[1]) : 0);
        }, 0);

        $('#statTotal').text(total);
        $('#statDenganRule').text(denganRule);
        $('#statTanpaRule').text(tanpaRule);
        $('#statTotalRule').text(totalRule);
    });
}
updateStats();

// ── Character counter ─────────────────────────────────────────────────────────
function updateCounter(input, counter, max) {
    const len = $(input).val().length;
    $(counter).text(`${len}/${max}`);
    $(counter).removeClass('warn over');
    if (len > max * 0.9) $(counter).addClass(len >= max ? 'over' : 'warn');
}

$('#namaProduk').on('input', function()      { updateCounter(this, '#counterNama', 150); });
$('#deskripsiProduk').on('input', function() { updateCounter(this, '#counterDesc', 500); });

// ── Helper reset form ─────────────────────────────────────────────────────────
function resetForm() {
    $('#produkId').val('');
    $('#namaProduk').val('').removeClass('is-invalid');
    $('#deskripsiProduk').val('').removeClass('is-invalid');
    $('#errNama, #errDeskripsi').text('');
    $('#counterNama').text('0/150').removeClass('warn over');
    $('#counterDesc').text('0/500').removeClass('warn over');
}

// ── TAMBAH ────────────────────────────────────────────────────────────────────
$('#btnTambah').on('click', function () {
    resetForm();
    $('#titleModal').html('<i class="fas fa-plus me-2 text-primary"></i>Tambah Produk Suplemen');
    modalForm.show();
});

// ── EDIT ──────────────────────────────────────────────────────────────────────
$(document).on('click', '.btn-edit', function () {
    resetForm();
    const $btn = $(this);
    $('#produkId').val($btn.data('id'));
    $('#namaProduk').val($btn.data('nama'));
    $('#deskripsiProduk').val($btn.data('deskripsi'));
    updateCounter('#namaProduk',      '#counterNama', 150);
    updateCounter('#deskripsiProduk', '#counterDesc', 500);
    $('#titleModal').html('<i class="fas fa-edit me-2 text-warning"></i>Edit Produk Suplemen');
    modalForm.show();
});

// ── SIMPAN (Tambah & Edit) ────────────────────────────────────────────────────
$('#btnSimpan').on('click', function () {
    const id   = $('#produkId').val().trim();
    const nama = $('#namaProduk').val().trim();
    const desc = $('#deskripsiProduk').val().trim();

    // Reset validasi
    $('#namaProduk, #deskripsiProduk').removeClass('is-invalid');
    $('#errNama, #errDeskripsi').text('');

    // Validasi client
    if (!nama) {
        $('#namaProduk').addClass('is-invalid');
        $('#errNama').text('Nama produk tidak boleh kosong.');
        return;
    }

    const $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url:  id ? `/alternatif/${id}` : '{{ route("alternatif.store") }}',
        type: 'POST',
        data: {
            _token:     $('meta[name="csrf-token"]').attr('content'),
            _method:    id ? 'PUT' : 'POST',
            nama:       nama,
            deskripsi:  desc,
        },
        success: function () {
            modalForm.hide();
            dt.ajax.reload(null, false);
            updateStats();
            Swal.fire({
                icon: 'success', title: 'Berhasil',
                text: id ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.',
                timer: 1800, showConfirmButton: false
            });
        },
        error: function (xhr) {
            const err = xhr.responseJSON?.message || {};
            if (typeof err === 'object') {
                if (err.nama)      { $('#namaProduk').addClass('is-invalid');      $('#errNama').text(err.nama[0]); }
                if (err.deskripsi) { $('#deskripsiProduk').addClass('is-invalid'); $('#errDeskripsi').text(err.deskripsi[0]); }
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err || 'Terjadi kesalahan.' });
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
        }
    });
});

// ── DETAIL ────────────────────────────────────────────────────────────────────
$(document).on('click', '.btn-detail', function () {
    const id = $(this).data('id');
    $('#detailContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
    modalDetail.show();

    $.get(`/alternatif/${id}`, function (res) {
        const d = res.data;
        $('#detailContent').html(`
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);
                            display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">${d.nama}</h5>
                    <small class="text-muted">Ditambahkan: ${d.created_at}</small>
                </div>
            </div>

            <div class="mb-3">
                <div class="text-muted small fw-semibold mb-1 text-uppercase" style="letter-spacing:.05em">Deskripsi</div>
                <p class="mb-0">${d.deskripsi || '<span class="text-muted fst-italic">Belum ada deskripsi</span>'}</p>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="p-3 rounded-3 text-center" style="background:#f0f9ff;border:1.5px solid #bae6fd;">
                        <div class="fs-3 fw-bold text-primary">${d.jumlah_rule}</div>
                        <div class="small text-muted">Fuzzy Rules Terkait</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:1.5px solid #bbf7d0;">
                        <div class="fs-3 fw-bold text-success">
                            ${d.jumlah_rule > 0 ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle text-warning"></i>'}
                        </div>
                        <div class="small text-muted">${d.jumlah_rule > 0 ? 'Aktif di Sistem' : 'Belum Ada Rule'}</div>
                    </div>
                </div>
            </div>

            ${d.jumlah_rule === 0 ? `
            <div class="alert alert-warning small mt-3 mb-0 py-2">
                <i class="fas fa-triangle-exclamation me-1"></i>
                Produk ini belum memiliki fuzzy rule sehingga tidak akan muncul sebagai rekomendasi.
                Tambahkan rule di menu <strong>Fuzzy Rules</strong>.
            </div>` : ''}
        `);
    }).fail(function() {
        $('#detailContent').html('<div class="alert alert-danger">Gagal memuat data.</div>');
    });
});

// ── HAPUS ─────────────────────────────────────────────────────────────────────
$(document).on('click', '.btn-delete', function () {
    const $btn     = $(this);
    const id       = $btn.data('id');
    const nama     = $btn.data('nama');
    const jumlahRule = parseInt($btn.data('rule')) || 0;

    deleteId = id;

    if (jumlahRule > 0) {
        // Produk punya rule — tampilkan peringatan
        $('#deleteMsg').text(`Tidak bisa hapus "${nama}"`);
        $('#deleteSubMsg').html(
            `<span class="text-danger">Produk ini masih digunakan oleh <strong>${jumlahRule} fuzzy rule</strong>.<br>
            Hapus rule terkait di menu <strong>Fuzzy Rules</strong> terlebih dahulu.</span>`
        );
        $('#btnConfirmDelete').prop('disabled', true).addClass('d-none');
    } else {
        // Aman dihapus
        $('#deleteMsg').text(`Yakin hapus "${nama}"?`);
        $('#deleteSubMsg').text('Data tidak bisa dikembalikan.');
        $('#btnConfirmDelete').prop('disabled', false).removeClass('d-none');
    }
    modalDelete.show();
});

// Reset tombol hapus saat modal ditutup
document.getElementById('modalDelete').addEventListener('hidden.bs.modal', function () {
    $('#btnConfirmDelete').prop('disabled', false).removeClass('d-none')
        .html('<i class="fas fa-trash me-1"></i> Hapus');
});

$('#btnConfirmDelete').on('click', function () {
    const $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...');

    $.ajax({
        url:  `/alternatif/${deleteId}`,
        type: 'POST',
        data: {
            _token:  $('meta[name="csrf-token"]').attr('content'),
            _method: 'DELETE'
        },
        success: function () {
            modalDelete.hide();
            dt.ajax.reload(null, false);
            updateStats();
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