<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_status',
        'sandbox_reference',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}