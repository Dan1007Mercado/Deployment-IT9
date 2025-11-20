<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';
    
    protected $fillable = [
        'reservation_id',
        'booking_status',
        'booking_date',
        'actual_check_in',
        'actual_check_out'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime'
    ];

    // Relationship to Reservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    // Relationship to Rooms through BookingRoom pivot
    public function rooms()
    {
        return $this->hasMany(BookingRoom::class, 'booking_id');
    }

    // Helper method to get room numbers
    public function getRoomNumbersAttribute()
    {
        return $this->rooms->map(function($bookingRoom) {
            return $bookingRoom->room->room_number;
        })->implode(', ');
    }

    // Helper method to calculate booking total
    public function getBookingTotalAttribute()
    {
        return $this->rooms->sum(function($bookingRoom) {
            return $bookingRoom->room_price;
        });
    }
}