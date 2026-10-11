@extends('layouts.app')

@section('content')
  <section class="section">
    <div class="section-header">
      <h1>Daftar Kriteria</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item">Pengguna</div>
      </div>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body p-4">
              @if (sizeof($kriteria) === 0)
                <div class="alert alert-light">
                    Tidak tersedia kriteria.
                </div>
              @else
                <form id="form_validation" role="form" method="POST" action="{{ route('bobot-kriteria.store') }}">
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

                <hr>
                <h6>Matriks Perbandingan Kriteria</h6>
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

                <br>

                <h6>Matriks Nilai Kriteria</h6>
                <div class="table-responsive">
                  <table id="" class="table table-bordered table-md">
                    <thead class="text-center">
                        <tr>
                          <th colspan={{sizeof($kriteria)}}>Nilai Eigen</th>
                          <th>Jumlah</th>
                          <th>Prioritas</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $jumlah = 0; $rata2 = 0;
                      ?>
                      @foreach ($bobot as $key => $item)  
                        <?php
                          $jumlah += $kriteria[$key]->jumlah_eigen;
                          $rata2 += $kriteria[$key]->rata_eigen;
                        ?>                      
                        <tr>
                          {{-- <th scope="row">{{ $item[$key]["nama"] }}</th> --}}
                          @foreach ($item as $item)
                            <th scope="row">{{ number_format($item->eigen, 5, ',', ' ') }}</th>
                          @endforeach
                          <th scope="row">{{ number_format($kriteria[$key]->jumlah_eigen, 8, ',', ' ') }}</th>
                          <th scope="row">{{ number_format($kriteria[$key]->rata_eigen, 8, ',', ' ') }}</th>
                        </tr>
                      @endforeach
                      <tr style="border: 2px solid rgb(218, 218, 218)">
                        <th colspan={{sizeof($kriteria)}}>Total</th>
                        <th>{{$jumlah}}</th>
                        <th>{{$rata2}}</th>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <?php 
                  $lamdaMax = 0; $ci = 0; $cr = 0; $ir = 0;

                  foreach ($kriteria as $value) {
                    $lamdaMax = number_format(($value->jumlah_bobot * $value->rata_eigen)+$lamdaMax, 5) ;
                  }

                  if($lamdaMax > 0 && sizeof($kriteria)-1 > 0) $ci = ($lamdaMax-sizeof($kriteria))/(sizeof($kriteria)-1);

                  //rasio konsistensi
                  if(sizeof($kriteria) == 1) $ir = 0;
                  elseif(sizeof($kriteria) == 2) $ir = 0;
                  elseif(sizeof($kriteria) == 3) $ir = 0.58;
                  elseif(sizeof($kriteria) == 4) $ir = 0.9;
                  elseif(sizeof($kriteria) == 5) $ir = 1.12;
                  elseif(sizeof($kriteria) == 6) $ir = 1.24;
                  elseif(sizeof($kriteria) == 7) $ir = 1.32;
                  elseif(sizeof($kriteria) == 8) $ir = 1.41;
                  elseif(sizeof($kriteria) == 9) $ir = 1.46;
                  elseif(sizeof($kriteria) == 10) $ir = 1.49;

                  if($ir > 0)$cr = $ci/$ir;

                ?>

                <table>
                  <tr>
                    <th>CI = (Lamda Max-n)/(n-1)</th>
                  </tr>
                  <tr>
                    <td>Lamda Max</td>
                    <td>:</td>
                    <td>{{$lamdaMax}}</td>
                  </tr>
                  <tr>
                    <td>CI =</td>
                    <td>:</td>
                    <td>{{$ci}}</td>
                  </tr>
                  <tr>
                    <td>CR=CI/IR</td>
                    <td>:</td>
                    <td>{{$cr}}</td>
                  </tr>
                </table>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection