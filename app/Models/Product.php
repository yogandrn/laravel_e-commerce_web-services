<?php

namespace App\Models;

use App\Helpers\Formatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'products';
    protected $primary = 'id';

    protected $fillable = [
        'name',
        'category_id', 
        'slug', 
        'description',
        'thumbnail',
        'tags',
        'price',
        'count_stock',
        'count_sold',
        'weight',
    ];

    // handling generate slug
    public function generateUniqueSlug($value) : string {
        // generate slug
        $slug = Str::slug($value);

        // check if duplicat slug
        $countSlug = Product::where('slug', $slug)->count();
    
        // add number identifier on duplicate slug
        if ($countSlug > 0) { $slug .= '-' . $countSlug; }

        return $slug;
    }

    public function getCreatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    public function getUpdatedAtAttribute($value) {
        return Formatter::datetimeFormat($value);
    }

    //// table relations list
    public function category() : BelongsTo {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function pictures() : HasMany {
        return $this->hasMany(ProductPicture::class, 'product_id', 'id');
    }
}
