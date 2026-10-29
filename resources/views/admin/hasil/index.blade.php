@extends('layouts.admin')

@section('title', 'Riwayat Diagnosa')
@section('header', 'Riwayat Diagnosa')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-history mr-1 text-primary"></i> Daftar Riwayat Diagnosa Pasien
            </h3>
            @if($hasil->total() > 0)
                <form action="{{ route('admin.hasil.destroy-all') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus SEMUA riwayat diagnosa? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm font-weight-bold">
                        <i class="fas fa-trash-alt mr-1"></i> Kosongkan Riwayat
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Pasien</th>
                        <th>Alamat</th>
                        <th>Hasil Diagnosa</th>
                        <th width="150" class="text-center">Status Inferensi</th>
                        <th>Tanggal</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hasil as $i => $h)
                    <tr>
                        <td class="text-center">{{ $i + $hasil->firstItem() }}</td>
                        <td><strong>{{ $h->nama_pasien }}</strong></td>
                        <td>{{ $h->alamat_pasien ?? '-' }}</td>
                        <td>
                            @if($h->penyakit)
                                <span class="badge badge-info p-1 font-monospace">{{ $h->penyakit->kode_penyakit }}</span>
                                <strong>{{ $h->penyakit->nama_penyakit }}</strong>
                            @else
                                <span class="text-muted">Tidak Cukup Spesifik</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($h->status_kesimpulan === 'cocok_penuh')
                                <span class="badge badge-success px-2 py-1">Pasti (100%)</span>
                            @elseif($h->status_kesimpulan === 'multi_match')
                                <span class="badge badge-primary px-2 py-1">Multi-Match</span>
                            @elseif($h->status_kesimpulan === 'parsial')
                                <span class="badge badge-warning px-2 py-1">Parsial (&ge; 50%)</span>
                            @else
                                <span class="badge badge-secondary px-2 py-1">Tidak Cocok</span>
                            @endif
                        </td>
                        <td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.hasil.show', $h) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.hasil.destroy', $h) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data diagnosa pasien {{ $h->nama_pasien }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Riwayat">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data diagnosa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($hasil->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $hasil->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
@endsection
