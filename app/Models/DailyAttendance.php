<?php

namespace App\Models;

use Database\Factories\DailyAttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAttendance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): DailyAttendanceFactory
    {
        return DailyAttendanceFactory::new();
    }

    public function workSite(): BelongsTo
    {
        return $this->belongsTo(WorkSite::class);
    }
}
