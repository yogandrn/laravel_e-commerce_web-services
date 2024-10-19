<?php

namespace App\Models;

use App\Helpers\Formatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPicture extends Model
{
    protected $table = 'product_pictures';
    protected $primary = 'id';

    protected $fillable = ['product_id', 'image_url'];

    public function getCreatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    public function getUpdatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    // handle uploading file

    ///  table relations
    public function product() : BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
