<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_room_id';
    
    protected $fillable = [
        'booking_id',
        'room_id',
        'room_price'
    ];

    // Relationship to Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    // Relationship to Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}