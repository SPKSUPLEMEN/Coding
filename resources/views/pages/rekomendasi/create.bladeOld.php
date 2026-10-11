@extends('layouts.app')

@section('content')

  <section class="section">
    <div class="section-header">
      <h1>Daftar Mahasiswa</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="#">Layout</a></div>
        <div class="breadcrumb-item">Daftar Mahasiswa</div>
      </div>
    </div>

    <div class="section-body">
      {{-- <h2 class="section-title">This is Example Page</h2>
      <p class="section-lead">This page is just an example for you to create your own page.</p> --}}
      <div class="card">
        <div class="card-header">
          <span class="badge badge-secondary" style="border-radius: 5px">Analisa Kriteria</span>
        </div>
        <div class="card-body">
          <form id="form_validation" role="form" method="POST" action="{{ route('rekomendasi.storeKriteria') }}">
            @csrf
            <div class="form-group">
              <div class="input-group">
                <select name="kriteria_1" class="custom-select" id="" required>
                  <option value="" selected>Pilih</option>
                  @foreach ($kriteria as $item)
                    <option value={{$item->kode}}>{{$item->nama}}</option>
                  @endforeach
                </select>
                <select name="nilai" class="custom-select" id="" required>
                  <option value="" selected>Pilih</option>
                  <option value="1">1 - Sama penting dengan</option>
                  <option value="2">2 - Mendekati sedikit lebih penting dari</option>
                  <option value="3">3 - Sedikit lebih penting dari</option>
                  <option value="4">4 - Mendekati lebih penting dari</option>
                  <option value="5">5 - Lebih penting dari</option>
                  <option value="6">6 - Mendekati sangat penting dari</option>
                  <option value="7">7 - Sangat penting dari</option>
                  <option value="8">8 - Mendekati mutlak dari</option>
                  <option value="9">9 - Mutlak sangat penting dari</option>
                </select>
                <select name="kriteria_2" class="custom-select" id="" required>
                  <option value="" selected>Pilih</option>
                  @foreach ($kriteria as $item)
                    <option value={{$item->kode}}>{{$item->nama}}</option>
                  @endforeach
                </select>
                <div class="input-group-append">
                  <button class="btn btn-light" type="submit">Simpan</button>
                </div>
              </div>
            </div>

          </form>
  
          {{-- @foreach ($formKriteria as $key => $item)                    

          <div>
            <div class="input-group" style="margin-top: 0.50rem">
              <div type="text" class="form-control">{{$item[1]}}</div>
              <select name="{{$item[0]}}#{{$item[2]}}" class="custom-select" id="">
                <option value="" selected>Pilih</option>

                <option value="{{$item[0]}}#{{$item[2]}}#1" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#1" ? 'selected' : '' }}>1 : Sama penting dengan</option>
                <option value="{{$item[0]}}#{{$item[2]}}#2" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#2" ? 'selected' : '' }}>2 : Mendekati sedikit lebih penting dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#3" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#3" ? 'selected' : '' }}>3 : Sedikit lebih penting dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#4" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#4" ? 'selected' : '' }}>4 : Mendekati lebih penting dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#5" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#5" ? 'selected' : '' }}>5 : Lebih penting dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#6" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#6" ? 'selected' : '' }}>6 : Mendekati sangat penting dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#7" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#7" ? 'selected' : '' }}>7 : Sangat penting dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#8" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#8" ? 'selected' : '' }}>8 : Mendekati mutlak dari</option>
                <option value="{{$item[0]}}#{{$item[2]}}#9" {{ old("$item[0]#$item[2]") == "$item[0]#$item[2]#9" ? 'selected' : '' }}>9 : Mutlak sangat penting dari</option>
              </select>
              <div type="text" class="form-control">{{$item[3]}}</div>

            </div>
          </div>
          @if ($errors->has("$item[0]#$item[2]"))
          <div class="text-center">
            <label style="color: red">
                {{ $errors->first("$item[0]#$item[2]") }}
            </label>
          </div>
          @endif
        @endforeach --}}

            @foreach ($formKriteria as $key => $item)                    
              {{-- format $item : [ kode_kriteria, nama_kriteria, kode_kriteria, nama_kriteria ] --}}

              <div>
                @if ($item[4])
                    ADAAAA
                @endif
                <div class="input-group" style="margin-top: 0.50rem">
                  <div type="text" class="form-control" @if($item[4]?->kriteria1 === $item[0])style="background-color: orange"@endif>{{$item[1]}}</div>
                  <select name="{{$item[0]}}#{{$item[2]}}" class="custom-select" id="">
                    <option value="" selected>Pilih</option>

                    <option value="1" {{ $item[4]?->bobot == "1" ? 'selected' : '' }}>1 : Sama penting dengan</option>
                    <option value="2" {{ $item[4]?->bobot == "2" ? 'selected' : '' }}>2 : Mendekati sedikit lebih penting dari</option>
                    <option value="3" {{ $item[4]?->bobot == "3" ? 'selected' : '' }}>3 : Sedikit lebih penting dari</option>
                    <option value="4" {{ $item[4]?->bobot == "4" ? 'selected' : '' }}>4 : Mendekati lebih penting dari</option>
                    <option value="5" {{ $item[4]?->bobot == "5" ? 'selected' : '' }}>5 : Lebih penting dari</option>
                    <option value="6" {{ $item[4]?->bobot == "6" ? 'selected' : '' }}>6 : Mendekati sangat penting dari</option>
                    <option value="7" {{ $item[4]?->bobot == "7" ? 'selected' : '' }}>7 : Sangat penting dari</option>
                    <option value="8" {{ $item[4]?->bobot == "8" ? 'selected' : '' }}>8 : Mendekati mutlak dari</option>
                    <option value="9" {{ $item[4]?->bobot == "9" ? 'selected' : '' }}>9 : Mutlak sangat penting dari</option>
                  </select>
                  <div type="text" class="form-control" @if($item[4]?->kriteria2 === $item[2])style="background-color: orange"@endif>{{$item[3]}}</div>
  
                </div>
              </div>
              @if ($errors->has("$item[0]#$item[2]"))
              <div class="text-center">
                <label style="color: red">
                    {{ $errors->first("$item[0]#$item[2]") }}
                </label>
              </div>
              @endif
            @endforeach
            <br>

          <div class="table-responsive">
            <table id="" class="table table-bordered table-md">
              <thead class="text-center">
                  <tr>
                    <th>Kriteria</th>
                    @foreach ($bobot as $key => $item)     
                      <th>{{ $item[$key]["nama"] }}</th>
                    @endforeach
                  </tr>
              </thead>
              <tbody class="text-center">

                @foreach ($bobot as $key => $item)                        
                  <tr>
                    <th scope="row">{{ $item[$key]["nama"] }}</th>
                    @foreach ($item as $item)
                      <th scope="row" style="{{$item->kriteria1 === $item->kriteria2 ? "color: orange":""}}">
                        {{-- {{ $item->kriteria1 }} --- {{ $item->kriteria2 }} ---  --}}
                        {{ number_format($item->bobot, 2, ',', ' ') }}</th>
                    @endforeach
                  </tr>
                @endforeach
                <tr style="border: 2px solid rgb(218, 218, 218)">
                  <th>Jumlah</th>
                  @foreach ($kriteria as $item)
                    <th>{{number_format($item->jumlah_bobot, 2, ',', ' ')}}</th>
                  @endforeach
                </tr>
              </tbody>
            </table>
          </div>


        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="badge badge-secondary" style="border-radius: 5px">Analisa Sub Kriteria</span>
        </div>
        <div class="card-body">

          <form id="form_validation" role="form" method="POST" action="{{ route('rekomendasi.storeSubkriteria') }}">
            @csrf

            @foreach ($formSubkriteria as $key => $data)
              {{-- format $item : [ nama_subkriteria, detail_subkriteria ] --}}  
  
              @if ($key > 0)
                <hr>
              @endif
  
              <strong>Kriteria : {{$data[0]}}</strong>
              <div class="input-group" style="margin-top: 0.50rem">
                <input type="text" class="form-control" value="Sub Kriteria" disabled>
                <select class="custom-select" disabled>
                  <option value="" selected>#</option>
                </select>
                <input type="text" class="form-control" value="Sub Kriteria" disabled>
              </div>         
  
              @foreach ($data[1] as $key => $item)  
                {{-- format $item : [ id_subkriteria, nama_subkriteria, id_subkriteria, nama_subkriteria ] --}}
  
                <div class="input-group" style="margin-top: 0.50rem">
                  <div type="text" class="form-control">{{$item[1]}}</div>
                  <select name="{{$item[0]}}---{{$item[2]}}" class="custom-select" id="">
                    <option value="" selected>Pilih</option>
                    <option value="1">1 - Sama penting dengan</option>
                    <option value="2">2 - Mendekati sedikit lebih penting dari</option>
                    <option value="3">3 - Sedikit lebih penting dari</option>
                    <option value="4">4 - Mendekati lebih penting dari</option>
                    <option value="5">5 - Lebih penting dari</option>
                    <option value="6">6 - Mendekati sangat penting dari</option>
                    <option value="7">7 - Sangat penting dari</option>
                    <option value="8">8 - Mendekati mutlak dari</option>
                    <option value="9">9 - Mutlak sangat penting dari</option>
                  </select>
                  <div type="text" class="form-control">{{$item[3]}}</div>
                </div>
              @endforeach
  
            @endforeach
            
            <br>

            <div class="text-right">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

          </form>

        </div>
      </div>
    </div>
  </section>

@endsection