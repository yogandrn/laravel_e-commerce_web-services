<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionItem extends Model
{
    protected $table = 'transaction_items';
    protected $primary = 'id';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'subtotal',
    ];


    /// table relations
    public function product() : HasOne {
        return $this->hasOne(Product::class, 'product_id', 'id');
    }

    public function transaction() : BelongsTo {
        return $this->belongsTo(Transaction::class, 'transation_id', 'id');
    }
}
