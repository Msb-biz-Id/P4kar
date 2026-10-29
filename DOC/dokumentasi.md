# DOKUMENTASI KODE PROGRAM SISTEM PAKAR (SKRIPSI)

Dokumen ini berisi potongan kode program (Source Code) untuk kebutuhan penyusunan bab 4 skripsi pada sistem pakar diagnosa penyakit ibu hamil menggunakan metode **Forward Chaining (Basis Aturan IF-THEN)**:

---

### Segmen 4.1 Tambah Penyakit
File: `app/Http/Controllers/PenyakitController.php`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'kode_penyakit' => 'required|string|max:10|unique:penyakit,kode_penyakit',
        'nama_penyakit' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
    ]);

    $validated['kode_penyakit'] = strtoupper($validated['kode_penyakit']);

    Penyakit::create($validated);

    return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil ditambahkan!');
}
```

---

### Segmen 4.2 Edit Penyakit
File: `app/Http/Controllers/PenyakitController.php`

```php
public function update(Request $request, Penyakit $penyakit)
{
    $validated = $request->validate([
        'kode_penyakit' => ['required', 'string', 'max:10', Rule::unique('penyakit')->ignore($penyakit->id)],
        'nama_penyakit' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
    ]);

    $validated['kode_penyakit'] = strtoupper($validated['kode_penyakit']);

    $penyakit->update($validated);

    return redirect()->route('admin.penyakit.index')->with('success', 'Penyakit berhasil diperbarui!');
}
```

---

### Segmen 4.3 Tambah Gejala
File: `app/Http/Controllers/GejalaController.php`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'kode_gejala' => 'required|string|max:10|unique:gejala,kode_gejala',
        'nama_gejala' => 'required|string|max:255',
    ]);

    $validated['kode_gejala'] = strtoupper($validated['kode_gejala']);

    Gejala::create($validated);

    return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil ditambahkan!');
}
```

---

### Segmen 4.4 Edit Gejala
File: `app/Http/Controllers/GejalaController.php`

```php
public function update(Request $request, Gejala $gejala)
{
    $validated = $request->validate([
        'kode_gejala' => ['required', 'string', 'max:10', Rule::unique('gejala')->ignore($gejala->id)],
        'nama_gejala' => 'required|string|max:255',
    ]);

    $validated['kode_gejala'] = strtoupper($validated['kode_gejala']);

    $gejala->update($validated);

    return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil diperbarui!');
}
```

---

### Segmen 4.5 Tambah Aturan (Basis Pengetahuan Forward Chaining)
File: `app/Http/Controllers/AturanController.php`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'kode_aturan' => 'required|string|max:20|unique:aturan,kode_aturan',
        'nama_aturan' => 'required|string|max:255',
        'penyakit_id' => 'required|exists:penyakit,id',
        'gejala_ids' => 'required|array|min:1',
        'gejala_ids.*' => 'exists:gejala,id',
        'keterangan' => 'nullable|string',
    ]);

    $aturan = Aturan::create([
        'kode_aturan' => strtoupper($validated['kode_aturan']),
        'nama_aturan' => $validated['nama_aturan'],
        'penyakit_id' => $validated['penyakit_id'],
        'keterangan' => $validated['keterangan'] ?? null,
        'is_active' => true,
    ]);

    $aturan->gejalas()->sync($validated['gejala_ids']);

    return redirect()->route('admin.aturan.index')->with('success', 'Aturan baru berhasil ditambahkan!');
}
```

---

### Segmen 4.6 Edit Aturan
File: `app/Http/Controllers/AturanController.php`

```php
public function update(Request $request, Aturan $aturan)
{
    $validated = $request->validate([
        'kode_aturan' => ['required', 'string', 'max:20', Rule::unique('aturan')->ignore($aturan->id)],
        'nama_aturan' => 'required|string|max:255',
        'penyakit_id' => 'required|exists:penyakit,id',
        'gejala_ids' => 'required|array|min:1',
        'gejala_ids.*' => 'exists:gejala,id',
        'keterangan' => 'nullable|string',
        'is_active' => 'nullable|boolean',
    ]);

    $aturan->update([
        'kode_aturan' => strtoupper($validated['kode_aturan']),
        'nama_aturan' => $validated['nama_aturan'],
        'penyakit_id' => $validated['penyakit_id'],
        'keterangan' => $validated['keterangan'] ?? null,
        'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
    ]);

    $aturan->gejalas()->sync($validated['gejala_ids']);

    return redirect()->route('admin.aturan.index')->with('success', 'Aturan berhasil diperbarui!');
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

### Segmen 4.9 Mesin Inferensi Forward Chaining & Proses Diagnosa
File: `app/Services/ForwardChainingService.php` & `app/Http/Controllers/DiagnosaController.php`

```php
// app/Services/ForwardChainingService.php
public function diagnose(array $gejalaIds): array
{
    // 1. Inisialisasi Working Memory
    $faktaGejala = Gejala::whereIn('id', $gejalaIds)->get()->keyBy('id');
    $faktaIds = $faktaGejala->keys()->toArray();

    // 2. Pattern Matching terhadap Basis Aturan
    $aturans = Aturan::with(['penyakit', 'gejalas'])->where('is_active', true)->get();
    $evaluasiAturan = [];
    $exactMatches = [];

    foreach ($aturans as $aturan) {
        $premisIds = $aturan->gejalas->pluck('id')->toArray();
        $totalPremis = count($premisIds);
        $cocokIds = array_values(array_intersect($faktaIds, $premisIds));
        $cocokCount = count($cocokIds);
        $matchRatio = $totalPremis > 0 ? ($cocokCount / $totalPremis) : 0;
        $isExactMatch = ($totalPremis > 0 && $cocokCount === $totalPremis);

        $evalItem = [
            'aturan_id' => $aturan->id,
            'kode_aturan' => $aturan->kode_aturan,
            'nama_penyakit' => $aturan->penyakit?->nama_penyakit,
            'total_premis' => $totalPremis,
            'cocok_count' => $cocokCount,
            'match_percentage' => round($matchRatio * 100, 2),
            'is_exact_match' => $isExactMatch,
        ];

        $evaluasiAturan[] = $evalItem;
        if ($isExactMatch) {
            $exactMatches[] = $evalItem;
        }
    }

    // 3. Conflict Resolution
    if (count($exactMatches) > 0) {
        $statusKesimpulan = 'cocok_penuh';
        $aturanTerpilih = $exactMatches[0];
        $penyakitTerpilih = Penyakit::find($aturanTerpilih['penyakit_id']);
    } else {
        $top = $evaluasiAturan[0] ?? null;
        if ($top && $top['match_percentage'] >= 50.0) {
            $statusKesimpulan = 'parsial';
            $penyakitTerpilih = Penyakit::find($top['penyakit_id']);
        } else {
            $statusKesimpulan = 'tidak_cocok';
            $penyakitTerpilih = null;
        }
    }

    return [
        'penyakit_id' => $penyakitTerpilih?->id,
        'status_kesimpulan' => $statusKesimpulan,
        'detail_inferensi' => [...],
    ];
}
```
