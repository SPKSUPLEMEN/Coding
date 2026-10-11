@extends('layouts.app')
@section('title', 'Kelola Fuzzy Rules')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Fuzzy Rules</h4>
        <p class="text-muted mb-0 small">Atur aturan IF–THEN untuk inferensi Fuzzy Mamdani</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalRule">
            <i class="fas fa-plus me-1"></i> Tambah Rule
        </button>
    </div>
</div>

{{-- Info cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-primary" id="totalRules">-</div>
            <div class="text-muted small">Total Rules</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-success">{{ $suplemen->count() }}</div>
            <div class="text-muted small">Suplemen</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center py-3">
            <div class="fs-2 fw-bold text-warning">{{ $kriteria->count() }}</div>
            <div class="text-muted small">Kriteria</div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header py-3">
        <i class="fas fa-code-branch me-2 text-primary"></i> Daftar Fuzzy Rules
    </div>
    <div class="card-body">
        <table id="tabelRule" class="table table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Rule</th>
                    <th>Kondisi IF</th>
                    <th>THEN (Suplemen)</th>
                    <th>Bobot</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL TAMBAH / EDIT RULE --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalRule" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalRule">Tambah Fuzzy Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ruleId">

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nama Rule</label>
                        <input type="text" id="namaRule" class="form-control" placeholder="cth: Rule 1">
                        <div class="invalid-feedback" id="errNamaRule"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bobot Rule (0–1)</label>
                        <input type="number" id="bobotRule" class="form-control" min="0" max="1" step="0.1" value="1">
                    </div>
                </div>

                {{-- Kondisi IF --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Kondisi <span class="badge bg-primary">IF</span>
                        <small class="text-muted ms-1 fw-normal">— pilih 1 himpunan per kriteria</small>
                    </label>
                    <div class="row g-2" id="kondisiForm">
                        @foreach($kriteria as $k)
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                                <div class="fw-semibold small mb-2 text-primary">{{ $k->nama }}</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($k->subKriteria as $sub)
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input kondisi-check"
                                               type="radio"
                                               name="kondisi_{{ $k->id }}"
                                               value="{{ $sub->id }}"
                                               id="sub_{{ $sub->id }}"
                                               data-kriteria="{{ $k->id }}">
                                        <label class="form-check-label small" for="sub_{{ $sub->id }}">
                                            {{ $sub->nama }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="invalid-feedback d-block" id="errKondisi"></div>
                </div>

                {{-- THEN --}}
                <div class="mb-1">
                    <label class="form-label fw-semibold">
                        Hasil <span class="badge bg-success">THEN</span>
                        <small class="text-muted ms-1 fw-normal">— suplemen yang direkomendasikan</small>
                    </label>
                    <select id="suplemenId" class="form-select">
                        <option value="">-- Pilih Suplemen --</option>
                        @foreach($suplemen as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" id="errSuplemen"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" id="btnSimpanRule">
                    <i class="fas fa-save me-1"></i> Simpan Rule
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DELETE --}}
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-triangle-exclamation text-danger fs-2 mb-2 d-block"></i>
                <p class="mb-1 fw-semibold">Yakin hapus rule ini?</p>
                <small class="text-muted">Data tidak bisa dikembalikan.</small>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm" id="btnConfirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteId = null;

// ── DataTable ─────────────────────────────────────────────────────────────────
const dt = $('#tabelRule').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("fuzzy-rule.datatable") }}',
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
        { data: 'nama_rule' },
        { data: 'kondisi_detail', orderable: false },
        { data: 'suplemen' },
        { data: 'bobot_rule', width: '70px' },
        { data: 'action', orderable: false, searchable: false, width: '80px' }
    ],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    drawCallback() {
        $('#totalRules').text(this.api().page.info().recordsTotal);
    }
});

// ── Reset modal saat dibuka untuk tambah baru ─────────────────────────────────
$('#modalRule').on('show.bs.modal', function (e) {
    if ($(e.relatedTarget).length) {
        $('#ruleId').val('');
        $('#namaRule, #bobotRule').val('');
        $('#bobotRule').val(1);
        $('#suplemenId').val('');
        $('.kondisi-check').prop('checked', false);
        $('#titleModalRule').text('Tambah Fuzzy Rule');
        clearErrors();
    }
});

function clearErrors() {
    $('#errNamaRule, #errKondisi, #errSuplemen').text('');
    $('#namaRule, #suplemenId').removeClass('is-invalid');
}

// ── Edit rule ─────────────────────────────────────────────────────────────────
$(document).on('click', '.show-edit-modal', function () {
    const id = $(this).data('id');
    clearErrors();

    $.get(`/fuzzy-rule/${id}/edit`, function (data) {
        $('#ruleId').val(data.id);
        $('#namaRule').val(data.nama_rule);
        $('#bobotRule').val(data.bobot_rule);
        $('#suplemenId').val(data.suplemen_id);

        // Set kondisi
        $('.kondisi-check').prop('checked', false);
        data.kondisi.forEach(subId => {
            $(`#sub_${subId}`).prop('checked', true);
        });

        $('#titleModalRule').text('Edit Fuzzy Rule');
        new bootstrap.Modal('#modalRule').show();
    });
});

// ── Simpan rule ───────────────────────────────────────────────────────────────
$('#btnSimpanRule').on('click', function () {
    clearErrors();

    const id     = $('#ruleId').val();
    const url    = id ? `/fuzzy-rule/${id}` : '{{ route("fuzzy-rule.store") }}';
    const method = id ? 'PUT' : 'POST';

    // Kumpulkan kondisi yang dicheck
    const kondisi = [];
    $('.kondisi-check:checked').each(function () { kondisi.push($(this).val()); });

    $.ajax({
        url, type: method,
        data: {
            nama_rule:   $('#namaRule').val(),
            suplemen_id: $('#suplemenId').val(),
            kondisi:     kondisi,
            bobot_rule:  $('#bobotRule').val(),
            _method:     method
        },
        success() {
            bootstrap.Modal.getInstance('#modalRule').hide();
            dt.ajax.reload();
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Fuzzy rule disimpan.', timer: 1800, showConfirmButton: false });
        },
        error(xhr) {
            const err = xhr.responseJSON?.message || {};
            if (err.nama_rule)   { $('#namaRule').addClass('is-invalid');  $('#errNamaRule').text(err.nama_rule[0]); }
            if (err.suplemen_id) { $('#suplemenId').addClass('is-invalid');$('#errSuplemen').text(err.suplemen_id[0]); }
            if (err.kondisi)     { $('#errKondisi').text(err.kondisi[0]); }
            if (typeof err === 'string') Swal.fire({ icon: 'error', title: 'Error', text: err });
        }
    });
});

// ── Hapus rule ────────────────────────────────────────────────────────────────
$(document).on('click', '.show-delete-modal', function () {
    deleteId = $(this).data('id');
    new bootstrap.Modal('#modalDelete').show();
});

$('#btnConfirmDelete').on('click', function () {
    $.ajax({
        url: `/fuzzy-rule/${deleteId}`, type: 'DELETE',
        success() {
            bootstrap.Modal.getInstance('#modalDelete').hide();
            dt.ajax.reload();
            Swal.fire({ icon: 'success', title: 'Dihapus', timer: 1500, showConfirmButton: false });
        },
        error(xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message });
        }
    });
});
</script>
@endpush
