<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\Alternatif;
use App\Models\FuzzyRule;
use App\Models\Perankingan;
use App\Models\Rekomendasi;
use DB, Auth;

class RekomendasiController extends Controller
{

    // =========================================================
    //  HALAMAN
    // =========================================================

    /**
     * Halaman hasil rekomendasi (setelah proses fuzzy dijalankan)
     */
    public function index()
    {
        $perankingan = Perankingan::with('alternatif')
            ->orderBy('nilai', 'DESC')
            ->get();

        $rekomendasi = Rekomendasi::where('user_id', Auth::id())
            ->orderBy('ranking', 'ASC')
            ->get();

        return view('pages.rekomendasi.index', compact('perankingan', 'rekomendasi'));
    }

    /**
     * Halaman form input user (pilih preferensi per kriteria)
     * User memilih 1 himpunan fuzzy per kriteria (dropdown/radio)
     */
    public function create()
    {
        $kriteria = Kriteria::with(['subKriteria' => function ($q) {
            $q->orderBy('kode', 'ASC');
        }])->orderBy('kode', 'ASC')->get();

        $rekomendasi = Rekomendasi::where('user_id', Auth::id())
            ->orderBy('ranking', 'ASC')
            ->get();

        return view('pages.rekomendasi.create', compact('kriteria', 'rekomendasi'));
    }

    // =========================================================
    //  PROSES UTAMA — FUZZY MAMDANI
    // =========================================================

    /**
     * Menerima input user, jalankan Fuzzy Mamdani, simpan hasil.
     *
     * Input form: { kriteria_id => nilai_crisp (0–10), ... }
     *   Contoh: { "uuid-harga" => 3, "uuid-isi" => 7, ... }
     */
    public function cari(Request $request)
    {
        // ── TAHAP 1: Validasi & Kalkulasi Fuzzy (di luar transaction) ──────
        // Kalkulasi tidak ubah database, aman dilakukan sebelum transaction
        try {
            $kriteria = Kriteria::orderBy('kode', 'ASC')->get();
            $rules    = [];
            foreach ($kriteria as $k) {
                $rules[$k->id] = 'required|numeric|min:0|max:10';
            }
            $this->validate($request, $rules, [
                'required' => 'Semua kriteria harus diisi.',
                'numeric'  => 'Nilai harus berupa angka.',
                'min'      => 'Nilai minimal adalah 0.',
                'max'      => 'Nilai maksimal adalah 10.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        }

        // Ambil input valid
        $kriteriaValid = Kriteria::pluck('id')->toArray();
        $inputUser     = [];
        foreach ($kriteriaValid as $kid) {
            if ($request->has($kid)) {
                $inputUser[$kid] = (float) $request->input($kid);
            }
        }

        // Jalankan fuzzy engine
        $membership   = $this->fuzzifikasi($inputUser);
        $fireStrength = $this->inferensi($membership);
        $skorAkhir    = $this->defuzzifikasi($fireStrength);

        if (empty($skorAkhir) || max($skorAkhir) == 0) {
            session()->flash('warning', 'Tidak ada suplemen yang cocok. Coba sesuaikan nilai preferensi.');
            return redirect()->route('rekomendasi.create');
        }

        // ── TAHAP 2: Simpan hasil ke database ─────────────────────────────
        // TRUNCATE tidak bisa dalam transaction di MySQL → lakukan di luar
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Perankingan::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $th) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            session()->flash('error', 'Gagal membersihkan data lama: ' . $th->getMessage());
            return back();
        }

        // Simpan perankingan & rekomendasi dalam transaction
        DB::beginTransaction();
        try {
            // Simpan perankingan global
            foreach ($skorAkhir as $suplemenId => $skor) {
                Perankingan::create([
                    'alternatif_id' => $suplemenId,
                    'nilai'         => $skor,
                ]);
            }

            // Hapus rekomendasi lama user ini lalu simpan yang baru
            Rekomendasi::where('user_id', Auth::id())->delete();

            $ranking = 1;
            foreach ($skorAkhir as $suplemenId => $skor) {
                if ($skor > 0) {
                    $suplemen = Alternatif::find($suplemenId);
                    Rekomendasi::create([
                        'user_id'       => Auth::id(),
                        'alternatif_id' => $suplemenId,
                        'nama'          => $suplemen ? $suplemen->nama : '-',
                        'skor'          => $skor,
                        'ranking'       => $ranking,
                    ]);
                    $ranking++;
                }
            }

            DB::commit();
            session()->flash('success', 'Rekomendasi suplemen berhasil ditemukan!');
            return redirect()->route('rekomendasi.index');

        } catch (\Throwable $th) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan: ' . $th->getMessage());
            return back();
        }
    }

    // =========================================================
    //  FUZZY ENGINE — PRIVATE METHODS
    // =========================================================

    /**
     * STEP 1 · FUZZIFIKASI
     *
     * Menggunakan fungsi keanggotaan SEGITIGA (trimf):
     *
     *         1  ┤        /\
     *            ┤       /  \
     *         0  ┤──────/────\──────
     *             bawah tengah atas
     *
     * μ(x) = 0                           jika x ≤ bawah atau x ≥ atas
     * μ(x) = (x - bawah)/(tengah-bawah)  jika bawah < x ≤ tengah
     * μ(x) = (atas - x)/(atas - tengah)  jika tengah < x < atas
     *
     * @param  array  $inputUser  [ kriteriaId => nilaiCrisp ]
     * @return array              [ subkriteriaId => derajat ]
     */
    private function fuzzifikasi(array $inputUser): array
    {
        $membership = [];

        foreach ($inputUser as $kriteriaId => $nilaiCrisp) {
            $nilaiCrisp  = (float) $nilaiCrisp;
            $subkriteria = SubKriteria::where('kriteria_id', $kriteriaId)
                ->orderBy('kode', 'ASC')
                ->get();

            foreach ($subkriteria as $sub) {
                $a = (float) $sub->batas_bawah;
                $b = (float) $sub->batas_tengah;
                $c = (float) $sub->batas_atas;

                $derajat = $this->trimf($nilaiCrisp, $a, $b, $c);
                $membership[$sub->id] = round($derajat, 6);
            }
        }

        return $membership;
    }

    /**
     * Fungsi keanggotaan segitiga (Triangular Membership Function)
     */
    private function trimf(float $x, float $a, float $b, float $c): float
    {
        // Kasus khusus: ketiga titik sama persis
        if ($a == $b && $b == $c) return ($x == $a) ? 1.0 : 0.0;

        // Kasus khusus: titik puncak = batas bawah (trapesium kiri)
        // Contoh: Murah (0, 0, 4) → μ = 1 saat x = 0
        if ($a == $b) {
            if ($x <= $a) return 1.0;
            if ($x >= $c) return 0.0;
            return ($c - $b) > 0 ? ($c - $x) / ($c - $b) : 0.0;
        }

        // Kasus khusus: titik puncak = batas atas (trapesium kanan)
        // Contoh: Mahal (6, 10, 10) → μ = 1 saat x = 10
        if ($b == $c) {
            if ($x >= $c) return 1.0;
            if ($x <= $a) return 0.0;
            return ($b - $a) > 0 ? ($x - $a) / ($b - $a) : 0.0;
        }

        // Kasus normal: segitiga standar
        // Perbedaan dari versi lama: gunakan < bukan <= untuk batas atas
        // agar nilai tepat di c tetap menghasilkan 0 (tidak error)
        if ($x < $a || $x > $c) return 0.0;
        if ($x == $b)            return 1.0;

        if ($x < $b) {
            return ($b - $a) > 0 ? ($x - $a) / ($b - $a) : 1.0;
        }

        return ($c - $b) > 0 ? ($c - $x) / ($c - $b) : 1.0;
    }

    /**
     * STEP 2 · INFERENSI (Mamdani — operator AND = MIN)
     *
     * Tiap rule dievaluasi:
     *   fire_strength = MIN(μ(kondisi_1), μ(kondisi_2), ...) × bobot_rule
     *
     * Untuk suplemen yang memiliki lebih dari 1 rule, diambil nilai MAX
     * (metode MAX untuk agregasi).
     *
     * @param  array  $membership  [ subkriteriaId => derajat ]
     * @return array               [ suplemenId => maxFireStrength ]
     */
    private function inferensi(array $membership): array
    {
        $fireStrength = [];

        $rules = FuzzyRule::all();

        foreach ($rules as $rule) {
            $kondisiIds = $rule->kondisi; // array of subkriteria_id

            // Ambil derajat keanggotaan tiap kondisi dalam rule ini
            // PENTING: skip kondisi yang subkriteriaId-nya tidak ada di $membership
            // (terjadi jika kriteria tidak diinput user atau UUID sudah tidak valid)
            $derajatKondisi = [];
            foreach ($kondisiIds as $subId) {
                if (array_key_exists($subId, $membership)) {
                    // Kondisi ada di membership → pakai nilainya
                    $derajatKondisi[] = $membership[$subId];
                }
                // Kondisi tidak ada → skip (tidak ikut MIN)
                // Ini mencegah UUID yang tidak dikenal memaksa fire = 0
            }

            // Jika tidak ada kondisi yang valid → skip rule ini
            if (empty($derajatKondisi)) continue;

            // Operator AND → MIN
            $fire = min($derajatKondisi);

            // Terapkan bobot rule
            $fire = $fire * (float) $rule->bobot_rule;

            // Agregasi: MAX per suplemen
            $sid = $rule->suplemen_id;
            if (!isset($fireStrength[$sid]) || $fire > $fireStrength[$sid]) {
                $fireStrength[$sid] = round($fire, 6);
            }
        }

        return $fireStrength;
    }

    /**
     * STEP 3 · DEFUZZIFIKASI (Weighted Average / Mean of Maximum)
     *
     * Normalisasi skor akhir agar berada di rentang 0–1:
     *   skorNormalisasi = fireStrength / maxFireStrength
     *
     * @param  array  $fireStrength  [ suplemenId => maxFireStrength ]
     * @return array                 [ suplemenId => skorNormalisasi ]
     */
    private function defuzzifikasi(array $fireStrength): array
    {
        if (empty($fireStrength)) return [];

        $maxVal = max($fireStrength);
        if ($maxVal == 0) return array_fill_keys(array_keys($fireStrength), 0);

        $skorNormalisasi = [];
        foreach ($fireStrength as $suplemenId => $val) {
            $skorNormalisasi[$suplemenId] = round($val / $maxVal, 6);
        }

        // Urutkan DESC berdasarkan skor
        arsort($skorNormalisasi);

        return $skorNormalisasi;
    }



    // =========================================================
    //  HALAMAN HASIL & CETAK
    // =========================================================

    public function store()
    {
        return $this->index();
    }

    public function cetak()
    {
        $perankingan = Perankingan::with('alternatif')
            ->orderBy('nilai', 'DESC')
            ->get();

        $rekomendasi = Rekomendasi::where('user_id', Auth::id())
            ->orderBy('ranking', 'ASC')
            ->get();

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cetak.rekomendasi', compact('perankingan', 'rekomendasi'))
                ->setPaper('A4', 'portrait');
            return $pdf->stream('rekomendasi-suplemen.pdf');
        } catch (\Throwable $th) {
            return back()->with('error', 'Fitur cetak PDF belum tersedia: ' . $th->getMessage());
        }
    }

    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}

    /**
     * DEBUG: Cek hasil tiap langkah fuzzy tanpa menyimpan ke DB
     * Akses: GET /rekomendasi/debug?{kriteria_id}={nilai}&...
     * Hapus route ini setelah selesai debugging!
     */
    public function debug(Request $request)
    {
        $kriteriaAll   = Kriteria::with('subKriteria')->orderBy('kode')->get();
        $kriteriaValid = $kriteriaAll->pluck('id')->toArray();

        // Gunakan nilai default 5 jika tidak ada input
        $inputUser = [];
        foreach ($kriteriaValid as $kid) {
            $inputUser[$kid] = (float) ($request->input($kid, 5));
        }

        $membership   = $this->fuzzifikasi($inputUser);
        $fireStrength = $this->inferensi($membership);
        $skorAkhir    = $this->defuzzifikasi($fireStrength);

        // Susun output yang mudah dibaca
        $out = [];

        // Input
        $out['INPUT'] = [];
        foreach ($kriteriaAll as $k) {
            $out['INPUT'][$k->nama] = $inputUser[$k->id] ?? 'N/A';
        }

        // Fuzzifikasi per kriteria
        $out['FUZZIFIKASI'] = [];
        foreach ($kriteriaAll as $k) {
            $out['FUZZIFIKASI'][$k->nama] = [];
            foreach ($k->subKriteria as $sub) {
                $mu = $membership[$sub->id] ?? 0;
                $out['FUZZIFIKASI'][$k->nama][$sub->nama] = [
                    'batas' => "({$sub->batas_bawah}, {$sub->batas_tengah}, {$sub->batas_atas})",
                    'mu'    => round($mu, 6),
                    'aktif' => $mu > 0 ? 'YA' : 'tidak',
                ];
            }
        }

        // Inferensi per rule
        $out['INFERENSI'] = [];
        $rules = FuzzyRule::with('suplemen')->get();
        foreach ($rules as $rule) {
            $derajatKondisi = [];
            $kondisiDetail  = [];
            foreach ($rule->kondisi as $subId) {
                $sub = \App\Models\SubKriteria::with('kriteria')->find($subId);
                if (array_key_exists($subId, $membership)) {
                    $mu = $membership[$subId];
                    $derajatKondisi[] = $mu;
                    $kondisiDetail[] = ($sub ? $sub->kriteria->nama . '=' . $sub->nama : $subId) . ' (μ=' . round($mu, 4) . ')';
                } else {
                    $kondisiDetail[] = ($sub ? $sub->kriteria->nama . '=' . $sub->nama : $subId) . ' [SKIP - tidak di input]';
                }
            }
            $fire = !empty($derajatKondisi) ? min($derajatKondisi) : 0;
            $fire = $fire * (float) $rule->bobot_rule;

            $out['INFERENSI'][$rule->nama_rule] = [
                'suplemen'  => $rule->suplemen ? $rule->suplemen->nama : '?',
                'kondisi'   => $kondisiDetail,
                'fire'      => round($fire, 6),
                'aktif'     => $fire > 0 ? 'AKTIF ✓' : 'tidak aktif',
            ];
        }

        // Hasil akhir
        $out['SKOR_AKHIR'] = [];
        foreach ($skorAkhir as $supId => $skor) {
            $sup = \App\Models\Alternatif::find($supId);
            $out['SKOR_AKHIR'][$sup ? $sup->nama : $supId] = round($skor, 6);
        }

        $out['KESIMPULAN'] = empty($skorAkhir) || max(array_values($skorAkhir) ?: [0]) == 0
            ? 'TIDAK ADA REKOMENDASI — semua fire strength = 0'
            : 'REKOMENDASI DITEMUKAN: ' . array_key_first($out['SKOR_AKHIR']);

        return response()->json($out, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}