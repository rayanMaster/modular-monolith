<?php

namespace App\Models;

use Database\Factories\WareHouseFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read Address|null $address
 *
 * @method static WareHouseFactory factory($count = null, $state = [])
 * @method static Builder|Warehouse newModelQuery()
 * @method static Builder|Warehouse newQuery()
 * @method static Builder|Warehouse onlyTrashed()
 * @method static Builder|Warehouse query()
 * @method static Builder|Warehouse withTrashed()
 * @method static Builder|Warehouse withoutTrashed()
 *
 * @property int $id
 * @property string $name
 * @property int|null $address_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static Builder<static>|Warehouse whereAddressId($value)
 * @method static Builder<static>|Warehouse whereCreatedAt($value)
 * @method static Builder<static>|Warehouse whereDeletedAt($value)
 * @method static Builder<static>|Warehouse whereId($value)
 * @method static Builder<static>|Warehouse whereName($value)
 * @method static Builder<static>|Warehouse whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Warehouse extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'warehouses';

    protected $guarded = [];

    /**
     * @return BelongsTo<Address,Warehouse>
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public static function newFactory(): WareHouseFactory
    {
        return WareHouseFactory::new();
    }
}
