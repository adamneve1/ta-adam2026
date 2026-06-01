<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'pks_id',
        'nomor_invoice',
        'nominal',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'status',
        'kode_billing',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Relasi ke PKS (Perjanjian Kerja Sama)
     */
    public function pks()
    {
        return $this->belongsTo(Pks::class);
    }
}
