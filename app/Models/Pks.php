<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pks extends Model
{
    //
    protected $fillable = [
    'nomor',
    'judul',
    'nomor_referensi',
    'client_id',
    'deskripsi',
    'tanggal',
    'total'
];
    public function items()
{
    return $this->hasMany(PksItem::class);
}
public function client()
{
    return $this->belongsTo(Client::class);
}

}
