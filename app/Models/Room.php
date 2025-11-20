<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $primaryKey = 'room_id';
    
    protected $fillable = [
        'room_type_id', 
        'room_number', 
        'floor', 
        'room_status',
        'image_path'
    ];

    // -----------------------------------------
    // Updated Relationships
    // -----------------------------------------

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    // Updated: Relationship to bookings through booking_rooms
    public function bookings()
    {
        return $this->hasMany(BookingRoom::class, 'room_id');
    }

    public function roomHolds()
    {
        return $this->hasMany(RoomHold::class, 'room_id');
    }

    // -----------------------------------------
    // Updated: Availability Logic
    // -----------------------------------------

    /**
     * Get all rooms available for a specific date range.
     */
    public static function getAvailableRooms($checkIn, $checkOut, $roomTypeId = null)
    {
        $query = self::where('room_status', 'available');

        // Filter by Room Type if provided
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }

        // 1. Exclude rooms with conflicting CONFIRMED BOOKINGS (UPDATED)
        $query->whereDoesntHave('bookings', function($q) use ($checkIn, $checkOut) {
            $q->whereHas('booking.reservation', function($sq) use ($checkIn, $checkOut) {
                $sq->where('check_in_date', '<', $checkOut)
                   ->where('check_out_date', '>', $checkIn)
                   ->whereNotIn('status', ['cancelled']);
            })
            ->whereHas('booking', function($bq) {
                $bq->whereNotIn('booking_status', ['cancelled', 'no-show', 'checked-out']);
            });
        });

        // 2. Exclude rooms with active TEMPORARY HOLDS
        $query->whereDoesntHave('roomHolds', function($q) use ($checkIn, $checkOut) {
            $q->where('expires_at', '>', now())
              ->where('check_in_date', '<', $checkOut)
              ->where('check_out_date', '>', $checkIn);
        });

        return $query->get();
    }

    /**
     * Check if this specific room instance is available.
     */
    public function isAvailableForDates($checkIn, $checkOut)
    {
        // Check conflicting bookings (UPDATED)
        $hasConflict = $this->bookings()
            ->whereHas('booking.reservation', function($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn)
                  ->whereNotIn('status', ['cancelled']);
            })
            ->whereHas('booking', function($bq) {
                $bq->whereNotIn('booking_status', ['cancelled', 'no-show', 'checked-out']);
            })
            ->exists();

        if ($hasConflict) return false;

        // Check conflicting holds
        $hasHold = $this->roomHolds()
            ->where('expires_at', '>', now())
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();

        return !$hasHold;
    }
}