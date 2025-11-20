<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RoomHold extends Model
{
    use HasFactory;

    protected $primaryKey = 'hold_id';
    
    protected $fillable = [
        'room_id',
        'session_id',
        'check_in_date',
        'check_out_date',
        'expires_at'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'expires_at' => 'datetime'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // Scope to get expired holds
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    // Scope to get active holds for a room
    public function scopeActiveForRoom($query, $roomId, $checkIn, $checkOut)
    {
        return $query->where('room_id', $roomId)
            ->where('expires_at', '>', now())
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function($q) use ($checkIn, $checkOut) {
                      $q->where('check_in_date', '<=', $checkIn)
                        ->where('check_out_date', '>=', $checkOut);
                  });
            });
    }
}