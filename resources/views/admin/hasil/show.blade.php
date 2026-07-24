@extends('layouts.admin')

@section('title', 'Detail Diagnosa')
@section('header', 'Detail Diagnosa')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-injured mr-1"></i> Data Pasien & Hasil</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width: 35%">Nama Pasien</th>
                            <td>{{ $hasil->nama_pasien }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $hasil->alamat_pasien ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Hasil Diagnosa</th>
                            <td>
                                <span class="badge {{ $hasil->penyakit_id ? 'badge-success' : 'badge-warning' }} text-md p-2">
                                    {{ $hasil->penyakit->nama_penyakit ?? 'Tidak diketahui' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $hasil->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card card-warning card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lightbulb mr-1 text-warning"></i> Solusi & Saran</h3>
                </div>
                <div class="card-body">
                    @php
                        $solusiList = \App\Models\Solusi::where('penyakit_id', $hasil->penyakit_id)->get();
                    @endphp

                    @if($solusiList->count())
                        @foreach($solusiList as $sol)
                            <div class="callout callout-warning mb-2 p-2">
                                <strong>{{ $sol->judul_solusi }}</strong>
                                <p class="mb-0 mt-1">{{ $sol->deskripsi_solusi }}</p>
                            </div>
                        @endforeach
                    @elseif($hasil->solusi)
                        <div class="callout callout-warning mb-0 p-2">
                            <p class="mb-0">{{ $hasil->solusi }}</p>
                        </div>
                    @elseif($hasil->penyakit && $hasil->penyakit->keterangan)
                        <div class="callout callout-info mb-0 p-2">
                            <h5>Keterangan Penyakit</h5>
                            <p class="mb-0">{{ $hasil->penyakit->keterangan }}</p>
                        </div>
                    @else
                        <p class="text-muted mb-0">Tidak ada solusi atau keterangan tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calculator mr-1"></i> Perhitungan Probabilitas Naive Bayes</h3>
                </div>
                <div class="card-body">
                    @if(!empty($hasil->probabilitas))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Penyakit</th>
                                        <th class="text-center" style="width: 30%">Probabilitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hasil->probabilitas as $item)
                                        @php
                                            $isTop = ($hasil->penyakit && $item['nama_penyakit'] === $hasil->penyakit->nama_penyakit);
                                        @endphp
                                        <tr class="{{ $isTop ? 'table-success font-weight-bold' : '' }}">
                                            <td>
                                                {{ $item['nama_penyakit'] }}
                                                @if($isTop)
                                                    <span class="badge badge-success float-right">Diagnosa</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($item['probabilitas'] * 100, 2) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Data perhitungan tidak tersimpan atau tidak tersedia.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ route('admin.hasil.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Hasil</a>
        </div>
    </div>
@endsection
