<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use Illuminate\Http\Request;

class GejalaController extends Controller
{
    public function index()
    {
        $gejala = Gejala::latest()->paginate(10);
        return view('admin.gejala.index', compact('gejala'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_gejala' => 'required|string|max:255',
        ]);

        Gejala::create($request->only('nama_gejala'));

        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil ditambahkan!');
    }

    public function update(Request $request, Gejala $gejala)
    {
        $request->validate([
            'nama_gejala' => 'required|string|max:255',
        ]);

        $gejala->update($request->only('nama_gejala'));

        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil diperbarui!');
    }

    public function destroy(Gejala $gejala)
    {
        $gejala->delete();
        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil dihapus!');
    }
}
