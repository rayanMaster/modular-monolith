<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string|null $street
 * @property string|null $state
 * @property string|null $zipcode
 * @property int $city_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City|null $city
 * @method static \Database\Factories\AddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereZipcode($value)
 */
	class Address extends \Eloquent {}
}

namespace App\Models{use Illuminate\Database\Eloquent\Builder;
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CityFactory factory($count = null, $state = [])
 * @method static Builder|City newModelQuery()
 * @method static Builder|City newQuery()
 * @method static Builder|City query()
 * @method static Builder|City whereCreatedAt($value)
 * @method static Builder|City whereDeletedAt($value)
 * @method static Builder|City whereId($value)
 * @method static Builder|City whereName($value)
 * @method static Builder|City whereStatus($value)
 * @method static Builder|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{use Database\Factories\ContractorFactory;
/**
 *
 *
 * @property-read Address|null $address
 * @method static ContractorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contractor query()
 */
	class Contractor extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $phone
 * @property string $status
 * @property int|null $address_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Address|null $address
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property-read \App\Models\WorkSite|null $workSite
 * @method static \Database\Factories\DailyAttendanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|DailyAttendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyAttendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyAttendance query()
 */
	class DailyAttendance extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property int $status
 * @property int|null $job_title_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\WorkerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereJobTitleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $1
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Database\Factories\JobTitleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle where1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobTitle whereUpdatedAt($value)
 */
	class JobTitle extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $payable_type
 * @property int $payable_id
 * @property string $amount
 * @property string $payment_date
 * @property int $payment_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $payable
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup withoutTrashed()
 */
	class PermissionGroup extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property mixed|null $name
 * @property int $id
 * @property string|null $description
 * @property int $resource_category_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ResourceCategory|null $category
 * @method static \Database\Factories\ResourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereResourceCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource withoutTrashed()
 */
	class Resource extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\ResourceCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceCategory withoutTrashed()
 */
	class ResourceCategory extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int|null $customer_id
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Address|null $address
 * @property-read \App\Models\WorkSiteCategory|null $category
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\Payment|null $lastPayment
 * @property-read WorkSite|null $parentWorksite
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, WorkSite> $subWorkSites
 * @property-read int|null $sub_work_sites_count
 * @method static \Database\Factories\WorkSiteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereDeliverDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereParentWorksiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereReceiptDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereStartingBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereStartingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereStatusOnReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSite whereWorkersCount($value)
 */
	class WorkSite extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\WorkSiteCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteCategory withoutTrashed()
 */
	class WorkSiteCategory extends \Eloquent {}
}

namespace App\Models{
/**
 *
 *
 * @method static \Database\Factories\WorkSiteResourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteResource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteResource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkSiteResource query()
 */
	class WorkSiteResource extends \Eloquent {}
}

