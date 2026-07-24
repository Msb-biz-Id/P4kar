@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Penyakit::count() }}</h3>
                    <p>Data Penyakit</p>
                </div>
                <div class="icon"><i class="fas fa-virus"></i></div>
                <a href="{{ route('admin.penyakit.index') }}" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Gejala::count() }}</h3>
                    <p>Data Gejala</p>
                </div>
                <div class="icon"><i class="fas fa-stethoscope"></i></div>
                <a href="{{ route('admin.gejala.index') }}" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Relasi::count() }}</h3>
                    <p>Data Relasi</p>
                </div>
                <div class="icon"><i class="fas fa-project-diagram"></i></div>
                <a href="{{ route('admin.relasi.index') }}" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ \App\Models\Solusi::count() }}</h3>
                    <p>Data Solusi</p>
                </div>
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <a href="{{ route('admin.solusi.index') }}" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\Hasil::count() }}</h3>
                    <p>Hasil Diagnosa</p>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
                <a href="{{ route('admin.hasil.index') }}" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h3>
        </div>
        <div class="card-body">
            <p>Gunakan menu di samping untuk mengelola data penyakit, gejala, dan relasi. Anda juga bisa melihat riwayat diagnosa yang telah dilakukan.</p>
        </div>
    </div>
@endsection
