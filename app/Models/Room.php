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

    public function bookings()
    {
        return $this->hasMany(BookingRoom::class, 'room_id');
    }

    public function roomHolds()
    {
        return $this->hasMany(RoomHold::class, 'room_id');
    }

    // -----------------------------------------
    // NEW: Availability Scope (Clean & Reusable)
    // -----------------------------------------

    /**
     * Scope to get rooms available between specific dates
     */
    public function scopeAvailableBetween($query, $checkIn, $checkOut)
    {
        return $query->where('room_status', 'available')
            ->whereDoesntHave('bookings', function($q) use ($checkIn, $checkOut) {
                $q->whereHas('booking.reservation', function($r) use ($checkIn, $checkOut) {
                    $r->where('check_in_date', '<', $checkOut)
                      ->where('check_out_date', '>', $checkIn)
                      ->whereNotIn('status', ['cancelled', 'checked-out', 'no-show']);
                })
                ->whereHas('booking', function($bq) {
                    $bq->whereNotIn('booking_status', ['cancelled', 'no-show', 'checked-out']);
                });
            });
    }

    /**
     * Get all rooms available for a specific date range.
     * DEPRECATED: Use scopeAvailableBetween() instead
     */
    public static function getAvailableRooms($checkIn, $checkOut, $roomTypeId = null)
    {
        $query = self::availableBetween($checkIn, $checkOut);

        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }

        return $query->get();
    }

    /**
     * Check if this specific room instance is available.
     */
    public function isAvailableForDates($checkIn, $checkOut)
    {
        // Check conflicting bookings
        $hasConflict = $this->bookings()
            ->whereHas('booking.reservation', function($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn)
                  ->whereNotIn('status', ['cancelled', 'checked-out', 'no-show']);
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