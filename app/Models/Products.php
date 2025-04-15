<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogue;
use App\Models\Branch;
use App\Models\Commission;

class Products extends Model {
    use HasFactory, SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    */
    protected $dates = [ 'deleted_at' ];

    /**
    * Fillable fields for mass assignment.
    */
    protected $fillable = [
        // Relationships
        'branch_id',
        'catalogue_id',

        // Identifiers & Info
        'barcode',
        'sku',
        'name',
        'description',
        'photo',

        // Pricing
        'buying_price',
        'normal_price',
        'whole_sale_price',
        'agent_price',
        'commission_on_sale',
        'discount',
        'tax_rate',

        // Stock & Inventory
        'quantity',
        'sold_quantity',
        'low_stock_threshold',
        'unit',
        'weight',

        // Status & Verification
        'status',
        'is_verified',
        'verified_by',
        'verified_at',

        // Audit Trail
        'created_by',
        'updated_by',
    ];

    /**
    * Relationships
    */

    // A product belongs to a catalogue

    public function catalogue() {
        return $this->belongsTo( Catalogue::class );
    }

    // A product belongs to a branch

    public function branch() {
        return $this->belongsTo( Branch::class );
    }

    // A product can have many commissions

    public function commissions() {
        return $this->hasMany( Commission::class );
    }

    /**
    * Custom Accessors & Scopes
    */

    // Accessor: check if product is low on stock

    public function getIsLowStockAttribute(): bool {
        return $this->quantity <= $this->low_stock_threshold;
    }

    // Scope: only active products

    public function scopeActive( $query ) {
        return $query->where( 'status', 'active' );
    }

    // Scope: only inactive products

    public function scopeInactive( $query ) {
        return $query->where( 'status', 'inactive' );
    }

    // Scope: only verified products

    public function scopeVerified( $query ) {
        return $query->where( 'is_verified', true );
    }

    // Scope: only unverified products

    public function scopeUnverified( $query ) {
        return $query->where( 'is_verified', false );
    }
}
