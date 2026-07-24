@extends('layouts.admin')

@section('title', 'Riwayat Diagnosa')
@section('header', 'Riwayat Diagnosa')

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Pasien</th>
                        <th>Alamat</th>
                        <th>Diagnosa</th>
                        <th>Tanggal</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hasil as $i => $h)
                    <tr>
                        <td>{{ $i + $hasil->firstItem() }}</td>
                        <td>{{ $h->nama_pasien }}</td>
                        <td>{{ $h->alamat_pasien }}</td>
                        <td>{{ $h->penyakit->nama_penyakit ?? 'Tidak diketahui' }}</td>
                        <td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.hasil.show', $h) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
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
