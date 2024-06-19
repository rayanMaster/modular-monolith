<?php

namespace App\Models;

use Database\Factories\WorkSiteResourceFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static WorkSiteResourceFactory factory($count = null, $state = [])
 * @method static Builder|WorkSiteResource newModelQuery()
 * @method static Builder|WorkSiteResource newQuery()
 * @method static Builder|WorkSiteResource query()
 *
 * @mixin Eloquent
 * @property mixed $work_site_id
 * @property mixed $resource_id
 * @property mixed $price
 * @property mixed $quantity
 */
class WorkSiteResource extends Model
{
    use HasFactory;

    protected $table = 'work_site_resources';

    protected $guarded = [];

    protected static function newFactory(): WorkSiteResourceFactory
    {
        return WorkSiteResourceFactory::new();
    }
}
