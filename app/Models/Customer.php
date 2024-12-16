<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'name',
        'phone',
        'id_number',
        'county_id',
        'constituency_id',
        'ward_id',
        'location_id',
    ];

    // Relationships
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
