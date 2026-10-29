@extends('layouts.admin')

@section('title', 'Data Penyakit')
@section('header', 'Data Penyakit')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-virus mr-2"></i> Master Data Penyakit</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus mr-1"></i> Tambah Penyakit
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
                        <th width="120" class="text-center">Kode Penyakit</th>
                        <th width="220">Nama Penyakit</th>
                        <th>Keterangan Klinis</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penyakit as $i => $p)
                    <tr>
                        <td class="text-center">{{ $i + $penyakit->firstItem() }}</td>
                        <td class="text-center">
                            <span class="badge badge-info font-monospace p-2 text-md">{{ $p->kode_penyakit ?? 'P0' . $p->id }}</span>
                        </td>
                        <td><strong>{{ $p->nama_penyakit }}</strong></td>
                        <td>{{ Str::limit($p->keterangan, 120) }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $p->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $p->id }}" title="Hapus"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data penyakit.</td></tr>
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
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus mr-1"></i> Tambah Penyakit</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Penyakit <span class="text-danger">*</span></label>
                            <input type="text" name="kode_penyakit" class="form-control" placeholder="Contoh: P01, P02" required value="{{ old('kode_penyakit') }}">
                        </div>
                        <div class="form-group">
                            <label>Nama Penyakit <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penyakit" class="form-control" placeholder="Contoh: Anemia" required value="{{ old('nama_penyakit') }}">
                        </div>
                        <div class="form-group">
                            <label>Keterangan Klinis</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Penjelasan medis penyakit">{{ old('keterangan') }}</textarea>
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

    @foreach($penyakit as $p)
    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.penyakit.update', $p) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Edit Penyakit</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Penyakit <span class="text-danger">*</span></label>
                            <input type="text" name="kode_penyakit" value="{{ $p->kode_penyakit }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Penyakit <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penyakit" value="{{ $p->nama_penyakit }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Klinis</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ $p->keterangan }}</textarea>
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
    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus{{ $p->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.penyakit.destroy', $p) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-trash mr-1"></i> Hapus Penyakit</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus penyakit <strong>[{{ $p->kode_penyakit }}] {{ $p->nama_penyakit }}</strong>?
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
