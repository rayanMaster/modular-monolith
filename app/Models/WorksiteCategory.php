<?php

namespace App\Models;

use Database\Factories\WorksiteCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\WorksiteCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorksiteCategory withoutTrashed()
 *
 * @mixin \Eloquent
 */
class WorksiteCategory extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function newFactory(): WorksiteCategoryFactory
    {
        return WorksiteCategoryFactory::new();
    }
}
