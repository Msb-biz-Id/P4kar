<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Diagnosa - Sistem Pakar Forward Chaining</title>
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .main-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            background-color: white;
            overflow: hidden;
        }
        .main-card-header {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: white;
            padding: 24px 30px;
            border-bottom: none;
        }
        .info-table th {
            color: #64748b;
            font-weight: 500;
            width: 32%;
        }
        .info-table td {
            font-weight: 600;
            color: #1e293b;
        }
        .solusi-item {
            border-left: 4px solid #0d9488;
            border-radius: 8px;
            background-color: #f0fdfa;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }
        .solusi-item-title {
            color: #0f766e;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .solusi-item-desc {
            color: #134e4a;
            font-size: 0.875rem;
            margin-top: 4px;
            line-height: 1.5;
        }
        .rule-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            transition: all 0.2s ease-in-out;
        }
        .rule-card.active-match {
            border-color: #0d9488;
            background-color: #f0fdfa;
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.1);
        }
        .progress-bar-custom {
            height: 8px;
            border-radius: 999px;
            background-color: #e2e8f0;
            overflow: hidden;
        }
        .progress-bar-inner {
            height: 100%;
            border-radius: 999px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .text-rule-matched {
            color: #0d9488 !important;
        }
        .text-rule-default {
            color: #64748b !important;
        }
        .progress-inner-matched {
            background: linear-gradient(90deg, #0d9488, #14b8a6);
        }
        .progress-inner-default {
            background-color: #cbd5e1;
        }
        .btn-action {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-action-primary {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: white;
            border: none;
        }
        .btn-action-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: white;
        }
        .btn-action-secondary {
            border: 1px solid #cbd5e1;
            background-color: white;
            color: #475569;
        }
        .btn-action-secondary:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
    </style>
</head>
<body>
    @include('layouts.navigation')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card main-card">
                    <div class="main-card-header d-flex align-items-center">
                        <i class="fas fa-clipboard-check me-3" style="font-size: 1.75rem;"></i>
                        <div>
                            <h4 class="mb-1 fw-bold">Hasil Evaluasi Diagnosa</h4>
                            <p class="mb-0 small" style="color: rgba(255, 255, 255, 0.85);">Hasil penalaran sistem pakar menggunakan Metode Forward Chaining berdasarkan fakta klinis yang dirasakan</p>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4">
                            
                            <!-- Kolom Kiri: Informasi Pasien, Kesimpulan & Solusi -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-user me-2" style="color: #0d9488;"></i> Informasi Pasien</h5>
                                <table class="table info-table table-borderless mb-4">
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $hasil->nama_pasien }}</td>
                                    </tr>
                                    @if($hasil->alamat_pasien)
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $hasil->alamat_pasien }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Waktu Konsultasi</th>
                                        <td>{{ $hasil->created_at->format('d/m/Y H:i') }} WIB</td>
                                    </tr>
                                    <tr>
                                        <th>Status Kepastian</th>
                                        <td>
                                            @if($hasil->status_kesimpulan === 'cocok_penuh')
                                                <span class="badge bg-success px-2 py-1">Aturan Terpenuhi 100% (Pasti)</span>
                                            @elseif($hasil->status_kesimpulan === 'multi_match')
                                                <span class="badge bg-primary px-2 py-1">Multi-Diagnosis (Kombinasi Aturan)</span>
                                            @elseif($hasil->status_kesimpulan === 'parsial')
                                                <span class="badge bg-warning text-dark px-2 py-1">Kecocokan Parsial (&ge; 50%)</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Tidak Cukup Spesifik (&lt; 50%)</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-notes-medical me-2" style="color: #0d9488;"></i> Kesimpulan Hasil Diagnosa</h5>
                                
                                @if($hasil->penyakit_id && $hasil->penyakit)
                                    <div class="alert p-3 mb-4 {{ $hasil->status_kesimpulan === 'cocok_penuh' ? 'alert-success border-success-subtle' : 'alert-warning border-warning-subtle' }}" style="border-radius: 12px;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas {{ $hasil->status_kesimpulan === 'cocok_penuh' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-warning' }} me-3" style="font-size: 1.75rem;"></i>
                                            <div>
                                                <span class="d-block small text-muted">Hasil Penalaran Maju (Forward Chaining):</span>
                                                <strong class="fs-4 d-block">{{ $hasil->penyakit->nama_penyakit }}</strong>
                                                <span class="badge bg-secondary-subtle text-secondary font-monospace mt-1">Kode: {{ $hasil->penyakit->kode_penyakit ?? 'P0' . $hasil->penyakit->id }}</span>
                                                @if($hasil->aturan)
                                                    <span class="badge bg-teal-soft text-teal-800 ms-1 font-monospace" style="background-color: #ccfbf1; color: #0f766e;">Aturan Terpicu: {{ $hasil->aturan->kode_aturan }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-secondary p-3 mb-4" style="border-radius: 12px;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle text-secondary me-3" style="font-size: 1.75rem;"></i>
                                            <div>
                                                <span class="d-block small text-muted">Kesimpulan Diagnosa:</span>
                                                <strong class="fs-5">Gejala Tidak Mengarah Spesifik</strong>
                                                <p class="mb-0 small text-muted mt-1">Kombinasi gejala yang dimasukkan belum memenuhi ambang batas minimal aturan diagnosis maupun tanda bahaya yang didukung sistem.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @php
                                    $tandaBahayaRules = array_values(array_filter($evaluasiAturan, fn ($r) => !empty($r['is_exact_match']) && ($r['kode_penyakit'] ?? '') === 'P04'));
                                @endphp
                                @if(count($tandaBahayaRules))
                                    <div class="alert alert-danger p-3 mb-4 border-danger-subtle" style="border-radius: 12px;" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-exclamation-triangle text-danger me-3" style="font-size: 1.75rem;"></i>
                                            <div>
                                                <strong class="fs-5 d-block text-danger">Tanda Bahaya: Perlu Evaluasi Medis Segera</strong>
                                                <span class="d-block small mb-2">Aturan tanda bahaya (F04) terpicu berdasarkan fakta gejala Anda. Segera hubungi dokter, bidan, atau IGD rumah sakit terdekat — ini bukan diagnosis mandiri.</span>
                                                @foreach($tandaBahayaRules as $tb)
                                                    <div class="small mb-1">
                                                        <span class="badge bg-danger font-monospace me-1">{{ $tb['kode_aturan'] }}</span>
                                                        @foreach($tb['gejala_cocok'] as $gc)
                                                            <span class="badge bg-danger-subtle text-danger me-1">[{{ $gc['kode'] }}] {{ $gc['nama'] }}</span>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-lightbulb me-2" style="color: #0d9488;"></i> Solusi & Rekomendasi Medis</h5>
                                @php
                                    $solusiList = $hasil->penyakit ? \App\Models\Solusi::where('penyakit_id', $hasil->penyakit_id)->get() : collect();
                                @endphp

                                @if($solusiList->count())
                                    @foreach($solusiList as $sol)
                                        <div class="solusi-item">
                                            <div class="solusi-item-title"><i class="fas fa-check me-1"></i> {{ $sol->judul_solusi }}</div>
                                            <div class="solusi-item-desc">{{ $sol->deskripsi_solusi }}</div>
                                        </div>
                                    @endforeach
                                @elseif($hasil->solusi)
                                    <div class="solusi-item">
                                        <div class="solusi-item-title"><i class="fas fa-info-circle me-1"></i> Petunjuk Medis</div>
                                        <div class="solusi-item-desc">{{ $hasil->solusi }}</div>
                                    </div>
                                @else
                                    <p class="text-muted small">Tidak ada informasi solusi penanganan khusus yang tersedia.</p>
                                @endif
                            </div>

                            <!-- Kolom Kanan: Evaluasi Basis Aturan & Inference Trace -->
                            <div class="col-lg-6 border-start ps-lg-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0 text-secondary"><i class="fas fa-code-branch me-2" style="color: #0d9488;"></i> Evaluasi Basis Aturan (Rules)</h5>
                                    <span class="badge bg-light text-secondary border">Total Evaluasi: {{ count($evaluasiAturan) }} Aturan</span>
                                </div>
                                <p class="small text-muted mb-4">Tabel di bawah memperlihatkan bagaimana mesin inferensi mencocokkan fakta gejala Anda terhadap aturan IF-THEN:</p>

                                @if(!empty($evaluasiAturan))
                                    @foreach($evaluasiAturan as $ruleItem)
                                        @php
                                            $isMainMatch = ($hasil->aturan_terpilih_id == $ruleItem['aturan_id']);
                                            $isExact = $ruleItem['is_exact_match'];
                                            $pct = $ruleItem['match_percentage'];
                                        @endphp
                                        <div class="rule-card {{ $isMainMatch ? 'active-match' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge {{ $isExact ? 'bg-success' : ($isMainMatch ? 'bg-warning text-dark' : 'bg-secondary') }} font-monospace me-1">
                                                        {{ $ruleItem['kode_aturan'] }}
                                                    </span>
                                                    @if(($ruleItem['operator'] ?? 'AND') === 'OR')
                                                        <span class="badge bg-warning text-dark font-monospace me-1" title="Cukup satu premis terpenuhi (G35 OR G36 OR G40)">OR</span>
                                                    @endif
                                                    <strong class="text-dark">{{ $ruleItem['nama_penyakit'] }}</strong>
                                                    @if($isMainMatch)
                                                        <span class="badge bg-success ms-1 small">Aturan Terpilih</span>
                                                    @endif
                                                </div>
                                                <span class="fw-bold {{ $isMainMatch ? 'text-rule-matched' : 'text-rule-default' }}">
                                                    {{ $ruleItem['cocok_count'] }}/{{ $ruleItem['total_premis'] }} Gejala ({{ $pct }}%)
                                                </span>
                                            </div>

                                            <div class="progress-bar-custom mb-3">
                                                <div class="progress-bar-inner {{ $isMainMatch ? 'progress-inner-matched' : 'progress-inner-default' }}" {!! 'style="width: ' . $pct . '%;"' !!}></div>
                                            </div>

                                            <!-- Rincian Gejala yang Cocok vs Belum Terpenuhi -->
                                            <div class="small">
                                                @if(!empty($ruleItem['gejala_cocok']))
                                                    <div class="mb-1 text-success">
                                                        <i class="fas fa-check-circle me-1"></i> <strong>Gejala Cocok:</strong>
                                                        @foreach($ruleItem['gejala_cocok'] as $gc)
                                                            <span class="badge bg-success-subtle text-success me-1 mb-1">[{{ $gc['kode'] }}] {{ $gc['nama'] }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if(!empty($ruleItem['gejala_kurang']))
                                                    <div class="text-muted">
                                                        <i class="fas fa-minus-circle me-1"></i> <strong>Premis Belum Terpenuhi:</strong>
                                                        @foreach($ruleItem['gejala_kurang'] as $gk)
                                                            <span class="badge bg-light text-muted border me-1 mb-1">[{{ $gk['kode'] }}] {{ $gk['nama'] }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted small">Data jejak inferensi aturan tidak tersedia.</p>
                                @endif
                            </div>
                        </div>

                        <hr class="my-5 text-slate-200">

                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <a href="{{ route('konsultasi') }}" class="btn btn-action btn-action-primary px-4 py-3"><i class="fas fa-redo me-2"></i> Lakukan Konsultasi Lagi</a>
                            <a href="{{ route('home') }}" class="btn btn-action btn-action-secondary px-4 py-3"><i class="fas fa-home me-2"></i> Kembali ke Beranda</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>