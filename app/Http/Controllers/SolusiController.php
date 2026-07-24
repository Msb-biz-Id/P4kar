<?php

namespace App\Http\Controllers;

use App\Models\Solusi;
use App\Models\Penyakit;
use Illuminate\Http\Request;

class SolusiController extends Controller
{
    public function index()
    {
        $solusi = Solusi::with('penyakit')->latest()->paginate(15);
        $penyakit = \App\Models\Penyakit::all();
        return view('admin.solusi.index', compact('solusi', 'penyakit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakit,id',
            'judul_solusi' => 'required|string|max:255',
            'deskripsi_solusi' => 'required|string',
        ]);

        Solusi::create($request->only('penyakit_id', 'judul_solusi', 'deskripsi_solusi'));

        return redirect()->route('admin.solusi.index')->with('success', 'Solusi berhasil ditambahkan!');
    }

    public function update(Request $request, Solusi $solusi)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakit,id',
            'judul_solusi' => 'required|string|max:255',
            'deskripsi_solusi' => 'required|string',
        ]);

        $solusi->update($request->only('penyakit_id', 'judul_solusi', 'deskripsi_solusi'));

        return redirect()->route('admin.solusi.index')->with('success', 'Solusi berhasil diperbarui!');
    }

    public function destroy(Solusi $solusi)
    {
        $solusi->delete();
        return redirect()->route('admin.solusi.index')->with('success', 'Solusi berhasil dihapus!');
    }
}
