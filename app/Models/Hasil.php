<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hasil extends Model
{
    protected $fillable = [
        'nama_pasien',
        'alamat_pasien',
        'penyakit_id',
        'aturan_terpilih_id',
        'solusi',
        'status_kesimpulan',
        'detail_inferensi',
        'probabilitas',
    ];
    protected $table = 'hasil';
    protected $casts = [
        'detail_inferensi' => 'array',
        'probabilitas' => 'array',
    ];

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }

    public function aturan(): BelongsTo
    {
        return $this->belongsTo(Aturan::class, 'aturan_terpilih_id');
    }
}
