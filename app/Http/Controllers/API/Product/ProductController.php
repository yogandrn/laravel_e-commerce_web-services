<?php

namespace App\Http\Controllers\API\Product;

use App\Helpers\Formatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function getProducts(Request $request) : JsonResponse {
        try {
            $cacheTTL = 120;
            $products = Cache::remember('products', $cacheTTL, function() {
                return Product::with('category')->get();
            });

            // filter products
            if ($request->has('categoryId')) {
                $products = $products->where('category_id', $request->categoryId);
            }

            if ($request->has('minPrice') && $request->has('maxPrice')) {
                $products = $products->whereBetween('price', [$request->minPrice, $request->maxPrice]);
            }

            // sort product
            switch ($request->sortBy) {
                case 'recommended':
                    $products = $products->sortByDesc('count_sold');
                    break;
                case 'latest':
                    $products = $products->sortByDesc('created_at');
                    break;
                case 'name':
                    $products = $products->sortByAsc('name');
                    break;
                    
                default:
                    $products = $products->shuffle();
                    break;
            }

            return Formatter::responseJson(200, 'Berhasil mendapatkan data produk', $products->values());
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function getProductBySlug($slug) : JsonResponse {
        try {
            $product = Product::with('pictures:id,product_id,image_url')->where('slug', $slug)->sharedLock()->first();

            if (!$product) {
                return Formatter::responseJson(404, 'Data produk tidak ditemukan!');
            }

            return Formatter::responseJson(200, 'Berhasil mendapatkan data produk.', $product);
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

}
