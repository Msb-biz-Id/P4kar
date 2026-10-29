<?php

namespace App\Http\Controllers;

use App\Models\Penyakit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenyakitController extends Controller
{
    public function index()
    {
        $penyakit = Penyakit::orderBy('kode_penyakit', 'asc')->paginate(10);
        return view('admin.penyakit.index', compact('penyakit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_penyakit' => 'required|string|max:10|unique:penyakit,kode_penyakit',
            'nama_penyakit' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['kode_penyakit'] = strtoupper($validated['kode_penyakit']);

        Penyakit::create($validated);

        return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil ditambahkan!');
    }

    public function update(Request $request, Penyakit $penyakit)
    {
        $validated = $request->validate([
            'kode_penyakit' => ['required', 'string', 'max:10', Rule::unique('penyakit')->ignore($penyakit->id)],
            'nama_penyakit' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['kode_penyakit'] = strtoupper($validated['kode_penyakit']);

        $penyakit->update($validated);

        return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil diperbarui!');
    }

    public function destroy(Penyakit $penyakit)
    {
        $penyakit->delete();
        return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil dihapus!');
    }
}
