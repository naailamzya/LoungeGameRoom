<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'payment_code',
        'payment_method',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /**
     * Get the reservation that owns the payment
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Generate unique payment code
     */
    public static function generatePaymentCode()
    {
        do {
            $code = 'PAY-' . strtoupper(substr(md5(time() . rand()), 0, 10));
        } while (self::where('payment_code', $code)->exists());

        return $code;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'ewallet' => 'E-Wallet',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            default => 'Unknown'
        };
    }
}