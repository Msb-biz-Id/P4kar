@extends('layouts.admin')

@section('title', 'Data Gejala')
@section('header', 'Data Gejala')

@section('content')
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Gejala
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Gejala</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gejala as $i => $g)
                    <tr>
                        <td>{{ $i + $gejala->firstItem() }}</td>
                        <td>{{ $g->nama_gejala }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $g->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $g->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">Belum ada data</td></tr>
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
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Gejala</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Gejala</label>
                            <input type="text" name="nama_gejala" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Gejala</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Gejala</label>
                            <input type="text" name="nama_gejala" value="{{ $g->nama_gejala }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Gejala</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus <strong>{{ $g->nama_gejala }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
