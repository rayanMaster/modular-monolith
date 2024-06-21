<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouseItems extends Model
{
    use HasFactory;

    protected $table = 'warehouse_items';
    protected $guarded = [];
}
