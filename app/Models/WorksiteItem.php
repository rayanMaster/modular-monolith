<?php

namespace App\Models;

use Database\Factories\WorksiteItemFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static WorksiteItemFactory factory($count = null, $state = [])
 * @method static Builder|WorksiteItem newModelQuery()
 * @method static Builder|WorksiteItem newQuery()
 * @method static Builder|WorksiteItem query()
 *
 * @property mixed $worksite_id
 * @property mixed $item_id
 * @property mixed $price
 * @property mixed $quantity
 *
 * @mixin Eloquent
 */
class WorksiteItem extends Model
{
    use HasFactory;

    protected $table = 'worksite_items';

    protected $guarded = [];

    protected static function newFactory(): WorksiteItemFactory
    {
        return WorksiteItemFactory::new();
    }
}
