<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gejala extends Model
{
    protected $fillable = ['nama_gejala'];
    protected $table = 'gejala';

    public function relasis(): HasMany
    {
        return $this->hasMany(Relasi::class);
    }

    public function penyakits()
    {
        return $this->belongsToMany(Penyakit::class, 'relasi', 'gejala_id', 'penyakit_id')->withPivot('bobot');
    }
}
