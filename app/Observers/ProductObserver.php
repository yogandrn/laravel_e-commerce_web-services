<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductPicture;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        Cache::forget('products');
    }
    
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        Cache::forget('products');
    }
    
    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        Cache::forget('products');

        // delete all pictures file from relation
        foreach ($product->pictures as $picture ) {
            Storage::delete($picture->image_url);
        }

        // Hapus file terkait jika ada
        if ($product->thumbnail) {
            Storage::delete($product->thumbnail);
        }

    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
