<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    // Define the table if it's different from the default naming convention
    protected $table = 'payment_methods';

    // Define the fillable columns
    protected $fillable = [
        'icon',
        'name',
        'image',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
