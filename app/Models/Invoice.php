<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_BELUM_BILLING = 'belum_billing';
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    public const STATUS_PAID = 'paid';
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_LEGACY_UNPAID = 'Belum_Bayar';

    protected $fillable = [
        'pks_id',
        'nomor_invoice',
        'nominal',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'status',
        'kode_billing',
        'penyetor_nama',
        'penyetor_nip',
        'kepala_stasiun_nama',
        'kepala_stasiun_nip',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function getStatusAttribute($value)
    {
        return $value === self::STATUS_LEGACY_UNPAID ? self::STATUS_UNPAID : $value;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isUnpaid(): bool
    {
        return !$this->isPaid();
    }

    public function isBelumBilling(): bool
    {
        return !$this->isPaid() && blank($this->kode_billing);
    }

    public function isMenungguPembayaran(): bool
    {
        return !$this->isPaid() && filled($this->kode_billing);
    }

    public function isOverdue(): bool
    {
        return !$this->isPaid()
            && $this->tanggal_jatuh_tempo
            && $this->tanggal_jatuh_tempo->lt(Carbon::today());
    }

    public function overdueDays(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return (int) $this->tanggal_jatuh_tempo->diffInDays(Carbon::today());
    }

    public function statusLabel(): string
    {
        if ($this->isPaid()) {
            return 'Lunas';
        }

        return $this->isBelumBilling() ? 'Billing Belum Dibuat' : 'Menunggu Pembayaran';
    }

    public function statusBadgeClass(): string
    {
        if ($this->isPaid()) {
            return 'bg-success text-white';
        }

        return $this->isBelumBilling() ? 'bg-secondary text-white' : 'bg-warning text-dark';
    }

    /**
     * Relasi ke PKS (Perjanjian Kerja Sama)
     */
    public function pks()
    {
        return $this->belongsTo(Pks::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
