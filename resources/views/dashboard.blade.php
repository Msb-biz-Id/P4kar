@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <!-- Banner Sambutan Ringkas di Atas -->
    <div class="card bg-white border-0 shadow-sm mb-3" style="border-left: 4px solid #007bff !important;">
        <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-stethoscope text-primary mr-2"></i> Selamat Datang, {{ Auth::user()->name }} (Admin Sistem Pakar)!
                </h5>
                <small class="text-muted">
                    Sistem Pakar Diagnosa Penyakit Ibu Hamil menggunakan <strong>Metode Forward Chaining</strong>. Gunakan navigasi untuk mengelola master data atau menjalankan pengujian validasi sistem.
                </small>
            </div>
            <div class="mt-2 mt-md-0">
                <span class="badge badge-primary px-3 py-2 font-weight-bold">
                    <i class="fas fa-brain mr-1"></i> Forward Chaining Engine v2.0
                </span>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik Angka (5 Kotak Metrik Ringkas) -->
    <div class="row">
        <div class="col-lg col-md-4 col-6 mb-3">
            <div class="small-box bg-info mb-0 h-100">
                <div class="inner">
                    <h3>{{ \App\Models\Penyakit::count() }}</h3>
                    <p class="mb-1 font-weight-bold">Data Penyakit</p>
                </div>
                <div class="icon"><i class="fas fa-virus"></i></div>
                <a href="{{ route('admin.penyakit.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6 mb-3">
            <div class="small-box bg-success mb-0 h-100">
                <div class="inner">
                    <h3>{{ \App\Models\Gejala::count() }}</h3>
                    <p class="mb-1 font-weight-bold">Data Gejala</p>
                </div>
                <div class="icon"><i class="fas fa-stethoscope"></i></div>
                <a href="{{ route('admin.gejala.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6 mb-3">
            <div class="small-box bg-warning mb-0 h-100">
                <div class="inner">
                    <h3>{{ \App\Models\Aturan::count() }}</h3>
                    <p class="mb-1 font-weight-bold">Basis Aturan</p>
                </div>
                <div class="icon"><i class="fas fa-code-branch"></i></div>
                <a href="{{ route('admin.aturan.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg col-md-6 col-6 mb-3">
            <div class="small-box bg-secondary mb-0 h-100">
                <div class="inner">
                    <h3>{{ \App\Models\Solusi::count() }}</h3>
                    <p class="mb-1 font-weight-bold">Data Solusi</p>
                </div>
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <a href="{{ route('admin.solusi.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg col-md-6 col-12 mb-3">
            <div class="small-box bg-danger mb-0 h-100">
                <div class="inner">
                    <h3>{{ \App\Models\Hasil::count() }}</h3>
                    <p class="mb-1 font-weight-bold">Riwayat Diagnosa</p>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
                <a href="{{ route('admin.hasil.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- 2 KOLOM IDEAL: Penjelasan Metode (Kiri) & Uji Validasi (Kanan) -->
    <div class="row">
        <!-- Kolom Kiri: Penjelasan & Prinsip Kerja Metode Forward Chaining -->
        <div class="col-lg-6 mb-4">
            <div class="card card-primary card-outline shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold text-primary mb-0">
                        <i class="fas fa-project-diagram mr-2 text-primary"></i> Detail Metode Forward Chaining
                    </h5>
                    <span class="badge badge-light border text-primary font-weight-bold">
                        Data-Driven Reasoning
                    </span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small mb-3">
                            Metode <em>Forward Chaining</em> (penalaran maju) bekerja dari sekumpulan fakta awal yang diketahui (gejala klinis ibu hamil), kemudian mencocokkannya dengan anteseden (kondisi <em>IF</em>) pada basis pengetahuan aturan produksi (<em>Rule Base</em>) hingga menghasilkan konklusi (konsekuen <em>THEN</em>) berupa penyakit dan rekomendasi solusi medis yang tepat.
                        </p>

                        <!-- 4 Pilar Alur Inferensi (Grid 2x2) -->
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <div class="p-2 border rounded bg-light h-100">
                                    <strong class="d-block text-info small font-weight-bold mb-1">
                                        <span class="badge badge-info mr-1">1</span> Working Memory
                                    </strong>
                                    <p class="small text-muted mb-0" style="font-size: 0.82rem;">
                                        Menyimpan fakta klinis awal berupa daftar gejala yang dialami ibu hamil saat konsultasi.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="p-2 border rounded bg-light h-100">
                                    <strong class="d-block text-warning small font-weight-bold mb-1">
                                        <span class="badge badge-warning mr-1">2</span> Pattern Matching
                                    </strong>
                                    <p class="small text-muted mb-0" style="font-size: 0.82rem;">
                                        Mencocokkan fakta di Working Memory dengan klausul kondisi (<em>IF</em>) pada {{ $aturan->count() }} aturan produksi ({{ $aturan->first()?->kode_aturan }} - {{ $aturan->last()?->kode_aturan }}).
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="p-2 border rounded bg-light h-100">
                                    <strong class="d-block text-danger small font-weight-bold mb-1">
                                        <span class="badge badge-danger mr-1">3</span> Conflict Resolution
                                    </strong>
                                    <p class="small text-muted mb-0" style="font-size: 0.82rem;">
                                        Menyelesaikan aturan jika beberapa cocok: prioritas 100%, multi-diagnosis, atau parsial tertinggi (&ge; 50%).
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="p-2 border rounded bg-light h-100">
                                    <strong class="d-block text-success small font-weight-bold mb-1">
                                        <span class="badge badge-success mr-1">4</span> Firing & Trace Log
                                    </strong>
                                    <p class="small text-muted mb-0" style="font-size: 0.82rem;">
                                        Aturan terpilih dieksekusi (<em>fired</em>) untuk menetapkan konklusi penyakit, memicu solusi medis, dan mencatat alur penalaran.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center">
                        <div class="small text-muted">
                            <i class="fas fa-check-circle text-success mr-1"></i> {{ $aturan->where('is_active', true)->count() }} Aturan Aktif Terverifikasi
                        </div>
                        <a href="{{ route('admin.aturan.index') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                            <i class="fas fa-code-branch mr-1"></i> Kelola Basis Aturan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Uji Blackbox, Aturan & Validasi (Dinamis Total dari Database) -->
        <div class="col-lg-6 mb-4">
            <div class="card card-success card-outline shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold text-success mb-0">
                        <i class="fas fa-clipboard-check mr-2 text-success"></i> Uji Blackbox, Aturan & Validasi
                    </h5>
                    <span class="badge badge-success px-2 py-1 font-weight-bold">
                        Siap Diuji Langsung
                    </span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small mb-3">
                            Modul pengujian blackbox untuk memvalidasi fungsionalitas inferensi, verifikasi <em>firing rules</em>, dan akurasi diagnosa secara instan tanpa perlu input formulir pasien secara manual:
                        </p>

                        <!-- Skenario Uji Cepat (100% Dinamis dari Database) -->
                        <div class="row">
                            @forelse($penyakit as $p)
                                <div class="col-12 mb-2">
                                    <div class="p-2 px-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                        <div class="mr-2">
                                            <strong class="d-block text-dark small font-weight-bold">
                                                <span class="badge badge-primary font-monospace mr-1">{{ $p->kode_penyakit }}</span>
                                                Uji Kasus {{ $p->nama_penyakit }}
                                            </strong>
                                            <div class="mt-1" style="font-size: 0.78rem;">
                                                @forelse($p->aturans as $at)
                                                    <span class="badge badge-light border text-secondary font-weight-normal mr-1">
                                                        <strong>{{ $at->kode_aturan }}</strong> ({{ $at->gejalas->count() }} Gejala{{ $at->nama_aturan ? ' - ' . Str::limit($at->nama_aturan, 15) : '' }})
                                                    </span>
                                                @empty
                                                    <span class="text-warning small">Belum ada aturan aktif</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.simulasi.index', ['preset' => $p->kode_penyakit]) }}" class="btn btn-outline-primary btn-sm font-weight-bold text-nowrap">
                                                <i class="fas fa-play-circle mr-1"></i> Uji Kasus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-2 text-muted small">
                                    Belum ada data penyakit dan aturan di database.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <a href="{{ route('admin.simulasi.index') }}" class="btn btn-success btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Modul Uji Validasi Penuh
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
