<?php

namespace App\Http\Controllers;

use App\Models\Relasi;
use App\Models\Penyakit;
use App\Models\Gejala;
use Illuminate\Http\Request;

class RelasiController extends Controller
{
    public function index()
    {
        $relasi = Relasi::with(['penyakit', 'gejala'])->latest()->paginate(15);
        $penyakit = Penyakit::all();
        $gejala = Gejala::all();
        return view('admin.relasi.index', compact('relasi', 'penyakit', 'gejala'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakit,id',
            'gejala_id' => 'required|exists:gejala,id',
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        Relasi::create($request->only('penyakit_id', 'gejala_id', 'bobot'));

        return redirect()->route('admin.relasi.index')->with('success', 'Relasi berhasil ditambahkan!');
    }

    public function update(Request $request, Relasi $relasi)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakit,id',
            'gejala_id' => 'required|exists:gejala,id',
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        $relasi->update($request->only('penyakit_id', 'gejala_id', 'bobot'));

        return redirect()->route('admin.relasi.index')->with('success', 'Relasi berhasil diperbarui!');
    }

    public function destroy(Relasi $relasi)
    {
        $relasi->delete();

        return redirect()->route('admin.relasi.index')->with('success', 'Relasi berhasil dihapus!');
    }
}
