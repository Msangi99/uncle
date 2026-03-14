<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['fullname', 'class_id', 'year', 'student_type', 'fee_amount', 'contact', 'email'];

    public const TYPE_DAY = 'day';
    public const TYPE_BOARDING = 'boarding';

    public static function studentTypeOptions(): array
    {
        return [
            self::TYPE_DAY => __('Day'),
            self::TYPE_BOARDING => __('Boarding'),
        ];
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function otherPayments(): HasMany
    {
        return $this->hasMany(StudentOtherPayment::class);
    }
}
