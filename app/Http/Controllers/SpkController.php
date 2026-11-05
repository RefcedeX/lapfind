<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\SpkResult;
use App\Services\AhpService;
use App\Services\TopsisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpkController extends Controller
{
    public function __construct(
        protected AhpService $ahpService,
        protected TopsisService $topsisService
    ) {}

    /**
     * Langkah 1: Form pemilihan profil kebutuhan atau kustom matriks perbandingan berpasangan AHP.
     */
    public function index(Request $request): View
    {
        $criteria = Criterion::orderBy('code')->get();
        $criteriaKeys = $criteria->pluck('code')->toArray();

        // Default profil: umum
        $activeProfile = $request->input('profil', 'umum');

        // Dapatkan pasangan default berdasarkan profil
        $defaultMatrix = $this->ahpService->getProfileDefaultComparisons($activeProfile, $criteriaKeys);

        return view('spk.step1_criteria', compact('criteria', 'criteriaKeys', 'activeProfile', 'defaultMatrix'));
    }

    /**
     * Proses Langkah 1: Hitung matriks AHP & konsistensi, simpan di session.
     */
    public function processAhp(Request $request): RedirectResponse
    {
        $criteria = Criterion::orderBy('code')->get();
        $criteriaKeys = $criteria->pluck('code')->toArray();
        $profile = $request->input('profile', 'kustom');

        // Ambil data perbandingan berpasangan
        $rawComparisons = $request->input('comparisons', []);

        // Bangun dan hitung AHP
        $ahpResult = $this->ahpService->calculate($criteriaKeys, $rawComparisons);

        // Simpan snapshot di session
        session([
            'spk_profile' => $profile,
            'spk_ahp' => $ahpResult,
            'spk_criteria' => $criteria->keyBy('code')->toArray(),
        ]);

        return redirect()->route('spk.weights');
    }

    /**
     * Langkah 2: Tinjauan Bobot Prioritas Kriteria (AHP) dan Indikator Uji Konsistensi Saaty.
     */
    public function showWeights(): View|RedirectResponse
    {
        $ahpResult = session('spk_ahp');
        $profile = session('spk_profile', 'Kustom');
        $criteria = session('spk_criteria');

        if (! $ahpResult || ! $criteria) {
            return redirect()->route('spk.index')->with('error', 'Silakan tentukan perbandingan kriteria terlebih dahulu.');
        }

        return view('spk.step2_weights', compact('ahpResult', 'profile', 'criteria'));
    }

    /**
     * Proses Langkah 3: Eksekusi Perankingan TOPSIS menggunakan bobot AHP.
     */
    public function processTopsis(Request $request): RedirectResponse
    {
        $ahpResult = session('spk_ahp');
        $profile = session('spk_profile', 'Kustom');

        if (! $ahpResult) {
            return redirect()->route('spk.index')->with('error', 'Sesi pembobotan kriteria kedaluwarsa. Silakan mulai kembali.');
        }

        $weights = $ahpResult['weights'];

        // Ambil seluruh laptop dan kriteria dari database
        $criteria = Criterion::orderBy('code')->get();
        $alternatives = Alternative::with('values.criterion')->orderBy('name')->get();

        if ($alternatives->isEmpty()) {
            return redirect()->route('spk.index')->with('error', 'Belum ada alternatif laptop dalam database.');
        }

        // Susun matriks keputusan X dan mapping tipe kriteria
        $criteriaTypes = [];
        foreach ($criteria as $crit) {
            $criteriaTypes[$crit->code] = $crit->type;
        }

        $decisionMatrix = [];
        $alternativeNames = [];

        foreach ($alternatives as $alt) {
            $alternativeNames[$alt->id] = $alt->name;
            $decisionMatrix[$alt->id] = [];

            $valMap = $alt->values->keyBy(fn ($v) => $v->criterion->code);

            foreach ($criteria as $crit) {
                // Jika nilai belum diisi pada alternatif, default 0 atau harga untuk C1
                if (isset($valMap[$crit->code])) {
                    $decisionMatrix[$alt->id][$crit->code] = (float) $valMap[$crit->code]->value;
                } elseif ($crit->code === 'C1') {
                    $decisionMatrix[$alt->id][$crit->code] = (float) $alt->price;
                } else {
                    $decisionMatrix[$alt->id][$crit->code] = 0.0;
                }
            }
        }

        // Eksekusi kalkulasi TOPSIS
        $topsisResult = $this->topsisService->calculate(
            $decisionMatrix,
            $criteriaTypes,
            $weights,
            $alternativeNames
        );

        // Siapkan struktur snapshot lengkap langkah antara
        $stepsSnapshot = [
            'ahp' => $ahpResult,
            'topsis' => [
                'column_divisors' => $topsisResult['column_divisors'],
                'normalized_matrix' => $topsisResult['normalized_matrix'],
                'weighted_matrix' => $topsisResult['weighted_matrix'],
                'ideal_positive' => $topsisResult['ideal_positive'],
                'ideal_negative' => $topsisResult['ideal_negative'],
                'distance_positive' => $topsisResult['distance_positive'],
                'distance_negative' => $topsisResult['distance_negative'],
            ],
            'criteria' => $criteria->toArray(),
        ];

        // Simpan ke database jika pengguna telah login
        $savedResultId = null;
        if (auth()->check()) {
            $spkResult = SpkResult::create([
                'user_id' => auth()->id(),
                'title' => 'Rekomendasi Profil '.ucfirst($profile),
                'weights_snapshot' => $weights,
                'ranking_snapshot' => $topsisResult['rankings'],
                'steps_snapshot' => $stepsSnapshot,
            ]);
            $savedResultId = $spkResult->id;
        }

        // Simpan di session untuk pengguna tamu atau tampilan instan
        session([
            'spk_topsis' => $topsisResult,
            'spk_steps' => $stepsSnapshot,
            'spk_saved_id' => $savedResultId,
        ]);

        return redirect()->route('spk.result', $savedResultId ? ['spkResult' => $savedResultId] : []);
    }

    /**
     * Langkah 3: Tampilan Hasil Akhir Pemeringkatan TOPSIS + Transparansi Langkah Antara.
     */
    public function showResult(?SpkResult $spkResult = null): View|RedirectResponse
    {
        $criteria = Criterion::orderBy('code')->get();

        // Jika mengakses hasil tersimpan di DB
        if ($spkResult && $spkResult->exists) {
            $rankings = $spkResult->ranking_snapshot;
            $weights = $spkResult->weights_snapshot;
            $steps = $spkResult->steps_snapshot;
            $title = $spkResult->title;
            $createdAt = $spkResult->created_at;
            $resultId = $spkResult->id;

            // Load data laptop detail untuk gambar & spesifikasi
            $altIds = collect($rankings)->pluck('alternative_id')->toArray();
            $laptops = Alternative::whereIn('id', $altIds)->get()->keyBy('id');

            return view('spk.step3_result', compact(
                'rankings',
                'weights',
                'steps',
                'title',
                'createdAt',
                'resultId',
                'criteria',
                'laptops'
            ));
        }

        // Jika mengakses dari sesi aktif
        $topsisResult = session('spk_topsis');
        $steps = session('spk_steps');
        $ahpResult = session('spk_ahp');
        $profile = session('spk_profile', 'Kustom');
        $resultId = session('spk_saved_id');

        if (! $topsisResult || ! $steps) {
            return redirect()->route('spk.index')->with('error', 'Belum ada hasil kalkulasi SPK. Silakan jalankan evaluasi.');
        }

        $rankings = $topsisResult['rankings'];
        $weights = $ahpResult['weights'] ?? [];
        $title = 'Rekomendasi Profil '.ucfirst($profile);
        $createdAt = now();

        $altIds = collect($rankings)->pluck('alternative_id')->toArray();
        $laptops = Alternative::whereIn('id', $altIds)->get()->keyBy('id');

        return view('spk.step3_result', compact(
            'rankings',
            'weights',
            'steps',
            'title',
            'createdAt',
            'resultId',
            'criteria',
            'laptops'
        ));
    }

    /**
     * Ekspor Laporan Evaluasi SPK ke format PDF.
     */
    public function exportPdf(?SpkResult $spkResult = null): Response
    {
        $criteria = Criterion::orderBy('code')->get();

        if ($spkResult && $spkResult->exists) {
            $rankings = $spkResult->ranking_snapshot;
            $weights = $spkResult->weights_snapshot;
            $steps = $spkResult->steps_snapshot;
            $title = $spkResult->title;
            $createdAt = $spkResult->created_at;
            $resultId = $spkResult->id;
            $user = $spkResult->user?->name ?? 'Mahasiswa Pengguna';
        } else {
            $topsisResult = session('spk_topsis');
            $steps = session('spk_steps');
            $ahpResult = session('spk_ahp');
            $profile = session('spk_profile', 'Kustom');
            $resultId = session('spk_saved_id');

            if (! $topsisResult || ! $steps) {
                return redirect()->route('spk.index')->with('error', 'Belum ada hasil kalkulasi SPK untuk diekspor.');
            }

            $rankings = $topsisResult['rankings'];
            $weights = $ahpResult['weights'] ?? [];
            $title = 'Rekomendasi Profil '.ucfirst($profile);
            $createdAt = now();
            $user = auth()->user()?->name ?? 'Tamu (Guest)';
        }

        $altIds = collect($rankings)->pluck('alternative_id')->toArray();
        $laptops = Alternative::whereIn('id', $altIds)->get()->keyBy('id');

        $pdf = Pdf::loadView('spk.pdf_report', compact(
            'rankings',
            'weights',
            'steps',
            'title',
            'createdAt',
            'resultId',
            'criteria',
            'laptops',
            'user'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_SPK_Laptop_'.date('Ymd_His').'.pdf';

        return $pdf->download($filename);
    }
}
