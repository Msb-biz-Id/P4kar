# DOKUMENTASI KODE PROGRAM SISTEM PAKAR (SKRIPSI)

Dokumen ini berisi potongan kode program (Source Code) untuk kebutuhan penyusunan bab 4 skripsi pada sistem pakar diagnosa penyakit ibu hamil menggunakan metode Naive Bayes Classifier:

---

### Segmen 4.1 Tambah Penyakit
File: `app/Http/Controllers/PenyakitController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'nama_penyakit' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
    ]);

    Penyakit::create($request->only('nama_penyakit', 'keterangan'));

    return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil ditambahkan!');
}
```

---

### Segmen 4.2 Edit Penyakit
File: `app/Http/Controllers/PenyakitController.php`

```php
public function update(Request $request, Penyakit $penyakit)
{
    $request->validate([
        'nama_penyakit' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
    ]);

    $penyakit->update($request->only('nama_penyakit', 'keterangan'));

    return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil diperbarui!');
}
```

---

### Segmen 4.3 Tambah Gejala
File: `app/Http/Controllers/GejalaController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'nama_gejala' => 'required|string|max:255',
    ]);

    Gejala::create($request->only('nama_gejala'));

    return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil ditambahkan!');
}
```

---

### Segmen 4.4 Edit Gejala
File: `app/Http/Controllers/GejalaController.php`

```php
public function update(Request $request, Gejala $gejala)
{
    $request->validate([
        'nama_gejala' => 'required|string|max:255',
    ]);

    $gejala->update($request->only('nama_gejala'));

    return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil diperbarui!');
}
```

---

### Segmen 4.5 Tambah Relasi
File: `app/Http/Controllers/RelasiController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'penyakit_id' => 'required|exists:penyakit,id',
        'gejala_id' => 'required|exists:gejala,id',
        'bobot' => 'required|numeric|min:0|max:1',
    ]);

    Relasi::create($request->only('penyakit_id', 'gejala_id', 'bobot'));

    return redirect()->route('admin.relasi.index')->with('success', 'Relasi berhasil ditambahkan!');
}
```

---

### Segmen 4.6 Edit Relasi
File: `app/Http/Controllers/RelasiController.php`

```php
public function update(Request $request, Relasi $relasi)
{
    $request->validate([
        'penyakit_id' => 'required|exists:penyakit,id',
        'gejala_id' => 'required|exists:gejala,id',
        'bobot' => 'required|numeric|min:0|max:1',
    ]);

    $relasi->update($request->only('penyakit_id', 'gejala_id', 'bobot'));

    return redirect()->route('admin.relasi.index')->with('success', 'Relasi berhasil diperbarui!');
}
```

---

### Segmen 4.7 Tambah Solusi
File: `app/Http/Controllers/SolusiController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'penyakit_id' => 'required|exists:penyakit,id',
        'judul_solusi' => 'required|string|max:255',
        'deskripsi_solusi' => 'required|string',
    ]);

    Solusi::create($request->only('penyakit_id', 'judul_solusi', 'deskripsi_solusi'));

    return redirect()->route('admin.solusi.index')->with('success', 'Solusi berhasil ditambahkan!');
}
```

---

### Segmen 4.8 Edit Solusi
File: `app/Http/Controllers/SolusiController.php`

```php
public function update(Request $request, Solusi $solusi)
{
    $request->validate([
        'penyakit_id' => 'required|exists:penyakit,id',
        'judul_solusi' => 'required|string|max:255',
        'deskripsi_solusi' => 'required|string',
    ]);

    $solusi->update($request->only('penyakit_id', 'judul_solusi', 'deskripsi_solusi'));

    return redirect()->route('admin.solusi.index')->with('success', 'Solusi berhasil diperbarui!');
}
```

---

### Segmen 4.9 Perhitungan dan Hasil Diagnosa
File: `app/Http/Controllers/DiagnosaController.php`

```php
public function proses(Request $request)
{
    $validated = $request->validate([
        'nama_pasien' => 'required|string|max:255',
        'alamat_pasien' => 'nullable|string|max:255',
        'gejala' => 'required|array|min:1',
        'gejala.*' => 'exists:gejala,id',
    ]);

    $gejalaIds = $request->gejala;
    $penyakit = Penyakit::with(['relasis', 'solusi'])->get();

    // 1. Perhitungan Naive Bayes dalam log-space untuk mencegah underflow
    $totalPenyakit = $penyakit->count();
    $hasilPerhitungan = [];
    foreach ($penyakit as $p) {
        // Log prior: log(P(Disease))
        $logPrior = log(1 / $totalPenyakit);

        // Log likelihood: log(P(Symptoms|Disease))
        $relasiMap = $p->relasis->pluck('bobot', 'gejala_id');
        $logLikelihood = 0;

        foreach ($gejalaIds as $gejalaId) {
            $bobot = $relasiMap->get($gejalaId, 0.01);
            if ($bobot <= 0) {
                $logLikelihood = -INF;
                break;
            }
            $logLikelihood += log($bobot);
        }

        $hasilPerhitungan[$p->id] = [
            'id' => $p->id,
            'nama_penyakit' => $p->nama_penyakit,
            'log_posterior' => $logLikelihood + $logPrior,
        ];
    }

    // 2. Normalisasi probabilitas menggunakan softmax (stabilisasi log-sum-exp)
    $maxLog = max(array_column($hasilPerhitungan, 'log_posterior'));
    $expSum = 0;

    if ($maxLog === -INF) {
        foreach ($hasilPerhitungan as &$item) {
            $item['probabilitas'] = 1 / $totalPenyakit;
        }
        unset($item);
    } else {
        foreach ($hasilPerhitungan as &$item) {
            $item['probabilitas'] = exp($item['log_posterior'] - $maxLog);
            $expSum += $item['probabilitas'];
        }
        unset($item);

        foreach ($hasilPerhitungan as &$item) {
            $item['probabilitas'] = $expSum > 0 ? $item['probabilitas'] / $expSum : 0;
        }
        unset($item);
    }

    // Urutkan hasil dari probabilitas terbesar ke terkecil
    usort($hasilPerhitungan, function ($a, $b) {
        return $b['probabilitas'] <=> $a['probabilitas'];
    });

    $topResult = $hasilPerhitungan[0] ?? null;
    
    // 3. Penerapan Aturan Ambang Batas (Threshold) 50%
    if ($topResult && $topResult['probabilitas'] >= 0.5) {
        $penyakitId = $topResult['id'];
        $p = $penyakit->firstWhere('id', $penyakitId);
        $solusiList = $p?->solusi?->pluck('deskripsi_solusi')?->implode("\n\n");
        $solusi = $solusiList ?: ($p?->keterangan ?? null);
    } else {
        $penyakitId = null;
        $solusi = 'Gejala yang Anda rasakan tidak cukup spesifik untuk mendiagnosa penyakit tertentu (semua probabilitas < 50%). Silakan konsultasikan lebih lanjut dengan dokter kandungan atau bidan.';
    }

    // Simpan rincian hasil ke database
    $probabilitas = [];
    foreach ($hasilPerhitungan as $item) {
        $probabilitas[] = [
            'nama_penyakit' => $item['nama_penyakit'],
            'probabilitas' => round($item['probabilitas'], 6),
        ];
    }

    $hasil = Hasil::create([
        'nama_pasien' => $validated['nama_pasien'],
        'alamat_pasien' => $validated['alamat_pasien'] ?? null,
        'penyakit_id' => $penyakitId,
        'solusi' => $solusi,
        'probabilitas' => $probabilitas,
    ]);

    return view('diagnosa.hasil', compact('hasil', 'hasilPerhitungan'));
}
```
