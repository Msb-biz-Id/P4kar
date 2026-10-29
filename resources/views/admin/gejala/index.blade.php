@extends('layouts.admin')

@section('title', 'Data Gejala')
@section('header', 'Data Gejala Klinis')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-stethoscope mr-2"></i> Master Data {{ $gejala->total() }} Gejala Klinis</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus mr-1"></i> Tambah Gejala
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th width="120" class="text-center">Kode Gejala</th>
                        <th>Nama / Deskripsi Gejala Klinis</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gejala as $i => $g)
                    <tr>
                        <td class="text-center">{{ $i + $gejala->firstItem() }}</td>
                        <td class="text-center">
                            <span class="badge badge-secondary font-monospace p-2 text-md">{{ $g->kode_gejala ?? 'G' . str_pad($g->id, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td><strong>{{ $g->nama_gejala }}</strong></td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $g->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $g->id }}" title="Hapus"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data gejala.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gejala->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $gejala->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.gejala.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus mr-1"></i> Tambah Gejala Klinis</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Gejala <span class="text-danger">*</span></label>
                            <input type="text" name="kode_gejala" class="form-control" placeholder="Contoh: G01, G02, ..." required value="{{ old('kode_gejala') }}">
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Gejala Klinis <span class="text-danger">*</span></label>
                            <textarea name="nama_gejala" class="form-control" rows="2" placeholder="Masukkan keluhan gejala" required>{{ old('nama_gejala') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit & Hapus per item -->
    @foreach($gejala as $g)
    <!-- Edit -->
    <div class="modal fade" id="modalEdit{{ $g->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.gejala.update', $g) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Edit Gejala</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Gejala <span class="text-danger">*</span></label>
                            <input type="text" name="kode_gejala" value="{{ $g->kode_gejala }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Gejala Klinis <span class="text-danger">*</span></label>
                            <textarea name="nama_gejala" class="form-control" rows="2" required>{{ $g->nama_gejala }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Hapus -->
    <div class="modal fade" id="modalHapus{{ $g->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.gejala.destroy', $g) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-trash mr-1"></i> Hapus Gejala</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus gejala <strong>[{{ $g->kode_gejala }}] {{ $g->nama_gejala }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
