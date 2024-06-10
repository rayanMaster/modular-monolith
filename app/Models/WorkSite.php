<?php

namespace App\Models;

use Database\Factories\WorkSiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class WorkSite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function newFactory(): WorkSiteFactory
    {
        return WorkSiteFactory::new();
    }

    public function subWorkSites():HasMany
    {
        return $this->hasMany(WorkSite::class,'parent_worksite_id');
    }
    public function parentWorksite():BelongsTo
    {
        return $this->belongsTo(WorkSite::class,'parent_worksite_id');
    }
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class)->withPivot(['quantity', 'price']);
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(WorkSiteCategory::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lastPayment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable')->latest('id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function address(): BelongsTo
    {
        return $this->BelongsTo(Address::class);
    }
}
