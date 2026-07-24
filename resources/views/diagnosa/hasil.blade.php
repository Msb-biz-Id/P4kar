<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Diagnosa - Sistem Pakar Diagnosa Ibu Hamil</title>
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
            width: 30%;
        }
        .info-table td {
            font-weight: 600;
            color: #1e293b;
        }
        .solusi-item {
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            background-color: #fffbeb;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }
        .solusi-item-title {
            color: #b45309;
            font-weight: 700;
            font-size: 1rem;
        }
        .solusi-item-desc {
            color: #78350f;
            font-size: 0.9rem;
            margin-top: 4px;
            line-height: 1.5;
        }
        .progress-bar-custom {
            height: 10px;
            border-radius: 999px;
            background-color: #e2e8f0;
            overflow: hidden;
        }
        .progress-bar-inner {
            height: 100%;
            border-radius: 999px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
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
            <div class="col-lg-10">
                <div class="card main-card">
                    <div class="main-card-header d-flex align-items-center">
                        <i class="fas fa-clipboard-check me-3" style="font-size: 1.75rem;"></i>
                        <div>
                            <h4 class="mb-1 fw-bold">Hasil Analisis Diagnosa</h4>
                            <p class="mb-0 small" style="color: rgba(255, 255, 255, 0.8);">Berdasarkan gejala-gejala yang Anda centang pada form konsultasi</p>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4">
                            
                            <!-- Kolom Kiri: Informasi Pasien & Hasil Diagnosa -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-user me-2 text-teal-600" style="color: #0d9488;"></i> Informasi Pasien</h5>
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
                                        <th>Tanggal</th>
                                        <td>{{ $hasil->created_at->format('d/m/Y H:i') }} WIB</td>
                                    </tr>
                                </table>

                                <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-notes-medical me-2 text-teal-600" style="color: #0d9488;"></i> Hasil Diagnosa Utama</h5>
                                <div class="alert p-3 mb-4 {{ $hasil->penyakit_id ? 'alert-success border-success-subtle' : 'alert-warning border-warning-subtle' }}" style="border-radius: 12px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas {{ $hasil->penyakit_id ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning' }} me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <span class="d-block small text-muted">Kesimpulan Diagnosa:</span>
                                            <strong class="fs-5">{{ $hasil->penyakit->nama_penyakit ?? 'Tidak diketahui' }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-lightbulb me-2 text-teal-600" style="color: #0d9488;"></i> Solusi & Saran Penanganan</h5>
                                @php
                                    $solusiList = \App\Models\Solusi::where('penyakit_id', $hasil->penyakit_id)->get();
                                @endphp

                                @if($solusiList->count())
                                    @foreach($solusiList as $sol)
                                        <div class="solusi-item">
                                            <div class="solusi-item-title">{{ $sol->judul_solusi }}</div>
                                            <div class="solusi-item-desc">{{ $sol->deskripsi_solusi }}</div>
                                        </div>
                                    @endforeach
                                @elseif($hasil->solusi)
                                    <div class="solusi-item">
                                        <div class="solusi-item-title">Saran Medis</div>
                                        <div class="solusi-item-desc">{{ $hasil->solusi }}</div>
                                    </div>
                                @elseif($hasil->penyakit && $hasil->penyakit->keterangan)
                                    <div class="solusi-item" style="border-left-color: #0284c7; background-color: #f0f9ff;">
                                        <div class="solusi-item-title" style="color: #0369a1;">Keterangan Penyakit</div>
                                        <div class="solusi-item-desc" style="color: #0c4a6e;">{{ $hasil->penyakit->keterangan }}</div>
                                    </div>
                                @else
                                    <p class="text-muted small">Tidak ada informasi solusi penanganan yang tersedia.</p>
                                @endif
                            </div>

                            <!-- Kolom Kanan: Rincian Analisis Probabilitas Naive Bayes -->
                            <div class="col-md-6 border-start ps-md-4">
                                <h5 class="fw-bold mb-4 text-secondary"><i class="fas fa-calculator me-2 text-teal-600" style="color: #0d9488;"></i> Analisis Probabilitas Naive Bayes</h5>
                                
                                @if(!empty($hasilPerhitungan))
                                    <div class="d-flex flex-column gap-4">
                                        @foreach($hasilPerhitungan as $item)
                                            @php
                                                $isTop = ($hasil->penyakit && $item['nama_penyakit'] === $hasil->penyakit->nama_penyakit);
                                                $percent = $item['probabilitas'] * 100;
                                            @endphp
                                            <div class="p-3 border rounded-3 {{ $isTop ? 'bg-light border-teal-subtle' : '' }}" style="border-radius: 12px; transition: all 0.2s;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <span class="fw-bold {{ $isTop ? 'text-teal-800' : 'text-slate-700' }}">{{ $item['nama_penyakit'] }}</span>
                                                        @if($isTop)
                                                            <span class="badge bg-success ms-2 small">Diagnosa Utama</span>
                                                        @endif
                                                    </div>
                                                    <span class="fw-bold text-teal-600" style="color: #0d9488;">{{ number_format($percent, 2) }}%</span>
                                                </div>
                                                <div class="progress-bar-custom">
                                                    <div class="progress-bar-inner" style="width: {{ $percent }}%; background: {{ $isTop ? 'linear-gradient(90deg, #0d9488, #14b8a6)' : '#cbd5e1' }};"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">Data rincian probabilitas kalkulasi tidak tersedia.</p>
                                @endif
                            </div>
                        </div>

                        <hr class="my-5 text-slate-200">

                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <a href="{{ route('konsultasi') }}" class="btn btn-action btn-action-primary px-4 py-3"><i class="fas fa-redo me-2"></i> Lakukan Konsultasi Lagi</a>
                            <a href="{{ route('home') }}" class="btn btn-action btn-action-secondary px-4 py-3"><i class="fas fa-home me-2"></i> Kembali ke Home</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>