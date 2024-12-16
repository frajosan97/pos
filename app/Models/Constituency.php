<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{
    protected $fillable = [
        'name',
        'county_id'
    ];

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
