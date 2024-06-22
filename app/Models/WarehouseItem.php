<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @method static \Database\Factories\WarehouseItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseItem withoutTrashed()
 * @mixin Eloquent
 */
class WarehouseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouse_items';
    protected $guarded = [];
}
