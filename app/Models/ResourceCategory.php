<?php

namespace App\Models;

use Database\Factories\ResourceCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static ResourceCategoryFactory factory($count = null, $state = [])
 * @method static Builder|ResourceCategory newModelQuery()
 * @method static Builder|ResourceCategory newQuery()
 * @method static Builder|ResourceCategory onlyTrashed()
 * @method static Builder|ResourceCategory query()
 * @method static Builder|ResourceCategory whereCreatedAt($value)
 * @method static Builder|ResourceCategory whereDeletedAt($value)
 * @method static Builder|ResourceCategory whereId($value)
 * @method static Builder|ResourceCategory whereName($value)
 * @method static Builder|ResourceCategory whereStatus($value)
 * @method static Builder|ResourceCategory whereUpdatedAt($value)
 * @method static Builder|ResourceCategory withTrashed()
 * @method static Builder|ResourceCategory withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ResourceCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): ResourceCategoryFactory
    {
        return ResourceCategoryFactory::new();
    }
}
