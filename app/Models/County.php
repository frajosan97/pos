<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $fillable = [
        'name'
    ];

    public function constituencies()
    {
        return $this->hasMany(Constituency::class);
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
