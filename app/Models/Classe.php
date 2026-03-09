<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'is_exam_class'];

    protected $casts = [
        'is_exam_class' => 'boolean',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function classFee(): HasOne
    {
        return $this->hasOne(ClassFee::class, 'class_id');
    }
}
