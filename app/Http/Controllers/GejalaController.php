<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GejalaController extends Controller
{
    public function index()
    {
        $gejala = Gejala::orderBy('id', 'asc')->paginate(15);
        return view('admin.gejala.index', compact('gejala'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_gejala' => 'required|string|max:10|unique:gejala,kode_gejala',
            'nama_gejala' => 'required|string|max:255',
        ]);

        $validated['kode_gejala'] = strtoupper($validated['kode_gejala']);

        Gejala::create($validated);

        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil ditambahkan!');
    }

    public function update(Request $request, Gejala $gejala)
    {
        $validated = $request->validate([
            'kode_gejala' => ['required', 'string', 'max:10', Rule::unique('gejala')->ignore($gejala->id)],
            'nama_gejala' => 'required|string|max:255',
        ]);

        $validated['kode_gejala'] = strtoupper($validated['kode_gejala']);

        $gejala->update($validated);

        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil diperbarui!');
    }

    public function destroy(Gejala $gejala)
    {
        $gejala->delete();
        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil dihapus!');
    }
}
