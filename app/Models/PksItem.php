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
    'qty',
    'tarif',
    'subtotal'
];
public function katalog()
{
    
    return $this->belongsTo(Katalog::class);
}
}
