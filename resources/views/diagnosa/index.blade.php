<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pakar Diagnosa Ibu Hamil</title>
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
        .hero {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: white;
            padding: 100px 0 80px 0;
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(20, 184, 166, 0.2);
            border-radius: 50%;
            top: -50px;
            right: -50px;
            filter: blur(80px);
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(15, 118, 110, 0.3);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
            filter: blur(60px);
        }
        .hero-title {
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.15;
        }
        .hero-subtitle {
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 0 auto;
        }
        .btn-custom-start {
            background-color: white;
            color: #0f766e;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
        }
        .btn-custom-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
            background-color: #f0fdfa;
            color: #0d9488;
        }
        .card-custom {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background-color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        }
        .card-custom:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(13, 148, 136, 0.3);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 1.5rem;
            transition: all 0.2s;
        }
        .icon-anemia { background-color: #fee2e2; color: #ef4444; }
        .icon-pankreas { background-color: #ffedd5; color: #f97316; }
        .icon-diabetes { background-color: #e0f2fe; color: #0284c7; }
    </style>
</head>
<body>
    @include('layouts.navigation')

    <div class="hero text-center d-flex align-items-center">
        <div class="container py-3">
            <h1 class="display-4 hero-title">Deteksi Dini Kesehatan Ibu Hamil<br><span style="color: #5eead4;">Lebih Mudah & Cepat</span></h1>
            <p class="lead hero-subtitle mt-3">Sistem pakar berbasis penalaran maju untuk mendeteksi risiko komplikasi selama masa kehamilan menggunakan <strong>Metode Forward Chaining</strong> berdasarkan basis aturan klinis yang terstruktur.</p>
            <div class="mt-4 pt-2">
                <a href="{{ route('konsultasi') }}" class="btn btn-custom-start btn-lg">
                    <i class="fas fa-stethoscope me-2"></i> Mulai Konsultasi Sekarang
                </a>
            </div>
        </div>
    </div>

    <div class="container py-5 mt-3">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 bg-teal-soft text-teal-800 fw-semibold mb-2" style="background-color: #ccfbf1; color: #0f766e;">CAKUPAN DETEKSI</span>
            <h2 class="fw-bold" style="letter-spacing: -0.02em;">Penyakit & Kelainan yang Didukung</h2>
            <p class="text-muted">Sistem pakar dirancang untuk menalar pola 50 gejala klinis ke dalam 3 kondisi medis kebidanan serta kategori tanda bahaya:</p>
        </div>

        <div class="row justify-content-center">
            <!-- Anemia -->
            <div class="col-md-3 mb-4">
                <div class="card card-custom h-100 p-3">
                    <div class="card-body">
                        <div class="icon-box icon-anemia">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="badge bg-danger-subtle text-danger mb-2 px-2 py-1">KODE: P01</div>
                        <h4 class="fw-bold mb-2">Anemia</h4>
                        <p class="text-secondary leading-relaxed small">Defisiensi sel darah merah atau hemoglobin yang menurunkan suplai oksigen ke jaringan ibu dan janin. Dianalisis melalui aturan inferensi R01-R04 dan aturan lama R16-R17.</p>
                    </div>
                </div>
            </div>

            <!-- Pankreatitis -->
            <div class="col-md-3 mb-4">
                <div class="card card-custom h-100 p-3">
                    <div class="card-body">
                        <div class="icon-box icon-pankreas">
                            <i class="fas fa-fire-alt"></i>
                        </div>
                        <div class="badge bg-warning-subtle text-warning mb-2 px-2 py-1">KODE: P02</div>
                        <h4 class="fw-bold mb-2">Pankreatitis</h4>
                        <p class="text-secondary leading-relaxed small">Inflamasi akut pada organ pankreas yang merupakan kegawatdaruratan obstetri. Dianalisis melalui aturan inferensi R05-R08 dan aturan lama R18-R19.</p>
                    </div>
                </div>
            </div>

            <!-- Diabetes -->
            <div class="col-md-3 mb-4">
                <div class="card card-custom h-100 p-3">
                    <div class="card-body">
                        <div class="icon-box icon-diabetes">
                            <i class="fas fa-syringe"></i>
                        </div>
                        <div class="badge bg-info-subtle text-info mb-2 px-2 py-1">KODE: P03</div>
                        <h4 class="fw-bold mb-2">Diabetes Melitus</h4>
                        <p class="text-secondary leading-relaxed small">Gangguan toleransi glukosa selama masa kehamilan (Gestasional) yang memengaruhi metabolisme janin. Dianalisis melalui aturan inferensi R09-R12 dan aturan lama R20-R21.</p>
                    </div>
                </div>
            </div>

            <!-- Tanda Bahaya (F04) -->
            <div class="col-md-3 mb-4">
                <div class="card card-custom h-100 p-3">
                    <div class="card-body">
                        <div class="icon-box" style="background-color: #fee2e2; color: #dc2626;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="badge bg-danger-subtle text-danger mb-2 px-2 py-1">KODE: P04</div>
                        <h4 class="fw-bold mb-2">Tanda Bahaya</h4>
                        <p class="text-secondary leading-relaxed small">Kesimpulan F04: gejala yang memerlukan evaluasi medis segera (tekanan darah menurun, urine berkurang, kesadaran menurun, napas berbau buah, dan lainnya). Dianalisis melalui aturan R13 (operator OR) dan R14-R15.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5 border-top">
        <p class="mb-0 small text-gray-400">&copy; 2026 Sistem Pakar Diagnosa Ibu Hamil | Dikembangkan dengan Metode Forward Chaining</p>
    </footer>

    <!-- Bootstrap JS for navbar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>