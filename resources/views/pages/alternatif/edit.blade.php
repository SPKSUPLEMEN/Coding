@extends('layouts.app')

@section('content')

  <section class="section">
    <a href="{{ route('alternatif.index') }}" class="btn btn-light mb-4"><i class="fas fa-arrow-left"></i>&nbsp Kembali</a>
    <div class="section-header">
      <h1>Ubah Alternatif</h1>
      <div class="section-header-breadcrumb">
          <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
          <div class="breadcrumb-item">Ubah Alternatif</div>
      </div>
    </div>

    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12 col-lg-12">

                <div class="card">
                    <form role="form" method="POST" action="{{ route('alternatif.update',request()->segment(2)) }}">
                        @csrf
                        <input type="hidden" name="_method" value="PATCH">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Jurusan/Prodi</label>
                                <input type="text" value="{{$pengguna->nama}}" class="form-control" disabled>
                              </div>
                            @foreach ($daftarKriteria as $key => $value)                                    
                                <div class="form-group">
                                    <label>{{ $value['nama_kriteria'] }}</label>
                                    <select name={{$value['kriteria_id']}} class="custom-select">
                                        <option value="">Pilih</option>
                                        @foreach ($value['subkriteria'] as $item)
                                            <option value={{$item->id}} {{ old($value['kriteria_id'], $value['subkriteria_id']) == $item->id ? 'selected' : '' }}>{{$item->nama}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has($value['kriteria_id']))
                                        <label class="" style="color: red">
                                            {{ $errors->first($value['kriteria_id']) }}
                                        </label>
                                    @endif
                                </div>
                            @endforeach
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </section>

@endsection