@extends('layouts.app')

@section('content')
  <section class="section">

        <a href="{{ route('bobot-sub-kriteria.index') }}" class="btn btn-light mb-4"><i class="fas fa-arrow-left"></i>&nbsp Kembali</a>

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
                        <div class="card-header">
                            <span class="badge badge-secondary" style="border-radius: 5px">Kriteria : {{ $kriteria ? $kriteria->nama : '-' }}</span>
                        </div>
                        <div class="card-body p-4">
                            @if (sizeof($subkriteria) === 0)
                                <div class="alert alert-light">
                                    Tidak tersedia kriteria.
                                </div>
                            @else
                                <form id="form_validation" role="form" method="POST" action="{{ route('bobot-sub-kriteria.store') }}">
                                    @csrf
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="hidden" name="kriteria_id" value={{request()->segment(2)}}>
                                            <select name="kriteria_1" class="custom-select" id="" required>
                                                <option value="" selected>Pilih</option>
                                                @foreach ($subkriteria as $item)
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
                                                @foreach ($subkriteria as $item)
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
                                                @foreach ($subkriteria as $item)
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
                                                <th colspan={{sizeof($subkriteria)}}>Nilai Eigen</th>
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
                                                    $jumlah += $subkriteria[$key]->jumlah_eigen;
                                                    $rata2 += $subkriteria[$key]->rata_eigen;
                                                ?>                      
                                                <tr>
                                                    @foreach ($item as $item)
                                                        <th scope="row">{{ number_format($item->eigen, 5, ',', ' ') }}</th>
                                                    @endforeach
                                                    <th scope="row">{{ number_format($subkriteria[$key]->jumlah_eigen, 8, ',', ' ') }}</th>
                                                    <th scope="row">{{ number_format($subkriteria[$key]->rata_eigen, 8, ',', ' ') }}</th>
                                                </tr>
                                            @endforeach
                                            <tr style="border: 2px solid rgb(218, 218, 218)">
                                                <th colspan={{sizeof($subkriteria)}}>Total</th>
                                                <th>{{$jumlah}}</th>
                                                <th>{{$rata2}}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <?php 
                                    $lamdaMax = 0; $ci = 0; $cr = 0; $ir = 0;
                    
                                    foreach ($subkriteria as $value) {
                                        $lamdaMax = number_format(($value->jumlah_bobot * $value->rata_eigen)+$lamdaMax, 5) ;
                                    }
                    
                                    if($lamdaMax > 0 && sizeof($subkriteria)-1 > 0) $ci = ($lamdaMax-sizeof($subkriteria))/(sizeof($subkriteria)-1);
                    
                                    //rasio konsistensi
                                    if(sizeof($subkriteria) == 1) $ir = 0;
                                    elseif(sizeof($subkriteria) == 2) $ir = 0;
                                    elseif(sizeof($subkriteria) == 3) $ir = 0.58;
                                    elseif(sizeof($subkriteria) == 4) $ir = 0.9;
                                    elseif(sizeof($subkriteria) == 5) $ir = 1.12;
                                    elseif(sizeof($subkriteria) == 6) $ir = 1.24;
                                    elseif(sizeof($subkriteria) == 7) $ir = 1.32;
                                    elseif(sizeof($subkriteria) == 8) $ir = 1.41;
                                    elseif(sizeof($subkriteria) == 9) $ir = 1.46;
                                    elseif(sizeof($subkriteria) == 10) $ir = 1.49;
                    
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