<?php

namespace App\Models;

use Database\Factories\DailyAttendanceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read Worksite|null $workSite
 *
 * @method static DailyAttendanceFactory factory($count = null, $state = [])
 * @method static Builder|DailyAttendance newModelQuery()
 * @method static Builder|DailyAttendance newQuery()
 * @method static Builder|DailyAttendance query()
 *
 * @mixin \Eloquent
 */
class DailyAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): DailyAttendanceFactory
    {
        return DailyAttendanceFactory::new();
    }

    /**
     * @return BelongsTo<Worksite,DailyAttendance>
     */
    public function worksite(): BelongsTo
    {
        return $this->belongsTo(Worksite::class);
    }
}
