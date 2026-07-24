<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\Hasil;
use Illuminate\Http\Request;

class DiagnosaController extends Controller
{
    public function index()
    {
        return view('diagnosa.index');
    }

    public function konsultasi()
    {
        $gejala = Gejala::all();
        return view('diagnosa.konsultasi', compact('gejala'));
    }

    public function proses(Request $request)
    {
        $validated = $request->validate([
            'nama_pasien' => 'required|string|max:255',
            'alamat_pasien' => 'nullable|string|max:255',
            'gejala' => 'required|array|min:1',
            'gejala.*' => 'exists:gejala,id',
        ]);

        $gejalaIds = $request->gejala;
        $penyakit = Penyakit::with(['relasis', 'solusi'])->get();

        // Naive Bayes calculation in log-space to prevent underflow
        $totalPenyakit = $penyakit->count();
        $hasilPerhitungan = [];
        foreach ($penyakit as $p) {
            // Log prior: log(P(Disease))
            $logPrior = log(1 / $totalPenyakit);

            // Log likelihood: log(P(Symptoms|Disease))
            $relasiMap = $p->relasis->pluck('bobot', 'gejala_id');
            $logLikelihood = 0;

            foreach ($gejalaIds as $gejalaId) {
                $bobot = $relasiMap->get($gejalaId, 0.01);
                if ($bobot <= 0) {
                    $logLikelihood = -INF;
                    break;
                }
                $logLikelihood += log($bobot);
            }

            $hasilPerhitungan[$p->id] = [
                'id' => $p->id,
                'nama_penyakit' => $p->nama_penyakit,
                'log_posterior' => $logLikelihood + $logPrior,
            ];
        }

        // Normalize probabilities using softmax (subtract max for numerical stability)
        $maxLog = max(array_column($hasilPerhitungan, 'log_posterior'));
        $expSum = 0;

        if ($maxLog === -INF) {
            foreach ($hasilPerhitungan as &$item) {
                $item['probabilitas'] = 1 / $totalPenyakit;
            }
            unset($item);
        } else {
            foreach ($hasilPerhitungan as &$item) {
                $item['probabilitas'] = exp($item['log_posterior'] - $maxLog);
                $expSum += $item['probabilitas'];
            }
            unset($item);

            foreach ($hasilPerhitungan as &$item) {
                $item['probabilitas'] = $expSum > 0 ? $item['probabilitas'] / $expSum : 0;
            }
            unset($item);
        }

        // Sort by probability (descending)
        usort($hasilPerhitungan, function ($a, $b) {
            return $b['probabilitas'] <=> $a['probabilitas'];
        });

        $topResult = $hasilPerhitungan[0] ?? null;
        
        // Ambang batas (threshold): 50%
        if ($topResult && $topResult['probabilitas'] >= 0.5) {
            $penyakitId = $topResult['id'];
            $p = $penyakit->firstWhere('id', $penyakitId);
            $solusiList = $p?->solusi?->pluck('deskripsi_solusi')?->implode("\n\n");
            $solusi = $solusiList ?: ($p?->keterangan ?? null);
        } else {
            $penyakitId = null;
            $solusi = 'Gejala yang Anda rasakan tidak cukup spesifik untuk mendiagnosa penyakit tertentu (semua probabilitas < 50%). Silakan konsultasikan lebih lanjut dengan dokter kandungan atau bidan.';
        }

        // Build probability breakdown for storage
        $probabilitas = [];
        foreach ($hasilPerhitungan as $item) {
            $probabilitas[] = [
                'nama_penyakit' => $item['nama_penyakit'],
                'probabilitas' => round($item['probabilitas'], 6),
            ];
        }

        $hasil = Hasil::create([
            'nama_pasien' => $validated['nama_pasien'],
            'alamat_pasien' => $validated['alamat_pasien'] ?? null,
            'penyakit_id' => $penyakitId,
            'solusi' => $solusi,
            'probabilitas' => $probabilitas,
        ]);

        return view('diagnosa.hasil', compact('hasil', 'hasilPerhitungan'));
    }

    public function hasil($id)
    {
        $hasil = Hasil::with('penyakit')->findOrFail($id);
        $hasilPerhitungan = $hasil->probabilitas ?? [];
        return view('diagnosa.hasil', compact('hasil', 'hasilPerhitungan'));
    }
}
