<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relasi extends Model
{
    protected $fillable = ['penyakit_id', 'gejala_id', 'bobot'];
    protected $table = 'relasi';

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }

    public function gejala(): BelongsTo
    {
        return $this->belongsTo(Gejala::class);
    }
}
