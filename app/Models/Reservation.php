<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'game_room_id',
        'reservation_date',
        'start_time',
        'end_time',
        'duration_hours',
        'total_price',
        'status',
        'payment_status',
        'notes',
    ];

    /**
     * Get the user that owns the reservation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game room for the reservation
     */
    public function gameRoom()
    {
        return $this->belongsTo(GameRoom::class);
    }

    /**
     * Get the payment for the reservation
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Generate unique booking code
     */
    public static function generateBookingCode()
    {
        do {
            $code = 'LGR-' . strtoupper(substr(md5(time() . rand()), 0, 8));
        } while (self::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    /**
     * Get payment status badge
     */
    public function getPaymentStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'unpaid' => 'danger',
            'paid' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            default => 'Unknown'
        };
    }
}