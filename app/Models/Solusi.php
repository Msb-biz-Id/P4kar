<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solusi extends Model
{
    protected $fillable = ['penyakit_id', 'judul_solusi', 'deskripsi_solusi'];

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }
}
