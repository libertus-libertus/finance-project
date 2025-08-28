<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosingPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'is_locked',
        'locked_by',
        'locked_at',
    ];
}
