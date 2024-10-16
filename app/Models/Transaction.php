<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primary = 'id';

    protected $fillable = [
        'order_code',
        'user_id',
        'subtotal',
        'delivery_fee',
        'additional_fee',
        'total',
        'payment_url',
        'receipt_code',
        'status',
    ];

    // handle generate unique code
    public function generateOrderCode() : string {
        $date = Carbon::now()->format('ymd');
        do {
            $result = $date . Str::upper(Str::random(4));
        } while (self::where('order_code', $result)->exists());

        return $result;
    } 


    /// table relations
    public function items() : HasMany {
        return $this->hasMany(TransactionItem::class, 'transaction_id', 'id');
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function address() : HasOne {
        return $this->hasOne(TransactionAddress::class, 'transaction_id', 'id');
    }
}