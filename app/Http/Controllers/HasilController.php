<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function index()
    {
        $hasil = Hasil::with(['penyakit', 'aturan'])->latest()->paginate(10);
        return view('admin.hasil.index', compact('hasil'));
    }

    public function show(Hasil $hasil)
    {
        $hasil->load(['penyakit.solusi', 'aturan.penyakit']);
        return view('admin.hasil.show', compact('hasil'));
    }

    public function destroy(Hasil $hasil)
    {
        $namaPasien = $hasil->nama_pasien;
        $hasil->delete();

        return redirect()->route('admin.hasil.index')
            ->with('success', "Data riwayat diagnosa pasien '{$namaPasien}' berhasil dihapus.");
    }

    public function destroyAll()
    {
        $count = Hasil::count();
        Hasil::query()->delete();

        return redirect()->route('admin.hasil.index')
            ->with('success', "Seluruh data riwayat diagnosa ({$count} data) berhasil dihapus.");
    }
}
