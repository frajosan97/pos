<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'branch_id',
        'user_id',
        'customer_id',
        'sale_type',
        'total_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user (salesperson) who handled the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault();
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
