<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'customer',
        'amount',
        'tax_amount',
        'status',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
