<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function index()
    {
        $hasil = Hasil::with('penyakit')->latest()->paginate(10);
        return view('admin.hasil.index', compact('hasil'));
    }

    public function show(Hasil $hasil)
    {
        $hasil->load('penyakit');
        return view('admin.hasil.show', compact('hasil'));
    }
}
