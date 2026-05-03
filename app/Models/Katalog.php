<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Katalog extends Model
{
    protected $fillable = [
    'nama_layanan',
    'deskripsi'
];
    public function tarifs()
{
    return $this->hasMany(Tarif::class);
}
}


