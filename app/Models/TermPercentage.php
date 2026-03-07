<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermPercentage extends Model
{
    protected $fillable = ['term_number', 'percent_paid'];

    protected $casts = [
        'percent_paid' => 'decimal:2',
    ];
}
