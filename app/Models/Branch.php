<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'county_id',
        'constituency_id',
        'ward_id',
        'location_id'
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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
