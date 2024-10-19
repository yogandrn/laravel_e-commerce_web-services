<?php

namespace App\Models;

use App\Helpers\Formatter;
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
    
    protected $with = ['product:id,category_id,name,slug,description,thumbnail,price,count_stock,count_sold,weight'];

    public function getCreatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    public function getUpdatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }



    /// table relations
    public function product() : BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function transaction() : BelongsTo {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
}
