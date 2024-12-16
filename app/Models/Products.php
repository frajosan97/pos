<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'branch_id',
        'catalogue_id',
        'barcode',
        'name',
        'buying_price',
        'normal_price',
        'whole_sale_price',
        'agent_price',
        'tax_rate',
        'discount',
        'quantity',
        'sold_quantity',
        'low_stock_threshold',
        'sku',
        'photo',
        'unit',
        'weight',
        'status',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship with Catalogue.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class);
    }

    /**
     * Relationship with Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Accessor to check if the product is low on stock.
     */
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    /**
     * Scope to filter active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter inactive products.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
