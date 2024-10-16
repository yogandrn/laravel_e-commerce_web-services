<?php

namespace App\Models;

use App\Helpers\Formatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';
    protected $primary = 'id';

    protected $fillable = ['name', 'slug'];

    // handling generate slug
    public function generateUniqueSlug($value) : string {
        // generate slug
        $slug = Str::slug($value);

        // check if duplicat slug
        $countSlug = Category::where('slug', $slug)->count();
    
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

    ////  table relations
    public function products() : HasMany {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    // public function setNameAttribute($value) 
    // {
    //     $this->attributes['name'] = $value;
    //     // generate slug
    //     $slug = Str::slug($value);

    //     // Hanya menghasilkan slug jika nama baru berbeda dari nama sebelumnya
    //     if ($this->exists && $this->name === $value) {
    //         // Jika nama tidak berubah, jangan ubah slug
    //         return;
    //     }
        
    //     // Cek apakah slug sudah ada
    //     $countSlug = Category::where('slug', $slug)->count();

    //     // jika sudah ada, tandai dengan angkanya
    //     if ($countSlug > 0) { $slug .= '-' . $countSlug; }

    //     // Set slug yang unik
    //     $this->attributes['slug'] = $slug;
    // }
}
