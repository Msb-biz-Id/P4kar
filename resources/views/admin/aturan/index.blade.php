@extends('layouts.admin')

@section('title', 'Basis Aturan (Rules)')
@section('header', 'Basis Aturan Forward Chaining (IF-THEN)')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-code-branch mr-2"></i> Daftar Aturan Inferensi Klinis</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus mr-1"></i> Tambah Aturan Baru
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th width="100" class="text-center">Kode Aturan</th>
                            <th width="200">Nama Aturan</th>
                            <th width="160">Penyakit (Consequent)</th>
                            <th>Premis Gejala (Antecedent: IF...)</th>
                            <th width="90" class="text-center">Status</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aturan as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + $aturan->firstItem() }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary font-monospace p-2 text-md">{{ $item->kode_aturan }}</span>
                                @if(($item->operator ?? 'AND') === 'OR')
                                    <div><span class="badge badge-warning font-monospace" title="Operator premis: OR (cukup satu gejala terpenuhi)">OR</span></div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->nama_aturan }}</strong>
                                @if($item->keterangan)
                                    <div class="small text-muted mt-1 font-italic">{{ $item->keterangan }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info p-1 mb-1 font-monospace">{{ $item->penyakit?->kode_penyakit ?? '-' }}</span>
                                <div><strong>{{ $item->penyakit?->nama_penyakit ?? 'Tidak Terkait' }}</strong></div>
                            </td>
                            <td>
                                <div class="small mb-1 text-muted"><strong>{{ $item->gejalas->count() }} Gejala Premis:</strong></div>
                                <div>
                                    @foreach($item->gejalas as $g)
                                        <span class="badge badge-light border mb-1" style="font-size: 0.85rem;">
                                            <strong class="text-teal font-monospace" style="color: #0f766e;">[{{ $g->kode_gejala }}]</strong> {{ $g->nama_gejala }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                @if($item->is_active)
                                    <span class="badge badge-success px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalHapus{{ $item->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada basis aturan tersimpan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($aturan->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $aturan->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah Aturan -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.aturan.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus mr-1"></i> Tambah Aturan Forward Chaining</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode Aturan <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_aturan" class="form-control" placeholder="Contoh: R07" required value="{{ old('kode_aturan') }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nama Aturan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_aturan" class="form-control" placeholder="Deskripsi aturan" required value="{{ old('nama_aturan') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Penyakit Tujuan (THEN ...) <span class="text-danger">*</span></label>
                            <select name="penyakit_id" class="form-control" required>
                                <option value="">-- Pilih Penyakit --</option>
                                @foreach($penyakit as $p)
                                    <option value="{{ $p->id }}" {{ old('penyakit_id') == $p->id ? 'selected' : '' }}>
                                        [{{ $p->kode_penyakit }}] {{ $p->nama_penyakit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Operator Premis (IF) <span class="text-danger">*</span></label>
                            <select name="operator" class="form-control" required>
                                <option value="AND" {{ old('operator', 'AND') === 'AND' ? 'selected' : '' }}>AND — semua gejala harus terpenuhi</option>
                                <option value="OR" {{ old('operator') === 'OR' ? 'selected' : '' }}>OR — cukup satu gejala terpenuhi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Premis Gejala (IF ...) - Centang gejala yang harus terpenuhi <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background-color: #f8fafc;">
                                <div class="row">
                                    @foreach($gejala as $g)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="g_tambah_{{ $g->id }}" name="gejala_ids[]" value="{{ $g->id }}">
                                            <label class="custom-control-label font-weight-normal" for="g_tambah_{{ $g->id }}">
                                                <span class="badge badge-secondary font-monospace">{{ $g->kode_gejala }}</span> {{ $g->nama_gejala }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan Formal Aturan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: IF G01 AND G02 THEN P01">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Aturan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit & Hapus Aturan -->
    @foreach($aturan as $item)
    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.aturan.update', $item) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Edit Aturan {{ $item->kode_aturan }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode Aturan <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_aturan" class="form-control" value="{{ $item->kode_aturan }}" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nama Aturan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_aturan" class="form-control" value="{{ $item->nama_aturan }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Penyakit Tujuan (THEN ...) <span class="text-danger">*</span></label>
                            <select name="penyakit_id" class="form-control" required>
                                @foreach($penyakit as $p)
                                    <option value="{{ $p->id }}" {{ $item->penyakit_id == $p->id ? 'selected' : '' }}>
                                        [{{ $p->kode_penyakit }}] {{ $p->nama_penyakit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Operator Premis (IF) <span class="text-danger">*</span></label>
                            <select name="operator" class="form-control" required>
                                <option value="AND" {{ old('operator', $item->operator ?? 'AND') === 'AND' ? 'selected' : '' }}>AND — semua gejala harus terpenuhi</option>
                                <option value="OR" {{ old('operator', $item->operator ?? 'AND') === 'OR' ? 'selected' : '' }}>OR — cukup satu gejala terpenuhi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Premis Gejala (IF ...) <span class="text-danger">*</span></label>
                            @php
                                $selectedGejalaIds = $item->gejalas->pluck('id')->toArray();
                            @endphp
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background-color: #f8fafc;">
                                <div class="row">
                                    @foreach($gejala as $g)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="g_edit_{{ $item->id }}_{{ $g->id }}" name="gejala_ids[]" value="{{ $g->id }}" {{ in_array($g->id, $selectedGejalaIds) ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-normal" for="g_edit_{{ $item->id }}_{{ $g->id }}">
                                                <span class="badge badge-secondary font-monospace">{{ $g->kode_gejala }}</span> {{ $g->nama_gejala }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan Formal Aturan</label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ $item->keterangan }}</textarea>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switchActive{{ $item->id }}" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="switchActive{{ $item->id }}">Aturan Aktif Digunakan dalam Diagnosa</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update Aturan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Aturan -->
    <div class="modal fade" id="modalHapus{{ $item->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.aturan.destroy', $item) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-trash mr-1"></i> Hapus Aturan</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus aturan <strong>{{ $item->kode_aturan }} - {{ $item->nama_aturan }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
