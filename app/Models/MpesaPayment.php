<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaPayment extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'transaction_id',
        'name',
        'amount',
        'phone',
        'shortcode',
        'status',
        'use_status',
        'response_payload',
    ];

    // Casting attributes
    protected $casts = [
        'response_payload' => 'array',
    ];

    // Relationships
    public function payment()
    {
        return $this->hasOne(Payment::class, 'reference_id');
    }
}
