<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $fillable = [
        'name',
        'county_id',
        'constituency_id'
    ];

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
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
