<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassFee extends Model
{
    protected $fillable = ['class_id', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}
