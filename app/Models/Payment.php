<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'branch_id',
        'sale_id',
        'amount',
        'payment_method_id',
        'status',
        'payment_date',
        'reference_id',
    ];

    // Casting attributes
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function mpesaPayment()
    {
        return $this->belongsTo(MpesaPayment::class, 'reference_id');
    }
}
