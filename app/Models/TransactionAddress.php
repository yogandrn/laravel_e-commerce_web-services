<?php

namespace App\Models;

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


    // table relations
    public function transaction() : BelongsTo {
        return $this->belongsTo(Transaction::class, 'transation_id', 'id');
    }
}
