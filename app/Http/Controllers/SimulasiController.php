<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use App\Models\Aturan;
use App\Models\Penyakit;
use App\Services\ForwardChainingService;
use Illuminate\Http\Request;

class SimulasiController extends Controller
{
    protected ForwardChainingService $inferenceService;

    public function __construct(ForwardChainingService $inferenceService)
    {
        $this->inferenceService = $inferenceService;
    }

    public function index()
    {
        $gejala = Gejala::orderBy('id', 'asc')->get();
        $aturan = Aturan::with(['penyakit', 'gejalas'])->orderBy('kode_aturan', 'asc')->get();
        $penyakit = Penyakit::orderBy('kode_penyakit', 'asc')->get();

        return view('admin.simulasi.index', compact('gejala', 'aturan', 'penyakit'));
    }

    public function proses(Request $request)
    {
        $validated = $request->validate([
            'gejala' => 'required|array|min:1',
            'gejala.*' => 'exists:gejala,id',
        ]);

        $inferenceResult = $this->inferenceService->diagnose($validated['gejala']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $inferenceResult,
            ]);
        }

        return redirect()->route('admin.simulasi.index')->with('simulasiResult', $inferenceResult);
    }
}
