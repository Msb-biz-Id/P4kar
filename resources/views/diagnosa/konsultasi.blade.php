<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konsultasi - Sistem Pakar Diagnosa Ibu Hamil</title>
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
        .form-control-custom {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.2s ease-in-out;
        }
        .form-control-custom:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
            outline: none;
        }
        .symptom-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background-color: white;
            padding: 16px;
            height: 100%;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            position: relative;
        }
        .symptom-card:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
            transform: translateY(-1px);
        }
        .symptom-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 6px !important;
            border-color: #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .symptom-checkbox:checked {
            background-color: #0d9488 !important;
            border-color: #0d9488 !important;
        }
        .symptom-card.selected {
            border-color: #0d9488;
            background-color: #f0fdfa;
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.08);
        }
        .symptom-label-text {
            font-size: 0.925rem;
            font-weight: 500;
            color: #334155;
            line-height: 1.4;
            cursor: pointer;
        }
        .symptom-card.selected .symptom-label-text {
            color: #0f766e;
            font-weight: 600;
        }
        .btn-diagnosa {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
            transition: all 0.2s ease-in-out;
        }
        .btn-diagnosa:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(13, 148, 136, 0.3);
            color: white;
            opacity: 0.95;
        }
        .btn-outline-custom {
            border: 1px solid #cbd5e1;
            color: #64748b;
            background-color: white;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 6px 14px;
            transition: all 0.2s;
        }
        .btn-outline-custom:hover {
            border-color: #64748b;
            color: #334155;
            background-color: #f1f5f9;
        }
    </style>
</head>
<body>
    @include('layouts.navigation')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card main-card">
                    <div class="main-card-header d-flex align-items-center">
                        <i class="fas fa-notes-medical me-3" style="font-size: 1.75rem;"></i>
                        <div>
                            <h4 class="mb-1 fw-bold">Form Konsultasi Diagnosa</h4>
                            <p class="mb-0 small" style="color: rgba(255, 255, 255, 0.8);">Silakan isi data diri dan centang gejala yang sedang Anda rasakan</p>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('diagnosa.proses') }}" method="POST">
                            @csrf

                            <!-- Data Pasien -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-semibold text-secondary mb-2">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pasien" class="form-control form-control-custom" required placeholder="Masukkan nama Anda" value="{{ old('nama_pasien') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary mb-2">Alamat (Opsional)</label>
                                    <input type="text" name="alamat_pasien" class="form-control form-control-custom" placeholder="Masukkan alamat Anda" value="{{ old('alamat_pasien') }}">
                                </div>
                            </div>

                            <hr class="my-4 text-slate-200">

                            <!-- Gejala Section -->
                            <div class="mb-4">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-2">
                                    <div>
                                        <label class="form-label fw-bold text-secondary mb-0">Pilih Gejala yang Anda Rasakan <span class="text-danger">*</span></label>
                                        <span id="selectedCountBadge" class="badge ms-2" style="background-color: #ccfbf1; color: #0f766e;">0 Gejala Dipilih</span>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-custom" id="selectAllBtn"><i class="fas fa-check-double me-1"></i> Pilih Semua</button>
                                        <button type="button" class="btn btn-outline-custom" id="clearAllBtn"><i class="fas fa-trash-alt me-1"></i> Bersihkan</button>
                                    </div>
                                </div>

                                <!-- Bilah Pencarian Gejala -->
                                <div class="input-group mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #cbd5e1;"><i class="fas fa-search"></i></span>
                                    <input type="text" id="searchSymptom" class="form-control border-start-0 ps-0 form-control-custom" placeholder="Ketik kata kunci gejala untuk mencari... (contoh: lelah, pusing, nyeri)" style="border-radius: 0 12px 12px 0;">
                                </div>

                                <!-- List Gejala Grid -->
                                <div class="row" id="symptomsList">
                                    @foreach($gejala as $g)
                                    <div class="col-md-6 mb-3 symptom-card-wrapper">
                                        <div class="symptom-card" data-wrapper-id="{{ $g->id }}">
                                            <input class="form-check-input symptom-checkbox" type="checkbox" name="gejala[]" value="{{ $g->id }}" id="g{{ $g->id }}">
                                            <label class="symptom-label-text flex-grow-1 mb-0 d-flex align-items-center" for="g{{ $g->id }}">
                                                <span class="badge bg-secondary-subtle text-secondary me-2 px-2 py-1 fw-bold font-monospace">{{ $g->kode_gejala ?? 'G' . str_pad($g->id, 2, '0', STR_PAD_LEFT) }}</span>
                                                <span class="symptom-name">{{ $g->nama_gejala }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-diagnosa btn-lg w-100 py-3"><i class="fas fa-heartbeat me-2"></i> Proses Diagnosa Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Handling UI/UX Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchSymptom');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const clearAllBtn = document.getElementById('clearAllBtn');
            const checkboxes = document.querySelectorAll('.symptom-checkbox');
            const wrappers = document.querySelectorAll('.symptom-card-wrapper');

            const countBadge = document.getElementById('selectedCountBadge');

            function updateCounter() {
                const checkedCount = document.querySelectorAll('.symptom-checkbox:checked').length;
                countBadge.textContent = checkedCount + ' Gejala Dipilih';
                if (checkedCount > 0) {
                    countBadge.style.backgroundColor = '#ccfbf1';
                    countBadge.style.color = '#0f766e';
                } else {
                    countBadge.style.backgroundColor = '#f1f5f9';
                    countBadge.style.color = '#64748b';
                }
            }

            // Handle Card Selection State
            checkboxes.forEach(function(checkbox) {
                const card = checkbox.closest('.symptom-card');
                
                // Set initial selection states
                if (checkbox.checked) {
                    card.classList.add('selected');
                }

                // Listen for checkbox changes
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                    updateCounter();
                });

                // Let clicking the card label toggle the checked state
                card.addEventListener('click', function(e) {
                    if (e.target !== checkbox && e.target.tagName !== 'LABEL' && e.target.tagName !== 'INPUT') {
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            });

            // Live Search Filtering
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                wrappers.forEach(function(wrapper) {
                    const text = wrapper.querySelector('.symptom-name').textContent.toLowerCase();
                    if (text.includes(query)) {
                        wrapper.style.display = 'block';
                    } else {
                        wrapper.style.display = 'none';
                    }
                });
            });

            // Select All
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(function(checkbox) {
                    const wrapper = checkbox.closest('.symptom-card-wrapper');
                    if (wrapper.style.display !== 'none') {
                        checkbox.checked = true;
                        checkbox.closest('.symptom-card').classList.add('selected');
                    }
                });
                updateCounter();
            });

            // Clear All
            clearAllBtn.addEventListener('click', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                    checkbox.closest('.symptom-card').classList.remove('selected');
                });
                updateCounter();
            });

            updateCounter();
        });
    </script>

    <!-- Bootstrap JS for navbar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>