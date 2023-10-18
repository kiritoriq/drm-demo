<?php

namespace Domain\Shared\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Domain\Shared\Review\Models\Review;
use Domain\Shared\Service\Models\Service;
use Domain\Shared\State\Models\State;
use Domain\Shared\User\Builders\UserBuilder;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Contractor\Contractor;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Infrastructure\Notification\Models\Device;
use Infrastructure\Notification\Models\Notification;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, FilamentUser
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles,
        InteractsWithMedia,
        SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'brand_name',
        'phone',
        'office_address',
        'status',
        'whatsapp_number',
        'vendor_type',
        'company_description',
        'verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const COLLECTION_NAME = 'profile_picture';

    const BUSINESS_MEDIA_COLLECTION_NAME = 'business_logo';

    const MEDIA_CONVERSION_NAME = 'avatar';

    const BUSINESS_MEDIA_CONVERSION_NAME = 'thumb';

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    public function canManageSettings(): bool
    {
        return Role::hasAny([
            Role::admin,
        ]);
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(self::MEDIA_CONVERSION_NAME)
            ->width(130)
            ->height(130);
        
        $this->addMediaConversion(self::BUSINESS_MEDIA_CONVERSION_NAME)
            ->width(130)
            ->height(130);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_NAME);
        $this->addMediaCollection(self::BUSINESS_MEDIA_COLLECTION_NAME);
    }

    public function canAccessFilament(): bool
    {
        // return $this->hasAnyRole(Role::canAccessAdminPanel());
        return true;
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(\Domain\Shared\Ticket\Models\Ticket::class, 'assignee');
    }

    public function raisedTickets(): HasMany
    {
        return $this->hasMany(\Domain\Shared\Ticket\Models\Ticket::class, 'raised_by');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'user_id', 'id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'contractor_id', 'id');
    }

    public function locationCoverages(): BelongsToMany
    {
        return $this->belongsToMany(State::class, 'contractor_coverages', 'user_id', 'state_id');
    }

    public function offeredServices(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'contractor_services', 'user_id', 'service_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(related: Device::class);
    }

    public function mobileNotifications(): HasMany
    {
        return $this->hasMany(related: Notification::class);
    }

    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function attachedBranches(): BelongsToMany
    {
        return $this->belongsToMany(related: Branch::class, table: 'branches_users');
    }

    public function transactionHistories(): HasMany
    {
        return $this->hasMany(ContractorWallet::class, 'user_id', 'id');
    }
}
