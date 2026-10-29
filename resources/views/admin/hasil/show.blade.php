@extends('layouts.admin')

@section('title', 'Detail Diagnosa')
@section('header', 'Detail Diagnosa Forward Chaining')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-injured mr-1"></i> Data Pasien & Hasil Diagnosa</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width: 35%">Nama Pasien</th>
                            <td><strong>{{ $hasil->nama_pasien }}</strong></td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $hasil->alamat_pasien ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Hasil Diagnosa</th>
                            <td>
                                @if($hasil->penyakit_id && $hasil->penyakit)
                                    <span class="badge badge-success text-md p-2">
                                        [{{ $hasil->penyakit->kode_penyakit }}] {{ $hasil->penyakit->nama_penyakit }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary text-md p-2">Tidak Cukup Spesifik</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status Kesimpulan</th>
                            <td>
                                @if($hasil->status_kesimpulan === 'cocok_penuh')
                                    <span class="badge badge-success px-2 py-1">Aturan Terpenuhi 100% (Pasti)</span>
                                @elseif($hasil->status_kesimpulan === 'multi_match')
                                    <span class="badge badge-primary px-2 py-1">Multi-Diagnosis</span>
                                @elseif($hasil->status_kesimpulan === 'parsial')
                                    <span class="badge badge-warning px-2 py-1">Kecocokan Parsial (&ge; 50%)</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Tidak Cukup Spesifik (&lt; 50%)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Aturan Terpilih</th>
                            <td>
                                @if($hasil->aturan)
                                    <span class="badge badge-info p-1 font-monospace">{{ $hasil->aturan->kode_aturan }}</span>
                                    <span>{{ $hasil->aturan->nama_aturan }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Konsultasi</th>
                            <td>{{ $hasil->created_at->format('d/m/Y H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card card-warning card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lightbulb mr-1 text-warning"></i> Solusi & Saran Medis</h3>
                </div>
                <div class="card-body">
                    @php
                        $solusiList = $hasil->penyakit ? \App\Models\Solusi::where('penyakit_id', $hasil->penyakit_id)->get() : collect();
                    @endphp

                    @if($solusiList->count())
                        @foreach($solusiList as $sol)
                            <div class="callout callout-warning mb-2 p-2">
                                <strong class="text-dark"><i class="fas fa-check mr-1 text-success"></i> {{ $sol->judul_solusi }}</strong>
                                <p class="mb-0 mt-1 small">{{ $sol->deskripsi_solusi }}</p>
                            </div>
                        @endforeach
                    @elseif($hasil->solusi)
                        <div class="callout callout-info mb-0 p-2">
                            <p class="mb-0">{{ $hasil->solusi }}</p>
                        </div>
                    @else
                        <p class="text-muted mb-0">Tidak ada solusi atau petunjuk khusus tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code-branch mr-1"></i> Rincian Evaluasi Basis Aturan (Forward Chaining)</h3>
                </div>
                <div class="card-body">
                    @php
                        $detail = $hasil->detail_inferensi ?? [];
                        $evaluasi = $detail['evaluasi_aturan'] ?? [];
                        $fakta = $detail['fakta_input'] ?? [];
                    @endphp

                    @if(!empty($fakta))
                        <div class="mb-3">
                            <label class="font-weight-bold text-muted small text-uppercase">Gejala yang Dipilih Pasien ({{ count($fakta) }}):</label>
                            <div>
                                @foreach($fakta as $f)
                                    <span class="badge badge-light border mb-1">
                                        <strong class="text-primary font-monospace">[{{ $f['kode'] ?? '-' }}]</strong> {{ $f['nama'] ?? '-' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                    @endif

                    @if(!empty($evaluasi))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="80" class="text-center">Aturan</th>
                                        <th>Penyakit Sasaran</th>
                                        <th class="text-center" width="110">Kecocokan</th>
                                        <th class="text-center" width="90">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evaluasi as $item)
                                        @php
                                            $isMatch = ($hasil->aturan_terpilih_id == $item['aturan_id']);
                                            $isExact = $item['is_exact_match'];
                                        @endphp
                                        <tr class="{{ $isMatch ? 'table-success font-weight-bold' : '' }}">
                                            <td class="text-center font-monospace">{{ $item['kode_aturan'] }}</td>
                                            <td>
                                                {{ $item['nama_penyakit'] }}
                                                @if($isMatch)
                                                    <span class="badge badge-success float-right">Terpilih</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $item['cocok_count'] }}/{{ $item['total_premis'] }} ({{ $item['match_percentage'] }}%)
                                            </td>
                                            <td class="text-center">
                                                @if($isExact)
                                                    <span class="badge badge-success">Penuh</span>
                                                @elseif($item['match_percentage'] >= 50)
                                                    <span class="badge badge-warning">Parsial</span>
                                                @else
                                                    <span class="badge badge-secondary">Rendah</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Rincian inferensi aturan tidak tersedia atau tersimpan dalam format legacy.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.hasil.index') }}" class="btn btn-secondary font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Hasil
            </a>
            <form action="{{ route('admin.hasil.destroy', $hasil) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data riwayat diagnosa ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger font-weight-bold">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Riwayat Ini
                </button>
            </form>
        </div>
    </div>
@endsection
