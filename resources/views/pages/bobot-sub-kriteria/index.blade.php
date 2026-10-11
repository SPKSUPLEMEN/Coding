@extends('layouts.app')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Bobot Sub Kriteria</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item">Bobot Sub Kriteria</div>
      </div>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body p-4">
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
                  <tbody class="">
                    <?php $no=0; ?>
                    @foreach ($kriteria as $item)     
                      <?php $no++; ?>                   
                      <tr>
                        <th scope="row">{{ $no }}</th>
                        <th scope="row">K-{{ $item->kode }}</th>
                        <th scope="row">{{ $item->nama }}</th>
                        <th scope="row">
                          <a href="{{ route('bobot-sub-kriteria.edit',$item->id) }}" class="edit btn btn-light btn-sm">Kelola Bobot</a>
                        </th>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
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
        columnDefs: [
          { className: "dt-center", targets: [ 0, 1, 2, 3 ] }
        ]
      });

    });
  </script>
@endpush