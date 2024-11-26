<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Database\Factories\WorksiteFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $uuid
 * @property string $description
 * @property int|null $customer_id
 * @property int $warehouse_id
 * @property int|null $category_id
 * @property int|null $parent_worksite_id
 * @property string $starting_budget
 * @property string $cost
 * @property int|null $address_id
 * @property int $workers_count
 * @property string|null $receipt_date
 * @property string|null $starting_date
 * @property string|null $deliver_date
 * @property int $status_on_receive
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Address|null $address
 * @property-read WorksiteCategory|null $category
 * @property-read Customer|null $customer
 * @property-read User $manager
 * @property-read Payment|null $lastPayment
 * @property-read Worksite|null $parentWorksite
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Collection<int, Item> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Worksite> $subWorksites
 * @property-read int|null $sub_worksites_count
 * @property-read Model|Eloquent $model
 *
 * @method static WorksiteFactory factory($count = null, $state = [])
 * @method static Builder|Worksite newModelQuery()
 * @method static Builder|Worksite newQuery()
 * @method static Builder|Worksite query()
 * @method static Builder|Worksite whereAddressId($value)
 * @method static Builder|Worksite whereCategoryId($value)
 * @method static Builder|Worksite whereCost($value)
 * @method static Builder|Worksite whereCreatedAt($value)
 * @method static Builder|Worksite whereCustomerId($value)
 * @method static Builder|Worksite whereDeletedAt($value)
 * @method static Builder|Worksite whereDeliverDate($value)
 * @method static Builder|Worksite whereDescription($value)
 * @method static Builder|Worksite whereId($value)
 * @method static Builder|Worksite whereParentWorksiteId($value)
 * @method static Builder|Worksite whereReceiptDate($value)
 * @method static Builder|Worksite whereStartingBudget($value)
 * @method static Builder|Worksite whereStartingDate($value)
 * @method static Builder|Worksite whereStatusOnReceive($value)
 * @method static Builder|Worksite whereTitle($value)
 * @method static Builder|Worksite whereUpdatedAt($value)
 * @method static Builder|Worksite whereWorkersCount($value)
 *
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 *
 * @mixin Eloquent
 */
class Worksite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function newFactory(): WorksiteFactory
    {
        return WorksiteFactory::new();
    }

    /**
     * @return BelongsTo<User,Worksite>
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Worksite>
     */
    public function subWorksites(): HasMany
    {
        return $this->hasMany(Worksite::class, 'parent_worksite_id');
    }

    /**
     * @return BelongsTo<Worksite,Worksite>
     */
    public function parentWorksite(): BelongsTo
    {
        return $this->belongsTo(Worksite::class, 'parent_worksite_id');
    }

    /**
     * @return BelongsToMany<Item>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'worksite_items')->withPivot(['quantity', 'price']);
    }

    /**
     * @return BelongsTo<WorksiteCategory,Worksite>
     */
    public function category(): BelongsTo
    {
        return $this->BelongsTo(WorksiteCategory::class);
    }

    /**
     * @return BelongsTo<Customer,Worksite>
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
     * @return BelongsTo<Address,Worksite>
     */
    public function address(): BelongsTo
    {
        return $this->BelongsTo(Address::class);
    }

    /**
     * @return HasMany<Order>
     */
    public function pendingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'worksite_id')
            ->where(column: 'status', operator: '=', value: OrderStatusEnum::PENDING->value);
    }

    /**
     * @return MorphMany<Media>
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * @return BelongsTo<Warehouse>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
