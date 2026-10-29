<?php

namespace App\Services;

use App\Models\Aturan;
use App\Models\Gejala;
use App\Models\Penyakit;

class ForwardChainingService
{
    /**
     * Jalankan mesin inferensi Forward Chaining berdasarkan fakta gejala input.
     *
     * @param array $gejalaIds Daftar ID gejala yang dipilih pengguna
     * @return array Hasil inferensi, status kecocokan, aturan terpilih, dan trace log
     */
    public function diagnose(array $gejalaIds): array
    {
        // 1. Inisialisasi Working Memory (Memori Kerja)
        $faktaGejala = Gejala::whereIn('id', $gejalaIds)->get()->keyBy('id');
        $faktaIds = $faktaGejala->keys()->toArray();

        // 2. Ambil seluruh basis pengetahuan (Aturan aktif beserta premis dan penyakit)
        $aturans = Aturan::with(['penyakit', 'gejalas'])
            ->where('is_active', true)
            ->get();

        $evaluasiAturan = [];
        $exactMatches = [];

        // 3. Pattern Matching: Evaluasi premis setiap aturan terhadap working memory
        foreach ($aturans as $aturan) {
            $premisGejala = $aturan->gejalas;
            $premisIds = $premisGejala->pluck('id')->toArray();
            $totalPremis = count($premisIds);

            // Operator logika premis: AND (semua premis harus cocok) atau OR (cukup satu premis)
            $operator = strtoupper($aturan->operator ?? 'AND');
            $isOperatorOr = ($operator === 'OR');

            // Gejala yang cocok (irisan antara fakta dan premis aturan)
            $cocokIds = array_values(array_intersect($faktaIds, $premisIds));
            $cocokCount = count($cocokIds);

            // Gejala premis yang belum terpenuhi
            $kurangIds = array_values(array_diff($premisIds, $faktaIds));

            if ($isOperatorOr) {
                $isExactMatch = ($totalPremis > 0 && $cocokCount > 0);
                $matchRatio = $isExactMatch ? 1 : 0;
            } else {
                $matchRatio = $totalPremis > 0 ? ($cocokCount / $totalPremis) : 0;
                $isExactMatch = ($totalPremis > 0 && $cocokCount === $totalPremis);
            }

            $gejalaCocokList = $premisGejala->whereIn('id', $cocokIds)->map(fn($g) => [
                'id' => $g->id,
                'kode' => $g->kode_gejala,
                'nama' => $g->nama_gejala,
            ])->values()->toArray();

            $gejalaKurangList = $premisGejala->whereIn('id', $kurangIds)->map(fn($g) => [
                'id' => $g->id,
                'kode' => $g->kode_gejala,
                'nama' => $g->nama_gejala,
            ])->values()->toArray();

            $evalItem = [
                'aturan_id' => $aturan->id,
                'kode_aturan' => $aturan->kode_aturan,
                'nama_aturan' => $aturan->nama_aturan,
                'penyakit_id' => $aturan->penyakit_id,
                'nama_penyakit' => $aturan->penyakit?->nama_penyakit ?? 'Tidak diketahui',
                'kode_penyakit' => $aturan->penyakit?->kode_penyakit ?? '-',
                'operator' => $operator,
                'total_premis' => $totalPremis,
                'cocok_count' => $cocokCount,
                'kurang_count' => count($kurangIds),
                'match_percentage' => round($matchRatio * 100, 2),
                'is_exact_match' => $isExactMatch,
                'gejala_cocok' => $gejalaCocokList,
                'gejala_kurang' => $gejalaKurangList,
            ];

            $evaluasiAturan[] = $evalItem;

            if ($isExactMatch) {
                $exactMatches[] = $evalItem;
            }
        }

        // Urutkan evaluasi dari persentase kecocokan tertinggi ke terendah
        usort($evaluasiAturan, function ($a, $b) {
            if ($b['match_percentage'] === $a['match_percentage']) {
                return $b['total_premis'] <=> $a['total_premis'];
            }
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        // 4. Conflict Resolution & Penentuan Kesimpulan Akhir
        $statusKesimpulan = 'tidak_cocok';
        $aturanTerpilih = null;
        $penyakitTerpilih = null;
        $solusiText = null;

        if (count($exactMatches) > 0) {
            // Ada aturan yang terpenuhi 100%
            $penyakitIds = array_unique(array_column($exactMatches, 'penyakit_id'));

            // Urutkan dari jumlah premis terpenuhi terbanyak (aturan OR dinilai dari
            // cabang yang cocok agar aturan tanda bahaya tidak menyingkirkan diagnosa
            // utama; pada aturan AND ini setara dengan total premis karena cocok penuh).
            $urutExactMatch = fn($a, $b) => $b['cocok_count'] <=> $a['cocok_count'];

            if (count($penyakitIds) === 1) {
                // Semua aturan terpicu mengarah pada penyakit yang sama (Cocok Penuh Tunggal)
                $statusKesimpulan = 'cocok_penuh';
                // Pilih aturan dengan premis terbanyak (paling spesifik)
                usort($exactMatches, $urutExactMatch);
                $aturanTerpilih = $exactMatches[0];
            } else {
                // Lebih dari satu penyakit terpicu (Multi-Diagnosis)
                $statusKesimpulan = 'multi_match';
                usort($exactMatches, $urutExactMatch);
                $aturanTerpilih = $exactMatches[0];
            }

            $penyakitTerpilih = Penyakit::with('solusi')->find($aturanTerpilih['penyakit_id']);
            $solusiText = $penyakitTerpilih?->solusi?->pluck('deskripsi_solusi')?->implode("\n\n")
                ?: ($penyakitTerpilih?->keterangan ?? null);

        } else {
            // Tidak ada kecocokan 100%, evaluasi kemungkinan parsial tertinggi
            $topCandidate = $evaluasiAturan[0] ?? null;

            if ($topCandidate && $topCandidate['match_percentage'] >= 50.0 && $topCandidate['cocok_count'] > 0) {
                $statusKesimpulan = 'parsial';
                $aturanTerpilih = $topCandidate;
                $penyakitTerpilih = Penyakit::with('solusi')->find($aturanTerpilih['penyakit_id']);
                $solusiText = $penyakitTerpilih?->solusi?->pluck('deskripsi_solusi')?->implode("\n\n")
                    ?: ($penyakitTerpilih?->keterangan ?? null);
            } else {
                $statusKesimpulan = 'tidak_cocok';
                $aturanTerpilih = null;
                $penyakitTerpilih = null;
                $solusiText = 'Gejala klinis yang Anda rasakan belum memenuhi ambang batas aturan diagnosis spesifik untuk 3 penyakit yang didukung. Sangat disarankan untuk segera berkonsultasi langsung dengan dokter spesialis kandungan atau bidan untuk pemeriksaan fisik lebih komprehensif.';
            }
        }

        // Siapkan format backward-compatible probabilitas array jika masih dibutuhkan komponen lama
        $probabilitasLegacy = [];
        foreach ($evaluasiAturan as $item) {
            $probabilitasLegacy[] = [
                'nama_penyakit' => $item['nama_penyakit'],
                'probabilitas' => round($item['match_percentage'] / 100, 4),
            ];
        }

        // 5. Susun Trace Log
        $traceLog = [
            'fakta_input' => $faktaGejala->map(fn($g) => [
                'id' => $g->id,
                'kode' => $g->kode_gejala,
                'nama' => $g->nama_gejala,
            ])->values()->toArray(),
            'status_kesimpulan' => $statusKesimpulan,
            'aturan_terpilih' => $aturanTerpilih,
            'evaluasi_aturan' => $evaluasiAturan,
            'exact_matches' => $exactMatches,
        ];

        return [
            'penyakit_id' => $penyakitTerpilih?->id,
            'penyakit' => $penyakitTerpilih,
            'aturan_terpilih_id' => $aturanTerpilih['aturan_id'] ?? null,
            'aturan_terpilih' => $aturanTerpilih,
            'status_kesimpulan' => $statusKesimpulan,
            'solusi' => $solusiText,
            'detail_inferensi' => $traceLog,
            'probabilitas' => $probabilitasLegacy,
            'evaluasi_aturan' => $evaluasiAturan,
        ];
    }
}
