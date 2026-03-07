<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCredential extends Model
{
    protected $fillable = ['api_key', 'sender_id', 'url'];

    /**
     * Get the single SMS credentials row (singleton-style).
     */
    public static function getInstance(): ?self
    {
        return static::first();
    }
}
