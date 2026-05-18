<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = ['katalog_id','waktu','tarif'];

public function katalog()
{
    return $this->belongsTo(\App\Models\Katalog::class);
}

}
