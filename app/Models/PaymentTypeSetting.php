<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTypeSetting extends Model
{
    protected $table = 'payment_type_settings';

    protected $fillable = [
        'tahadhari',
        'maktaba',
        'ream',
        'maendeleo_ya_shule',
        'taaluma',
        'pesa_ya_mtihani_wa_taifa',
    ];

    protected $casts = [
        'tahadhari' => 'decimal:2',
        'maktaba' => 'decimal:2',
        'ream' => 'decimal:2',
        'maendeleo_ya_shule' => 'decimal:2',
        'taaluma' => 'decimal:2',
        'pesa_ya_mtihani_wa_taifa' => 'decimal:2',
    ];

    /**
     * Get the single settings row (one row per app).
     */
    public static function getInstance(): self
    {
        $row = self::first();
        if ($row) {
            return $row;
        }
        return self::create([
            'tahadhari' => 0,
            'maktaba' => 0,
            'ream' => 0,
            'maendeleo_ya_shule' => 0,
            'taaluma' => 0,
            'pesa_ya_mtihani_wa_taifa' => 0,
        ]);
    }

    /** Keys used in forms and student_other_payments.payment_type */
    public static function typeKeys(): array
    {
        return [
            'tahadhari' => __('Tahadhari'),
            'maktaba' => __('Maktaba'),
            'ream' => __('Ream'),
            'maendeleo_ya_shule' => __('Maendeleo ya shule'),
            'taaluma' => __('Taaluma'),
            'pesa_ya_mtihani_wa_taifa' => __('Pesa ya mtihani wa taifa (kwa darasa la mtihani)'),
        ];
    }
}
