<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PksItem extends Model
{

protected $fillable = [
    'pks_id',
    'katalog_id',
    'waktu',
    'channel',
    'tanggal_mulai',
    'tanggal_selesai',
    'qty',
    'tarif',
    'subtotal'
];

protected $casts = [
    'tanggal_mulai' => 'date',
    'tanggal_selesai' => 'date',
    'tarif' => 'decimal:2',
    'subtotal' => 'decimal:2',
];
public function katalog()
{
    
    return $this->belongsTo(Katalog::class);
}
}
