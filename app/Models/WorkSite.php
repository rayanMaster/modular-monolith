<?php

namespace App\Models;

use Database\Factories\WorkSiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * @return HasMany<WorkSite>
     */
    public function subWorkSites(): HasMany
    {
        return $this->hasMany(WorkSite::class, 'parent_work_site_id');
    }

    /**
     * @return BelongsTo<WorkSite,WorkSite>
     */
    public function parentWorksite(): BelongsTo
    {
        return $this->belongsTo(WorkSite::class, 'parent_work_site_id');
    }

    /**
     * @return BelongsToMany<Resource>
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'work_site_resources')->withPivot(['quantity', 'price']);
    }

    /**
     * @return BelongsTo<WorkSiteCategory,WorkSite>
     */
    public function category(): BelongsTo
    {
        return $this->BelongsTo(WorkSiteCategory::class);
    }

    /**
     * @return BelongsTo<Customer,WorkSite>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return MorphOne<Payment>
     */
    public function lastPayment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable')->latest('id');
    }

    /**
     * @return MorphMany<Payment>
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * @return BelongsTo<Address,WorkSite>
     */
    public function address(): BelongsTo
    {
        return $this->BelongsTo(Address::class);
    }
}
