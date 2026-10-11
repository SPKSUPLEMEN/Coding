<!DOCTYPE html>

<head>
    <title>Rekomendasi</title>
    <meta charset="utf-8">
    <style>
        /** Define the margins of your page **/
        @page {
            margin: 100px 50px;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            background-color: #4ec7ff;
            color: white;
            text-align: left;
            line-height: 35px;

            padding-left: 10px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            background-color: #4ec7ff;
            color: white;
            text-align: center;
            line-height: 35px;
        }

        .border-collapse {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px
        }

        .flex-container {
            padding: 0;
            margin: 0;
            list-style: none;
            -ms-box-orient: horizontal;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -moz-flex;
            display: -webkit-flex;
            display: flex;
        }

        .wrap {
            -webkit-flex-wrap: wrap;
            flex-wrap: wrap;
        }

        .flex-item {
            border-radius: 5px;
            background: tomato;
            padding: 3px;
            margin-right: 5px;

            width: 100px;

            color: white;
            text-align: center;

            line-height: 25px;
        }
    </style>

</head>

<body>
    <!-- Define header and footer blocks before your content -->
    <header>
        Rekomendasi Jurusan/Program Studi
    </header>

    <footer>
        Universitas Negeri Manado TI <?php echo date("Y"); ?>
    </footer>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
        <div>
            <div style="text-align : center; font-size: 14px; font-weight: bold; text-transform: uppercase">
                <p>Informasi Perbandingan Kriteria</p>
            </div>

            <p>Penyusunan matriks berpasangan untuk melakukan normalisasi bobot tingkat kepentingan pada tiap-tiap elemen pada hirarki masing-masing. Pada tahapan ini analisis dilakukan oleh pihak Universitas yang kompeten dibidangnya. </p>

            <table style="" class="border-collapse" width="100%">
                <tr>
                    <th class="border-collapse">Kriteria</th>
                    <th class="border-collapse">---</th>
                    <th class="border-collapse">Kriteria</th>
                </tr>
                @foreach ($subkriteria as $item)
                <tr>
                    <td class="border-collapse">{{$item->kriteria1_nama}}</td>
                    <td class="border-collapse">
                        @if(intval($item->bobot) === 1) Sama penting dengan
                        @elseif(intval($item->bobot) === 2) Mendekati sedikit lebih penting dari
                        @elseif(intval($item->bobot) === 3) Sedikit lebih penting dari
                        @elseif(intval($item->bobot) === 4) Mendekati lebih penting dari
                        @elseif(intval($item->bobot) === 5) Lebih penting dari
                        @elseif(intval($item->bobot) === 6) Mendekati sangat penting dari
                        @elseif(intval($item->bobot) === 7) Sangat penting dari
                        @elseif(intval($item->bobot) === 8) Mendekati mutlak dari
                        @elseif(intval($item->bobot) === 9) Mutlak sangat penting dari
                        @endif
                    </td>
                    <td class="border-collapse">{{$item->kriteria2_nama}}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div>
            <br>
            <div style="text-align : center; font-size: 14px; font-weight: bold; text-transform: uppercase">
                <p>Urutan Rekomendasi Jurusan/Program Studi</p>
            </div>

            <p>Daftar perankingan Jurusan/Program Studi dibawah ini dihitung berdasarkan perbandingan kriteria pada tabel diatas.</p>

            <div style="">
                <table style="text-align: center" class="border-collapse" width="100%">
                    <tr class="">
                        <th class="border-collapse" style="width: 25px">No</th>
                        <th class="border-collapse">Nama</th>
                    </tr>
                    <?php
                    $no = 0;
                    ?>
                    @foreach ($perankingan as $item)
                    <?php $no++; ?>
                    <tr>
                        <td class="border-collapse">{{$no}}</td>
                        <td class="border-collapse">{{$item->nama}}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div>
            <br>
            <div style="text-align : center; font-size: 14px; font-weight: bold; text-transform: uppercase">
                <p>Daftar Jurusan/Program Studi Sesuai Kriteria</p>
            </div>

            <div>Daftar kriteria yang dipilih:</div>

            <ul class="flex-container wrap">
                @foreach ($kriteriaUser as $item)
                <span class="flex-item">{{$item->kriteria}} : {{$item->subkriteria}}</span>
                @endforeach
            </ul>

            <br>

            <table style="text-align: center" class="border-collapse" width="100%">
                <tr class="">
                    <th style="width: 50px" class="border-collapse">No</th>
                    <th class="border-collapse">Nama</th>
                </tr>
                <?php
                $no = 0;
                ?>
                @foreach ($rekomendasi as $item)
                <?php $no++; ?>
                <tr>
                    <td class="border-collapse">{{$no}}</td>
                    <td class="border-collapse">{{$item->nama}}</td>
                </tr>
                @endforeach
                @if(count($rekomendasi) <=0) <tr>
                    <td colspan="2">Tidak tersedia.</td>
                    </tr>
                    @endif
            </table>
        </div>


        {{-- <p style="page-break-after: never;">
            Content Page 2
        </p> --}}

    </main>
</body>

</html>