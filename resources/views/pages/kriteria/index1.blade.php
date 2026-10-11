@extends('layouts.app')

@section('content')

  <section class="section">
    <div class="section-header">
      <h1>Daftar Kriteria</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="#">Layout</a></div>
        <div class="breadcrumb-item">Daftar Kriteria</div>
      </div>
    </div>

    <div class="section-body">
      {{-- <h2 class="section-title">This is Example Page</h2>
      <p class="section-lead">This page is just an example for you to create your own page.</p> --}}
      <div class="card">
        <div class="card-header">
          <button id="showAddModalBtn" type="button" class="btn btn-light">
            Tambah <i class="fas fa-plus-square"></i>
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="datatable" class="table table-striped table-md">
              <thead>
                  <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                  </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Add New Kriteria -->
  <div class="modal fade" id="addNewDataModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="newDataForm">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Tambah Kriteria</h5>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" required="">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="closeAddBtn" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" id="confirmAddBtn" class="btn btn-primary" >Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Kriteria -->
  <div class="modal fade" id="editDataModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="editDataForm">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Ubah Data</h5>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editDataId">
            <div class="form-group">
              <label>Nama</label>
              <input type="text" id="nama" name="nama" class="form-control" required="">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="closeEditBtn" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" id="confirmEditBtn" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Delete Kriteria -->
  <div class="modal fade" id="deleteDataModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Hapus Data Kriteria</h5>
        </div>
        <div class="modal-body">
          <input type="hidden" id="deleteDataId">
          Anda yakin akan menghapus?
        </div>
        <div class="modal-footer">
          <button type="button" id="closeDeleteBtn" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
          <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Ya</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    $(function () {

      /*------------------------------------------ Render DataTable --------------------------------------------*/ 
      let table = $('#datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        autoWidth: false,
        ajax: "{{ route('kriteria.datatable') }}",
        columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'kode', name: 'kode'},
          {data: 'nama', name: 'nama'},
          {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        columnDefs: [
          { className: "dt-center", targets: [ 0, 1, 2, 3 ] }
        ]
      });

      // /*------------------------------------------ Show modal button add new kriteria --------------------------------------------*/ 
      $('#showAddModalBtn').click(function () {
        $('#addNewDataModal').modal('show');
      });

      // /*------------------------------------------ Create new kriteria --------------------------------------------*/ 
      $('#newDataForm').submit(function (e) {
        e.preventDefault();
        $('#confirmAddBtn').html('Menyimpan...');
      
        // disable button while editing
        $("#confirmAddBtn").prop("disabled",true); 
        $("#closeAddBtn").prop("disabled",true);

        $.ajax({
          data: $('#newDataForm').serialize(),
          url: "{{ route('kriteria.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            $('#newDataForm').trigger("reset");
            $('#addNewDataModal').modal('hide');
            table.ajax.reload();
            Swal.fire({
              title: 'Berhasil',
              text: 'Kriteria berhasil disimpan',
              icon: 'success',
              confirmButtonText: 'OK'
            })
          },
          error: function (data) {
            let html = "";
            const { status, message } = data.responseJSON;

            for (const key in message) {
              html += `<p style="">${message[key]}</p>`
            }
            Swal.fire({
              title: 'Terjadi kesalahan',
              html: status === 'validation error' ? html : message,
              icon: status === 'validation error' ? 'warning' : 'error',
              confirmButtonText: 'OK'
            })
          },
          complete: function(data) {
            $('#confirmAddBtn').html('Simpan');

            // enable button
            $("#confirmAddBtn").prop("disabled",false); 
            $("#closeAddBtn").prop("disabled",false);
          }
        });
      });

      // /*------------------------------------------ Show modal button edit kriteria --------------------------------------------*/
      $(document).on('click', '.show-edit-modal', function () {
        
        let dataId = $(this).data('id');
        let nama = $(this).data('nama');

        $('#editDataModal').modal('show');
        $('#editDataId').val(dataId);
        $('#nama').val(nama);

      });

      // /*------------------------------------------ Edit data kriteria --------------------------------------------*/ 
      $('#editDataForm').submit(function (e) {
        e.preventDefault();
        $('#confirmEditBtn').html('Menyimpan...');
      
        let dataId = $('#editDataId').val();
        let url = '{{ route('kriteria.update', ':id') }}'; url = url.replace(':id', dataId);

        // disable button while editing
        $("#confirmEditBtn").prop("disabled",true); 
        $("#closeEditBtn").prop("disabled",true);

        $.ajax({
          data: $('#editDataForm').serialize(),
          url: url,
          type: "PUT",
          dataType: 'json',
          success: function (data) {
            $('#editDataForm').trigger("reset");
            $('#editDataModal').modal('hide');
            table.ajax.reload();
            Swal.fire({
              title: 'Berhasil',
              text: 'Kriteria berhasil disimpan',
              icon: 'success',
              confirmButtonText: 'OK'
            })
          },
          error: function (data) {
            let html = "";
            const { status, message } = data.responseJSON;

            for (const key in message) {
              html += `<p style="">${message[key]}</p>`
            }
            Swal.fire({
              title: 'Terjadi kesalahan',
              html: status === 'validation error' ? html : message,
              icon: status === 'validation error' ? 'warning' : 'error',
              confirmButtonText: 'OK'
            })
          },
          complete: function(data) {
            $('#confirmEditBtn').html('Simpan');

            // enable button
            $("#confirmEditBtn").prop("disabled",false); 
            $("#closeEditBtn").prop("disabled",false);
          }
        });
      });

      // /*------------------------------------------ Show modal delete kriteria --------------------------------------------*/ 
      $(document).on('click', '.show-delete-modal', function () {
        $('#deleteDataModal').modal('show');
        $('#deleteDataId').val($(this).data("id"));
      });

      // /*------------------------------------------ Delete data kriteria --------------------------------------------*/ 
      $('#confirmDeleteBtn').click(function (e) {
        $(this).html('Menghapus...');

        let dataId = $('#deleteDataId').val();
        let url = '{{ route('kriteria.destroy', ':id') }}'; url = url.replace(':id', dataId);

        // disable button while deleting
        $("#confirmDeleteBtn").prop("disabled",true); 
        $("#closeDeleteBtn").prop("disabled",true);

        $.ajax({
          type: "DELETE",
          url : url,
          success: function (data) {
            $('#deleteDataModal').modal('hide');
            table.ajax.reload();
            Swal.fire({
              title: 'Berhasil',
              text: 'Kriteria berhasil dihapus',
              icon: 'success',
              confirmButtonText: 'OK'
            })
          },
          error: function (data) {
            const { status, message } = data.responseJSON;
            Swal.fire({
              title: 'Terjadi kesalahan',
              text: message,
              icon: 'error',
              confirmButtonText: 'OK'
            })
          },
          complete: function(data) {
            $('#confirmDeleteBtn').html('Ya'); 

            // enable button
            $("#confirmDeleteBtn").prop("disabled",false);
            $("#closeDeleteBtn").prop("disabled",false);
          }
        });
      });

    });
  </script>
@endpush