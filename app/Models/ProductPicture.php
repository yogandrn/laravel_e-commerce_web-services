<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPicture extends Model
{
    protected $table = 'product_pictures';
    protected $primary = 'id';

    protected $fillable = ['product_id', 'image_url'];

    // handle uploading file

    ///  table relations
    public function product() : BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
