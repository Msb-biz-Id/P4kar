@extends('layouts.admin')

@section('title', 'Data Solusi')
@section('header', 'Data Solusi')

@section('content')
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Solusi
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
                        <th>Penyakit</th>
                        <th>Judul Solusi</th>
                        <th>Deskripsi</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solusi as $i => $s)
                    <tr>
                        <td>{{ $i + $solusi->firstItem() }}</td>
                        <td>{{ $s->penyakit->nama_penyakit ?? '-' }}</td>
                        <td>{{ $s->judul_solusi }}</td>
                        <td>{{ Str::limit($s->deskripsi_solusi, 50) }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $s->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $s->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($solusi->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $solusi->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.solusi.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Solusi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Penyakit</label>
                            <select name="penyakit_id" class="form-control" required>
                                <option value="">-- Pilih Penyakit --</option>
                                @foreach($penyakit as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Judul Solusi</label>
                            <input type="text" name="judul_solusi" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Solusi</label>
                            <textarea name="deskripsi_solusi" class="form-control" rows="4" required></textarea>
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
    @foreach($solusi as $s)
    <!-- Edit -->
    <div class="modal fade" id="modalEdit{{ $s->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.solusi.update', $s) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Solusi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Penyakit</label>
                            <select name="penyakit_id" class="form-control" required>
                                @foreach($penyakit as $p)
                                    <option value="{{ $p->id }}" {{ $s->penyakit_id == $p->id ? 'selected' : '' }}>{{ $p->nama_penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Judul Solusi</label>
                            <input type="text" name="judul_solusi" value="{{ $s->judul_solusi }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Solusi</label>
                            <textarea name="deskripsi_solusi" class="form-control" rows="4" required>{{ $s->deskripsi_solusi }}</textarea>
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
    <div class="modal fade" id="modalHapus{{ $s->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.solusi.destroy', $s) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Solusi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus solusi <strong>{{ $s->judul_solusi }}</strong>?
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
