<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'commissions';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'product_id',
        'unit_commission',
        'quantity_sold',
        'commission_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user associated with the commission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the commission.
     */
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    /**
     * Scope to filter commissions by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter commissions by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
