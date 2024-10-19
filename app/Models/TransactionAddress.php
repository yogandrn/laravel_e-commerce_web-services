<?php

namespace App\Models;

use App\Helpers\Formatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionAddress extends Model
{
    protected $table = 'transaction_addresses';
    protected $primary = 'id';

    protected $fillable = [
        'transaction_id',
        'name',
        'phone_number',
        'address',
        'postal_code',
    ];

    public function getCreatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    public function getUpdatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    // table relations
    public function transaction() : BelongsTo {
        return $this->belongsTo(Transaction::class, 'transation_id', 'id');
    }
}
