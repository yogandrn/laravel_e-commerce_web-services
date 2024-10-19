<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\Formatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primary = 'id';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'profile_photo',
        'access_role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getCreatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    public function getUpdatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    // JWT implement method
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function isExist($value) : bool {
        $countUser = User::where('email', $value)->orWhere('phone_number', $value)->sharedLock()->count();
        return $countUser === 0 ? false : true;
    }


    //// table relations
    public function address_list() : HasMany {
        return $this->hasMany(UserAddress::class, 'user_id', 'id');
    }

    public function transactions() : HasMany {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
}
