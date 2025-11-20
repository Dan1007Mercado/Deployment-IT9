<?php
// app/Models/RoomType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $primaryKey = 'room_type_id';
    
    protected $fillable = [
        'type_name',
        'description',
        'capacity',
        'base_price',
        'amenities',
        'total_rooms',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'room_type_id');
    }
}