<?php

namespace App\Models;

use App\Helpers\Formatter;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $table = 'user_addresses';
    protected $primary = 'id';

    protected $fillable = [
        'name',
        'phone_number',
        'address',
        'postal_code',
        'user_id',
    ];

    public static function hasAddress($userId): bool {
        $isExist = self::where('user_id', $userId)->sharedLock()->first();
        return !$isExist ? false : true;
    }

    public function getCreatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    public function getUpdatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    // validate address count
    public static function countUserAddress($userId) : int {
        $result = UserAddress::where('user_id', $userId)->lockForUpdate()->count();
        return $result ?? 0;
    }
    // validate if user authorized
    public static function isUserAuthorized(UserAddress $address, $userId) : bool {
        return $address->user_id === $userId;
    }

    /// table relations
    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
