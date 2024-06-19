<?php

namespace App\Models;

use Database\Factories\ResourceFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property mixed|null $name
 * @property int $id
 * @property string|null $description
 * @property int $resource_category_id
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ResourceCategory|null $category
 *
 * @method static ResourceFactory factory($count = null, $state = [])
 * @method static Builder|Resource newModelQuery()
 * @method static Builder|Resource newQuery()
 * @method static Builder|Resource onlyTrashed()
 * @method static Builder|Resource query()
 * @method static Builder|Resource whereCreatedAt($value)
 * @method static Builder|Resource whereDeletedAt($value)
 * @method static Builder|Resource whereDescription($value)
 * @method static Builder|Resource whereId($value)
 * @method static Builder|Resource whereName($value)
 * @method static Builder|Resource whereResourceCategoryId($value)
 * @method static Builder|Resource whereStatus($value)
 * @method static Builder|Resource whereUpdatedAt($value)
 * @method static Builder|Resource withTrashed()
 * @method static Builder|Resource withoutTrashed()
 *
 * @mixin Eloquent
 */
class Resource extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function newFactory(): ResourceFactory
    {
        return ResourceFactory::new();
    }

    /**
     * @return BelongsTo<ResourceCategory,Resource>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category_id');
    }
}
