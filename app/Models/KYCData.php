<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KYCData extends Model
{
    // Define the fillable columns
    protected $fillable = [
        'user_id',
        'doc_type',
        'document',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
