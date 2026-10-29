<?php

namespace App\Http\Controllers;

use App\Models\Aturan;
use App\Models\Gejala;
use App\Models\Penyakit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AturanController extends Controller
{
    public function index()
    {
        $aturan = Aturan::with(['penyakit', 'gejalas'])->orderBy('kode_aturan', 'asc')->paginate(15);
        $penyakit = Penyakit::orderBy('kode_penyakit', 'asc')->get();
        $gejala = Gejala::orderBy('id', 'asc')->get();

        return view('admin.aturan.index', compact('aturan', 'penyakit', 'gejala'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_aturan' => 'required|string|max:20|unique:aturan,kode_aturan',
            'nama_aturan' => 'required|string|max:255',
            'penyakit_id' => 'required|exists:penyakit,id',
            'gejala_ids' => 'required|array|min:1',
            'gejala_ids.*' => 'exists:gejala,id',
            'operator' => 'nullable|string|in:AND,OR',
            'keterangan' => 'nullable|string',
        ]);

        $aturan = Aturan::create([
            'kode_aturan' => strtoupper($validated['kode_aturan']),
            'nama_aturan' => $validated['nama_aturan'],
            'penyakit_id' => $validated['penyakit_id'],
            'keterangan' => $validated['keterangan'] ?? null,
            'operator' => strtoupper($validated['operator'] ?? 'AND'),
            'is_active' => true,
        ]);

        $aturan->gejalas()->sync($validated['gejala_ids']);

        return redirect()->route('admin.aturan.index')->with('success', 'Aturan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Aturan $aturan)
    {
        $validated = $request->validate([
            'kode_aturan' => ['required', 'string', 'max:20', Rule::unique('aturan')->ignore($aturan->id)],
            'nama_aturan' => 'required|string|max:255',
            'penyakit_id' => 'required|exists:penyakit,id',
            'gejala_ids' => 'required|array|min:1',
            'gejala_ids.*' => 'exists:gejala,id',
            'operator' => 'nullable|string|in:AND,OR',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $aturan->update([
            'kode_aturan' => strtoupper($validated['kode_aturan']),
            'nama_aturan' => $validated['nama_aturan'],
            'penyakit_id' => $validated['penyakit_id'],
            'keterangan' => $validated['keterangan'] ?? null,
            'operator' => strtoupper($validated['operator'] ?? 'AND'),
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        $aturan->gejalas()->sync($validated['gejala_ids']);

        return redirect()->route('admin.aturan.index')->with('success', 'Aturan berhasil diperbarui!');
    }

    public function destroy(Aturan $aturan)
    {
        $aturan->gejalas()->detach();
        $aturan->delete();

        return redirect()->route('admin.aturan.index')->with('success', 'Aturan berhasil dihapus!');
    }
}
