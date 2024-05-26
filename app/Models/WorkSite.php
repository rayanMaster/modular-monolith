<?php

namespace App\Models;

use Database\Factories\WorkSiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class)->withPivot(['quantity','price']);
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(WorkSiteCategory::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function last_payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latest('id');
    }
}
