<?php

namespace App\Models;

use App\Enums\ImageJobStatusEnum;
use App\Enums\MoneyTransferStatusEnum;
use App\Enums\TypeBalanceEnum;
use App\Enums\UserPlansEnum;
use App\Enums\UserPlansStatusEnum;
use App\Enums\UserSocialiteTypeEnum;
use App\Services\CryptService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use function Symfony\Component\Translation\t;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'first_name',
        'email',
        'password',
        'phone_id',
        'type_user',
        'phone',
        'verification_code',
        'phone_verified_at',
        'provider_id',
        'provider',
        'bio',
        'location',
        'business_name'
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
        'phone_verified_at' => 'datetime',
    ];

    /**
     * @return bool
     */
    public function userPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * @return bool
     */
    public function phoneVerifiedAt()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return HasOne
     */
    public function avatar(): HasOne
    {
        return $this->hasOne(Avatar::class, 'user_id', 'id');
    }

    /**
     * Image jobs
     *
     * @return HasMany
     */
    public function image_jobs(): HasMany
    {
        return $this->hasMany(ImageJob::class);
    }

    /**
     * @return HasMany
     */
    public function image_jobs_active(): HasMany
    {
        return $this->hasMany(ImageJob::class)->whereNotIn('status', [ImageJobStatusEnum::$FINISHED]);
    }

    /**
     * @return HasMany
     */
    public function style_guide(): HasMany
    {
        return $this->hasMany(StyleGuide::class);
    }

    /**
     * @return HasOne
     */
    public function stripe_user(): HasOne
    {
        return $this->hasOne(StripeUser::class);
    }

    /**
     * @return HasOne
     */
    public function user_card(): HasOne
    {
        return $this->hasOne(UserCard::class);
    }

    /**
     * @return HasOne
     */
    public function plan(): HasOne
    {
        return $this->hasOne(UserPlan::class)->where('status', UserPlansStatusEnum::$ACTIVE);
    }

    /**
     * @return HasMany
     */
    public function payment_history(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'succeeded')->orderBy('created_at', 'desc');
    }

    /**
     * @return bool
     */
    public function is_business(): bool
    {
        return $this->type_user === 'business';
    }

    /**
     * @return HasOne
     */
    public function connect_id(): HasOne
    {
        return $this->hasOne(StripeConnect::class);
    }

    /**
     * @return HasMany
     */
    public function money_transfers_done(): HasMany
    {
        return $this->hasMany(MoneyTransfer::class)
            ->where('status', '!=', MoneyTransferStatusEnum::$BLOCKED)
            ->where('type_balance', '!=', TypeBalanceEnum::$HOLD)
            ->orderBy('created_at', 'desc');
    }

    /**
     * @return string
     */
    public function getKeyInstagram(): string
    {
        $key = KeysUser::where('user_id', $this->id)->where('type', UserSocialiteTypeEnum::$INSTAGRAM)->first();
        return $key->key;
    }

    /**
     * @return HasOne
     */
    public function login_instagram(): HasOne
    {
        return $this->hasOne(InstagramConnects::class,'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function shopify_connect(): HasOne
    {
        return $this->hasOne(ShopifyConnects::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function user_files(): HasMany
    {
        return $this->hasMany(UserFile::class,'user_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * @return HasMany
     */
    public function editor_reviews():HasMany
    {
        return $this->hasMany(Review::class,'to_user_id','id');
    }
}
