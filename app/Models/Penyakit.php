<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penyakit extends Model
{
    protected $fillable = ['kode_penyakit', 'nama_penyakit', 'keterangan'];
    protected $table = 'penyakit';

    public function aturans(): HasMany
    {
        return $this->hasMany(Aturan::class);
    }

    public function relasis(): HasMany
    {
        return $this->hasMany(Relasi::class);
    }

    public function hasils(): HasMany
    {
        return $this->hasMany(Hasil::class);
    }

    public function solusi(): HasMany
    {
        return $this->hasMany(Solusi::class);
    }
}
