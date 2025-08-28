<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'ref_no',
        'posting_date',
        'memo',
        'status',
        'created_by',
    ];

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class);
    }
}
