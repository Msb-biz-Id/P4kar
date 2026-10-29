@extends('layouts.admin')

@section('title', 'Uji Blackbox, Aturan & Validasi')
@section('header', 'Uji Blackbox, Aturan & Validasi')

@section('content')
    <div class="row">
        <!-- Kolom Kiri: Panel Kontrol Pengujian & Pilihan Gejala -->
        <div class="col-lg-6">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-clipboard-check mr-1 text-primary"></i> Panel Uji Blackbox & Validasi Aturan
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success p-2 font-monospace">Uji Blackbox & Validasi</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Pilih skenario pengujian di bawah dengan 1 klik untuk memverifikasi fungsionalitas blackbox dan akurasi penalaran Forward Chaining secara instan, atau pilih kombinasi gejala secara bebas:
                    </p>

                    <!-- Tombol Preset Skenario Siap Uji (100% Dinamis dari Basis Aturan Database) -->
                    <label class="font-weight-bold small text-uppercase text-secondary mb-2">Preset Skenario Pengujian Cepat (Basis Aturan Aktif):</label>
                    <div class="d-flex flex-wrap gap-2 mb-4" style="gap: 8px;">
                        @foreach($aturan->where('is_active', true) as $at)
                            @php
                                $gejalaIds = $at->gejalas->pluck('id')->values()->toArray();
                            @endphp
                            <button type="button" class="btn btn-outline-primary btn-sm preset-btn" 
                                    data-gejala="{{ json_encode($gejalaIds) }}" 
                                    data-penyakit="{{ $at->penyakit?->kode_penyakit }}"
                                    title="{{ $at->nama_aturan }} ({{ count($gejalaIds) }} Gejala)">
                                <i class="fas fa-check-circle mr-1"></i> {{ $at->kode_aturan }}: {{ $at->penyakit->nama_penyakit ?? 'Penyakit' }} ({{ count($gejalaIds) }} Gejala)
                            </button>
                        @endforeach

                        @php
                            // Generate Skenario Parsial secara dinamis dari aturan pertama yang gejalanya >= 2
                            $ruleParsial = $aturan->first(fn($a) => $a->is_active && $a->gejalas->count() >= 2);
                            $parsialGejalaIds = [];
                            if ($ruleParsial) {
                                $takeCount = max(1, (int) floor($ruleParsial->gejalas->count() * 0.75));
                                $parsialGejalaIds = $ruleParsial->gejalas->take($takeCount)->pluck('id')->values()->toArray();
                            }

                            // Generate Skenario Tidak Cukup Spesifik (< 50%) secara dinamis (1 gejala)
                            $singleGejalaId = $gejala->first()?->id ? [$gejala->first()->id] : [];

                            // Generate Skenario Multi-Diagnosis secara dinamis dari 2 aturan dengan penyakit berbeda
                            $rulePenyakitA = $aturan->firstWhere('is_active', true);
                            $rulePenyakitB = $aturan->first(fn($a) => $a->is_active && $a->penyakit_id !== $rulePenyakitA?->penyakit_id);
                            $multiGejalaIds = [];
                            if ($rulePenyakitA && $rulePenyakitB) {
                                $multiGejalaIds = array_values(array_unique(array_merge(
                                    $rulePenyakitA->gejalas->pluck('id')->toArray(),
                                    $rulePenyakitB->gejalas->pluck('id')->toArray()
                                )));
                            }
                        @endphp

                        @if(!empty($parsialGejalaIds))
                            <button type="button" class="btn btn-outline-secondary btn-sm preset-btn" data-gejala="{{ json_encode($parsialGejalaIds) }}" title="Uji Kasus Parsial">
                                <i class="fas fa-adjust mr-1"></i> Uji Parsial ({{ count($parsialGejalaIds) }}/{{ $ruleParsial->gejalas->count() }} Gejala {{ $ruleParsial->kode_aturan }})
                            </button>
                        @endif

                        @if(!empty($singleGejalaId))
                            <button type="button" class="btn btn-outline-dark btn-sm preset-btn" data-gejala="{{ json_encode($singleGejalaId) }}" title="Uji Tidak Cukup Spesifik">
                                <i class="fas fa-times-circle mr-1"></i> Uji Tidak Cukup Spesifik (< 50%)
                            </button>
                        @endif

                        @if(!empty($multiGejalaIds))
                            <button type="button" class="btn btn-outline-success btn-sm preset-btn" data-gejala="{{ json_encode($multiGejalaIds) }}" title="Uji Multi-Diagnosis Komplikasi">
                                <i class="fas fa-layer-group mr-1"></i> Uji Multi-Diagnosis ({{ $rulePenyakitA->kode_aturan }} + {{ $rulePenyakitB->kode_aturan }})
                            </button>
                        @endif
                    </div>

                    <form id="simulasiForm">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold mb-0">Daftar {{ $gejala->count() }} Gejala Klinis:</label>
                            <div>
                                <span id="counterBadge" class="badge badge-secondary px-2 py-1 mr-2">0 Gejala Dipilih</span>
                                <button type="button" class="btn btn-link btn-sm p-0 text-danger" id="resetCheckboxes">Bersihkan</button>
                            </div>
                        </div>

                        <div class="border rounded p-3 mb-4" style="max-height: 380px; overflow-y: auto; background-color: #f8fafc;">
                            <div class="row">
                                @foreach($gejala as $g)
                                <div class="col-12 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input sim-checkbox" id="sim_g_{{ $g->id }}" name="gejala[]" value="{{ $g->id }}">
                                        <label class="custom-control-label font-weight-normal" for="sim_g_{{ $g->id }}" style="cursor: pointer;">
                                            <span class="badge badge-light border font-monospace mr-1 text-teal font-weight-bold" style="color: #0f766e;">
                                                {{ $g->kode_gejala ?? 'G' . str_pad($g->id, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            {{ $g->nama_gejala }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="button" id="btnJalankan" class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold">
                            <i class="fas fa-cogs mr-2"></i> Jalankan Mesin Inferensi Forward Chaining
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Hasil Evaluasi & Explainability Trace Log -->
        <div class="col-lg-6">
            <div class="card card-success card-outline shadow-sm" id="resultCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-poll-h mr-1 text-success"></i> Hasil Penalaran & Jejak Inferensi (Trace Log)
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Placeholder saat belum ada tes -->
                    <div id="emptyState" class="text-center py-5 text-muted">
                        <i class="fas fa-clipboard-check fa-4x mb-3 text-secondary" style="opacity: 0.4;"></i>
                        <h5 class="font-weight-bold">Uji Validasi Siap Dijalankan</h5>
                        <p class="small">Pilih salah satu skenario pengujian di sebelah kiri, lalu klik <strong>Jalankan Uji Inferensi</strong> untuk melihat proses pencocokan aturan secara langsung.</p>
                    </div>

                    <!-- Spinner Loading -->
                    <div id="loadingState" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                        <p class="font-weight-bold text-secondary">Mengevaluasi Working Memory terhadap {{ $aturan->where('is_active', true)->count() }} Basis Aturan...</p>
                    </div>

                    <!-- Konten Hasil Uji -->
                    <div id="contentHasil" class="d-none">
                        <!-- Banner Diagnosa -->
                        <div id="bannerDiagnosa" class="alert alert-success p-3 mb-3" style="border-radius: 10px;">
                            <div class="d-flex align-items-center">
                                <i id="iconStatus" class="fas fa-check-circle fa-2x mr-3 text-success"></i>
                                <div>
                                    <span class="small text-uppercase font-weight-bold text-muted d-block" id="labelStatus">Kesimpulan Diagnosa:</span>
                                    <h4 class="font-weight-bold mb-0" id="namaPenyakitHasil">-</h4>
                                    <div class="mt-1" id="badgeContainer"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Solusi Medis -->
                        <div class="card card-light mb-3">
                            <div class="card-header p-2 bg-light">
                                <strong class="small text-uppercase text-secondary"><i class="fas fa-heartbeat text-danger mr-1"></i> Rekomendasi Solusi Medis Terkoreksi:</strong>
                            </div>
                            <div class="card-body p-3" id="solusiContainer">
                                <p class="small mb-0 text-muted" id="solusiText">-</p>
                            </div>
                        </div>

                        <!-- Tabel Evaluasi Basis Aturan -->
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="font-weight-bold small text-uppercase text-secondary mb-0">Evaluasi Pattern Matching ({{ $aturan->count() }} Aturan):</label>
                            <span class="badge badge-light border small" id="totalFaktaBadge">0 Fakta Masukan</span>
                        </div>
                        <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-bordered table-sm table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="70" class="text-center">Aturan</th>
                                        <th>Target Penyakit</th>
                                        <th width="110" class="text-center">Premis Cocok</th>
                                        <th width="90" class="text-center">Kecocokan</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelEvaluasiBody">
                                    <!-- Dynamic rows via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.sim-checkbox');
        const counterBadge = document.getElementById('counterBadge');
        const presetBtns = document.querySelectorAll('.preset-btn');
        const resetBtn = document.getElementById('resetCheckboxes');
        const btnJalankan = document.getElementById('btnJalankan');

        const emptyState = document.getElementById('emptyState');
        const loadingState = document.getElementById('loadingState');
        const contentHasil = document.getElementById('contentHasil');

        // Update counter gejala terpilih
        function updateCounter() {
            const count = document.querySelectorAll('.sim-checkbox:checked').length;
            counterBadge.textContent = count + ' Gejala Dipilih';
            if (count > 0) {
                counterBadge.className = 'badge badge-primary px-2 py-1 mr-2';
            } else {
                counterBadge.className = 'badge badge-secondary px-2 py-1 mr-2';
            }
        }

        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', updateCounter);
        });

        // Reset
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            checkboxes.forEach(cb => cb.checked = false);
            updateCounter();
        });

        // Preset click handler
        presetBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const gejalaIds = JSON.parse(this.getAttribute('data-gejala'));
                checkboxes.forEach(function(cb) {
                    cb.checked = gejalaIds.includes(parseInt(cb.value));
                });
                updateCounter();
                // Otomatis jalankan simulasi agar instan untuk penguji/dosen
                jalankanSimulasi();
            });
        });

        btnJalankan.addEventListener('click', function() {
            jalankanSimulasi();
        });

        function jalankanSimulasi() {
            const checkedValues = Array.from(document.querySelectorAll('.sim-checkbox:checked')).map(cb => parseInt(cb.value));

            if (checkedValues.length === 0) {
                alert('Silakan pilih minimal 1 gejala untuk menjalankan simulasi penalaran.');
                return;
            }

            // Tampilkan loading
            emptyState.classList.add('d-none');
            contentHasil.classList.add('d-none');
            loadingState.classList.remove('d-none');

            // Kirim request ke SimulasiController
            fetch("{{ route('admin.simulasi.proses') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ gejala: checkedValues })
            })
            .then(res => res.json())
            .then(res => {
                loadingState.classList.add('d-none');
                if (res.success && res.data) {
                    renderHasil(res.data);
                } else {
                    alert('Gagal menjalankan simulasi: ' + (res.message || 'Error server'));
                }
            })
            .catch(err => {
                loadingState.classList.add('d-none');
                alert('Terjadi kesalahan koneksi simulasi.');
                console.error(err);
            });
        }

        function renderHasil(data) {
            contentHasil.classList.remove('d-none');

            const banner = document.getElementById('bannerDiagnosa');
            const icon = document.getElementById('iconStatus');
            const namaPenyakit = document.getElementById('namaPenyakitHasil');
            const badgeContainer = document.getElementById('badgeContainer');
            const solusiText = document.getElementById('solusiText');
            const tabelBody = document.getElementById('tabelEvaluasiBody');
            const totalFaktaBadge = document.getElementById('totalFaktaBadge');

            totalFaktaBadge.textContent = data.detail_inferensi.fakta_input.length + ' Fakta Terpilih';

            badgeContainer.innerHTML = '';

            if (data.status_kesimpulan === 'cocok_penuh') {
                banner.className = 'alert alert-success p-3 mb-3 border-success-subtle';
                icon.className = 'fas fa-check-circle fa-2x mr-3 text-success';
                namaPenyakit.textContent = data.penyakit ? data.penyakit.nama_penyakit : '-';
                badgeContainer.innerHTML = `
                    <span class="badge badge-success px-2 py-1 mr-1">Status: Cocok Penuh (100%)</span>
                    <span class="badge badge-light border font-monospace">Aturan Terpicu: ${data.aturan_terpilih ? data.aturan_terpilih.kode_aturan : '-'}</span>
                `;
            } else if (data.status_kesimpulan === 'multi_match') {
                banner.className = 'alert alert-primary p-3 mb-3 border-primary-subtle';
                icon.className = 'fas fa-layer-group fa-2x mr-3 text-primary';
                namaPenyakit.textContent = (data.penyakit ? data.penyakit.nama_penyakit : '') + ' (Multi-Diagnosis)';
                badgeContainer.innerHTML = `
                    <span class="badge badge-primary px-2 py-1 mr-1">Multi-Match (Memenuhi Lebih dari 1 Aturan)</span>
                    <span class="badge badge-light border font-monospace">Aturan Utama: ${data.aturan_terpilih ? data.aturan_terpilih.kode_aturan : '-'}</span>
                `;
            } else if (data.status_kesimpulan === 'parsial') {
                banner.className = 'alert alert-warning p-3 mb-3 border-warning-subtle';
                icon.className = 'fas fa-exclamation-triangle fa-2x mr-3 text-warning';
                namaPenyakit.textContent = data.penyakit ? data.penyakit.nama_penyakit : '-';
                const matchPct = data.aturan_terpilih ? data.aturan_terpilih.match_percentage : 0;
                badgeContainer.innerHTML = `
                    <span class="badge badge-warning text-dark px-2 py-1 mr-1">Kecocokan Parsial (${matchPct}%)</span>
                    <span class="badge badge-light border font-monospace">Potensi: ${data.aturan_terpilih ? data.aturan_terpilih.kode_aturan : '-'}</span>
                `;
            } else {
                banner.className = 'alert alert-secondary p-3 mb-3';
                icon.className = 'fas fa-info-circle fa-2x mr-3 text-secondary';
                namaPenyakit.textContent = 'Tidak Cukup Spesifik (< 50%)';
                badgeContainer.innerHTML = `
                    <span class="badge badge-secondary px-2 py-1">Gejala belum mencapai ambang batas aturan spesifik</span>
                `;
            }

            // Solusi
            solusiText.textContent = data.solusi || 'Tidak ada petunjuk solusi khusus.';

            // Tabel Evaluasi
            tabelBody.innerHTML = '';
            const evaluasi = data.evaluasi_aturan || [];
            evaluasi.forEach(function(item) {
                const isSelected = (data.aturan_terpilih_id == item.aturan_id);
                const tr = document.createElement('tr');
                if (isSelected) {
                    tr.className = 'table-success font-weight-bold';
                }

                tr.innerHTML = `
                    <td class="text-center font-monospace font-weight-bold">${item.kode_aturan}</td>
                    <td>
                        ${item.nama_penyakit}
                        ${isSelected ? '<span class="badge badge-success float-right">Terpilih</span>' : ''}
                    </td>
                    <td class="text-center">${item.cocok_count} / ${item.total_premis} Gejala</td>
                    <td class="text-center">
                        <span class="badge ${item.is_exact_match ? 'badge-success' : (item.match_percentage >= 50 ? 'badge-warning' : 'badge-secondary')}">
                            ${item.match_percentage}%
                        </span>
                    </td>
                `;
                tabelBody.appendChild(tr);
            });
        }

        // Auto-trigger jika diarahkan dari dashboard dengan parameter ?preset=P01
        const urlParams = new URLSearchParams(window.location.search);
        const presetParam = urlParams.get('preset');
        if (presetParam) {
            const targetBtn = document.querySelector(`.preset-btn[data-penyakit="${presetParam}"]`);
            if (targetBtn) {
                targetBtn.click();
            }
        }
    });
</script>
@endpush
