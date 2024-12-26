<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'sale_id',
        'catalogue_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
