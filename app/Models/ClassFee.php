<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Student;

class ClassFee extends Model
{
    protected $fillable = ['class_id', 'amount', 'amount_day', 'amount_boarding'];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_day' => 'decimal:2',
        'amount_boarding' => 'decimal:2',
    ];

    /**
     * Ada inayotakiwa kwa aina ya mwanafunzi (day au boarding).
     * Fallback to amount if amount_day/amount_boarding is null (backward compat).
     */
    public function getAmountForStudentType(string $studentType): float
    {
        $isBoarding = strtolower($studentType) === Student::TYPE_BOARDING;
        if ($isBoarding && $this->amount_boarding !== null) {
            return (float) $this->amount_boarding;
        }
        if (! $isBoarding && $this->amount_day !== null) {
            return (float) $this->amount_day;
        }
        return (float) ($this->amount ?? 0);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}
