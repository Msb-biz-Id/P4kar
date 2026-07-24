@extends('layouts.admin')

@section('title', 'Data Penyakit')
@section('header', 'Data Penyakit')

@section('content')
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Penyakit
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
                        <th>Nama Penyakit</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penyakit as $i => $p)
                    <tr>
                        <td>{{ $i + $penyakit->firstItem() }}</td>
                        <td>{{ $p->nama_penyakit }}</td>
                        <td>{{ Str::limit($p->keterangan, 80) }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $p->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $p->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($penyakit->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $penyakit->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.penyakit.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Penyakit</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Penyakit</label>
                            <input type="text" name="nama_penyakit" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
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

    @foreach($penyakit as $p)
    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.penyakit.update', $p) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Penyakit</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Penyakit</label>
                            <input type="text" name="nama_penyakit" value="{{ $p->nama_penyakit }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ $p->keterangan }}</textarea>
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
    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus{{ $p->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.penyakit.destroy', $p) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Penyakit</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus <strong>{{ $p->nama_penyakit }}</strong>?
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
