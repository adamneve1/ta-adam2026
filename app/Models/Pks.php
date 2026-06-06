<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pks extends Model
{
    protected $fillable = [
        'nomor',
        'judul',
        'nomor_referensi',
        'client_id',
        'deskripsi',
        'tanggal',
        'total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PksItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTanggalTerakhirPenyiaranAttribute()
    {
        return $this->tanggal_selesai_terakhir
            ?? $this->tanggal_mulai_terakhir
            ?? $this->tanggal;
    }

    public function getTanggalJatuhTempoInvoiceAttribute(): ?string
    {
        if (!$this->tanggal_terakhir_penyiaran) {
            return null;
        }

        return Carbon::parse($this->tanggal_terakhir_penyiaran)
            ->addDays(28)
            ->toDateString();
    }
}
