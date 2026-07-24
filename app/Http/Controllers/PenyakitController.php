<?php

namespace App\Http\Controllers;

use App\Models\Penyakit;
use Illuminate\Http\Request;

class PenyakitController extends Controller
{
    public function index()
    {
        $penyakit = Penyakit::latest()->paginate(10);
        return view('admin.penyakit.index', compact('penyakit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penyakit' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Penyakit::create($request->only('nama_penyakit', 'keterangan'));

        return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil ditambahkan!');
    }

    public function update(Request $request, Penyakit $penyakit)
    {
        $request->validate([
            'nama_penyakit' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $penyakit->update($request->only('nama_penyakit', 'keterangan'));

        return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil diperbarui!');
    }

    public function destroy(Penyakit $penyakit)
    {
        $penyakit->delete();
        return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil dihapus!');
    }
}
