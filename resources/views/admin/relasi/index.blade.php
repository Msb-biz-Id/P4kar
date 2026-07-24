@extends('layouts.admin')

@section('title', 'Data Relasi')
@section('header', 'Data Relasi Gejala-Penyakit')

@section('content')
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah Relasi
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
                        <th>Gejala</th>
                        <th width="80" class="text-center">Bobot</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relasi as $i => $r)
                    <tr>
                        <td>{{ $i + $relasi->firstItem() }}</td>
                        <td>{{ $r->penyakit->nama_penyakit }}</td>
                        <td>{{ $r->gejala->nama_gejala }}</td>
                        <td class="text-center">{{ $r->bobot }}</td>
                        <td>
                            <a href="{{ route('admin.relasi.edit', $r) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $r->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($relasi->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $relasi->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.relasi.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Relasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Penyakit</label>
                            <select name="penyakit_id" class="form-control" required>
                                <option value="">Pilih Penyakit</option>
                                @foreach($penyakit as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Gejala</label>
                            <select name="gejala_id" class="form-control" required>
                                <option value="">Pilih Gejala</option>
                                @foreach($gejala as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_gejala }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bobot (0.01 - 1.0)</label>
                            <input type="number" name="bobot" value="0.5" min="0.01" max="1" step="0.01" class="form-control" required>
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

    <!-- Modal Edit -->
    @foreach($relasi as $r)
    <div class="modal fade" id="modalEdit{{ $r->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.relasi.update', $r) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Relasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Penyakit</label>
                            <select name="penyakit_id" class="form-control" required>
                                @foreach($penyakit as $p)
                                    <option value="{{ $p->id }}" {{ $r->penyakit_id == $p->id ? 'selected' : '' }}>{{ $p->nama_penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Gejala</label>
                            <select name="gejala_id" class="form-control" required>
                                @foreach($gejala as $g)
                                    <option value="{{ $g->id }}" {{ $r->gejala_id == $g->id ? 'selected' : '' }}>{{ $g->nama_gejala }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bobot</label>
                            <input type="number" name="bobot" value="{{ $r->bobot }}" min="0.01" max="1" step="0.01" class="form-control" required>
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
    <div class="modal fade" id="modalHapus{{ $r->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.relasi.destroy', $r) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Relasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus relasi <strong>{{ $r->penyakit->nama_penyakit }}</strong> - <strong>{{ $r->gejala->nama_gejala }}</strong>?
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
