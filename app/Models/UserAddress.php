<?php

namespace App\Models;

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

    /// table relations
    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
