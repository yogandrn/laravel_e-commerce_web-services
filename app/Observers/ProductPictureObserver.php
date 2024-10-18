<?php

namespace App\Observers;

use App\Models\ProductPicture;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductPictureObserver
{
    /**
     * Handle the ProductPicture "created" event.
     */
    public function created(ProductPicture $productPicture): void
    {
        Cache::delete('products');
    }

    /**
     * Handle the ProductPicture "updated" event.
     */
    public function updated(ProductPicture $productPicture): void
    {
        Cache::delete('products');
    }

    /**
     * Handle the ProductPicture "deleted" event.
     */
    public function deleted(ProductPicture $productPicture): void
    {
        Cache::delete('products');
        // Hapus file terkait jika ada
        if ($productPicture->thumbnail) {
            Storage::delete($productPicture->image_url);
        }


    }

    /**
     * Handle the ProductPicture "restored" event.
     */
    public function restored(ProductPicture $productPicture): void
    {
        //
    }

    /**
     * Handle the ProductPicture "force deleted" event.
     */
    public function forceDeleted(ProductPicture $productPicture): void
    {
        //
    }
}
