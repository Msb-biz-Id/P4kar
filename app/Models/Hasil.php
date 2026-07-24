<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hasil extends Model
{
    protected $fillable = ['nama_pasien', 'alamat_pasien', 'penyakit_id', 'solusi', 'probabilitas'];
    protected $table = 'hasil';
    protected $casts = [
        'probabilitas' => 'array',
    ];

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }
}
