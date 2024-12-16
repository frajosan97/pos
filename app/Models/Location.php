<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'county_id',
        'constituency_id',
        'ward_id'
    ];

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

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
