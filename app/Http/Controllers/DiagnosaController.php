<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use App\Models\Hasil;
use App\Services\ForwardChainingService;
use Illuminate\Http\Request;

class DiagnosaController extends Controller
{
    protected ForwardChainingService $inferenceService;

    public function __construct(ForwardChainingService $inferenceService)
    {
        $this->inferenceService = $inferenceService;
    }

    public function index()
    {
        return view('diagnosa.index');
    }

    public function konsultasi()
    {
        $gejala = Gejala::orderBy('id', 'asc')->get();
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

        $inferenceResult = $this->inferenceService->diagnose($validated['gejala']);

        $hasil = Hasil::create([
            'nama_pasien' => $validated['nama_pasien'],
            'alamat_pasien' => $validated['alamat_pasien'] ?? null,
            'penyakit_id' => $inferenceResult['penyakit_id'],
            'aturan_terpilih_id' => $inferenceResult['aturan_terpilih_id'],
            'status_kesimpulan' => $inferenceResult['status_kesimpulan'],
            'solusi' => $inferenceResult['solusi'],
            'detail_inferensi' => $inferenceResult['detail_inferensi'],
            'probabilitas' => $inferenceResult['probabilitas'],
        ]);

        return redirect()->route('diagnosa.hasil', $hasil->id);
    }

    public function hasil(int|string $id)
    {
        $hasil = Hasil::with(['penyakit.solusi', 'aturan.penyakit'])->findOrFail($id);
        $inferenceDetail = $hasil->detail_inferensi ?? [];
        $evaluasiAturan = $inferenceDetail['evaluasi_aturan'] ?? [];
        $faktaInput = $inferenceDetail['fakta_input'] ?? [];

        return view('diagnosa.hasil', compact('hasil', 'inferenceDetail', 'evaluasiAturan', 'faktaInput'));
    }
}
