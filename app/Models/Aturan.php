<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aturan extends Model
{
    protected $table = 'aturan';

    protected $fillable = [
        'kode_aturan',
        'nama_aturan',
        'penyakit_id',
        'keterangan',
        'operator',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }

    public function gejalas(): BelongsToMany
    {
        return $this->belongsToMany(Gejala::class, 'aturan_gejala', 'aturan_id', 'gejala_id')->withTimestamps();
    }
}
