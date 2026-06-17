<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'jenis_klien',
        'nama',
        'nama_narahubung',
        'no_narahubung',
        'email',
        'nama_penanggung_jawab',
        'jabatan',
        'agen_rri',
        'alamat',
        'catatan',
    ];

    public function pks()
    {
        return $this->hasMany(Pks::class);
    }
}
