<?php

namespace App\Models;

use App\Notifications\UserVerifyNotification;
use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasPrimaryUuid;
    use HasRoles;
    use HasTimestamps;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'blocked_by_admin',
        'email_verified_at',
        'updated_at',
        'last_login_date',
        'not_verified_at',
        'type_thuiskok',
        'kvk_naam',
        'btw_nummer',
        'dac7_warning_email_sent',
        'dac7_required_email_sent',
        'kvk_nummer',
        'rsin',
        'vestigingsnummer',
        'bsn',
        'nvwa_nummer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relatie namen die moeten worden verwijderd bij het verwijderen van een gebruiker.
     * NIET $relations gebruiken omdat dit conflicteert met Eloquent's interne property.
     */
    protected array $deletableRelations = [
        'cook',
        'order',
        'cookMailingList',
        'userProfile',
        'image',
    ];

    public function getProfileImagePath(): string
    {
        $imageRepository = app(\App\Repositories\ImageRepository::class);
        $mainImage = $imageRepository->findMainProfileImage($this->getUuid());

        if ($mainImage && file_exists(public_path($mainImage->getCompletePath()))) {
            return $mainImage->getCompletePath();
        }

        return url('/img/kok.png');
    }

    public function sendEmailVerificationNotification()
    {

        $this->notify(new UserVerifyNotification($this));

    }

    public function dac7Establishment()
    {
        return $this->hasOne(Dac7Establishment::class, 'user_id', 'uuid');
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->email_verified_at;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function isBlockedByAdmin(): ?bool
    {
        return $this->blocked_by_admin;
    }

    public function dish(): HasMany
    {
        return $this->hasMany(
            Dish::class,
            'user_uuid',
            'uuid'
        );
    }

    public function trashedDish(): HasMany
    {
        return $this->hasMany(
            Dish::class,
            'user_uuid',
            'uuid'
        )->with('trashedAdverts')->withTrashed();
    }

    public function cook(): HasOne
    {
        return $this->hasOne(Cook::class, 'user_uuid', 'uuid');
    }

    public function trashedCook(): HasOne
    {
        return $this->hasOne(Cook::class, 'user_uuid', 'uuid')
            ->withTrashed();
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class, 'user_uuid', 'uuid');
    }

    public function trashedOrder(): HasMany
    {
        return $this->hasMany(Order::class, 'user_uuid', 'uuid')
            ->withTrashed();
    }

    public function cookMailingList(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'mailing_lists',
            'cook_uuid',
            'client_uuid',
            'uuid',
            'uuid'
        )->using(new class extends Pivot
        {
            use HasPrimaryUuid;
        })->withTimestamps();
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(
            UserProfile::class,
            'user_uuid',
            'uuid'
        );
    }

    public function trashedUserProfile(): HasOne
    {
        return $this->hasOne(
            UserProfile::class,
            'user_uuid',
            'uuid'
        )
            ->withTrashed();
    }

    public function image(): HasOne
    {
        return $this->hasOne(
            Image::class,
            'user_uuid',
            'uuid'
        )
            ->where('type_id', Image::PROFILE_IMAGE)
            ->where('main_picture', true);
    }

    public function trashedImage(): HasOne
    {
        return $this->hasOne(
            Image::class,
            'user_uuid',
            'uuid'
        )
            ->where('type_id', Image::PROFILE_IMAGE)
            ->where('main_picture', true)
            ->withTrashed();
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'user_uuid', 'uuid');
    }

    public function trashedImages(): HasMany
    {
        return $this->hasMany(Image::class, 'user_uuid', 'uuid')
            ->withTrashed();
    }

    public function delete(): ?bool
    {
        foreach ($this->deletableRelations as $relation) {
            if ($relation === 'order') {
                continue;
            }
            $this->$relation()?->delete();
        }

        return parent::delete();
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'user_uuid', 'uuid');
    }

    public function trashedClient(): HasOne
    {
        return $this->hasOne(Client::class, 'user_uuid', 'uuid')
            ->withTrashed();
    }

    public function privacy(): HasOne
    {
        return $this->hasOne(Privacy::class, 'user_uuid', 'uuid');
    }

    public function trashedPrivacy(): HasOne
    {
        return $this->hasOne(Privacy::class, 'user_uuid', 'uuid')
            ->withTrashed();
    }

    public function banking(): HasOne
    {
        return $this->hasOne(Banking::class, 'user_uuid', 'uuid');
    }

    public function trashedBanking(): HasOne
    {
        return $this->hasOne(Banking::class, 'user_uuid', 'uuid')
            ->withTrashed();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'user_uuid',
            'uuid'
        )->orderBy('created_at', 'desc');
    }

    public function trashedReviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'user_uuid',
            'uuid'
        )
            ->withTrashed();
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(
            Wallet::class,
            'user_uuid',
            'uuid'
        );
    }

    public function trashedWallet(): HasOne
    {
        return $this->hasOne(
            Wallet::class,
            'user_uuid',
            'uuid'
        )
            ->withTrashed();
    }

    public function profileDescription(): HasOne
    {
        return $this->hasOne(
            CookProfileDescription::class,
            'user_uuid',
            'uuid'
        );
    }

    public function trashedProfileDescription(): HasOne
    {
        return $this->hasOne(
            CookProfileDescription::class,
            'user_uuid',
            'uuid'
        )
            ->withTrashed();
    }

    public function getLastLoginDate()
    {
        return $this->last_login_date;
    }

    public function deleteUserData(): ?bool
    {
        // Verwijder alle gerelateerde gegevens van de gebruiker
        foreach ($this->deletableRelations as $relation) {
            $this->$relation()?->delete();
        }

        // Verwijder de gebruiker
        return $this->forceDelete();
    }

    public function hardDelete()
    {
        // Perform additional deletion tasks, if any, before or after calling parent::delete()
        return parent::delete();
    }

    /**
     * FIXED: Nu gebruikt deze methode de Cook model logica voor consistentie
     * Delegate naar Cook model voor consistente berekening van verkochte porties
     * Telt orders met SUCCEED, PAYOUT_PENDING of PAID_OUT die niet geannuleerd zijn
     */
    public function getTotalSoldPortions(): int
    {
        // Als de user een cook heeft, gebruik de Cook model logica
        if ($this->cook) {
            return $this->cook->getSoldPortions();
        }

        // Fallback: als geen cook relatie, tel direct via orders met dezelfde logica als Cook model
        return (int) $this->order()
            ->whereIn('payment_state', [
                Order::SUCCEED,
                Order::PAYOUT_PENDING,
                Order::PAID_OUT,
            ])
            ->where('status', '!=', Order::STATUS_GEANNULEERD)
            ->sum('portion_amount');
    }

    public function getTotalSoldOrders()
    {
        return $this->order()->where('status', Order::STATUS_VERLOPEN)->count();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function dac7Information()
    {
        return $this->hasOne(Dac7Information::class, 'user_id', 'uuid');
    }
}
