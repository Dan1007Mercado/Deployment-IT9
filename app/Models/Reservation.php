<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $primaryKey = 'reservation_id';
    
    protected $fillable = [
        'guest_id',
        'room_type_id',
        'check_in_date',
        'check_out_date',
        'num_guests',
        'total_amount',
        'status',
        'reservation_type',
        'booking_source',
        'special_requests',
        'expires_at'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'expires_at' => 'datetime'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'reservation_id');
    }

    // Get all rooms across all bookings for this reservation
    public function getAllRoomsAttribute()
    {
        $rooms = collect();
        foreach ($this->bookings as $booking) {
            $rooms = $rooms->merge($booking->rooms->map->room);
        }
        return $rooms->unique('room_id');
    }

    // Get room numbers for display
    public function getRoomNumbersAttribute()
    {
        $roomNumbers = [];
        foreach ($this->bookings as $booking) {
            foreach ($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        return implode(', ', array_unique($roomNumbers));
    }

    // Calculate nights
    public function getNightsAttribute()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    // Check if reservation is expired
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}