<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'tanggal_pembayaran',
        'kode_billing',
        'ntpn',
        'ntb',
        'jumlah_pembayaran',
        'catatan',
        'bukti_pembayaran_path',
        'kwitansi_penyetor_nama',
        'kwitansi_penyetor_nip',
        'kwitansi_kepala_stasiun_nama',
        'kwitansi_kepala_stasiun_nip',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'jumlah_pembayaran' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
