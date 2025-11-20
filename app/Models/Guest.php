<?php
// app/Models/Guest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $primaryKey = 'guest_id';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'email',
        'guest_type',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'guest_id');
    }
}