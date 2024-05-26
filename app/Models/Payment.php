<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }
}
