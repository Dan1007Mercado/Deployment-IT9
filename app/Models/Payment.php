<?php
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
        'stripe_payment_url', // Make sure this field exists
        'stripe_session_id',  // Make sure this field exists
        'payment_date'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    // Accessor for formatted amount
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }

    // Check if payment is successful
    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'completed' || $this->payment_status === 'paid';
    }
}