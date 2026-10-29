<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gejala extends Model
{
    protected $fillable = ['kode_gejala', 'nama_gejala'];
    protected $table = 'gejala';

    public function aturans(): BelongsToMany
    {
        return $this->belongsToMany(Aturan::class, 'aturan_gejala', 'gejala_id', 'aturan_id');
    }

    public function relasis(): HasMany
    {
        return $this->hasMany(Relasi::class);
    }

    public function penyakits()
    {
        return $this->belongsToMany(Penyakit::class, 'relasi', 'gejala_id', 'penyakit_id')->withPivot('bobot');
    }
}
