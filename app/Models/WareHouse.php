<?php

namespace App\Models;

use Database\Factories\WareHouseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WareHouse extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'warehouses';
    protected $guarded = [];


    public function address(): BelongsTo{
        return $this->belongsTo(Address::class);
    }

    public static function newFactory() : WareHouseFactory
    {
        return WareHouseFactory::new();
    }
}
