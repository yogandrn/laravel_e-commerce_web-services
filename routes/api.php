<?php

use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\Transaction\ShoppingCartController;
use App\Http\Controllers\API\User\AuthController;
use App\Http\Controllers\API\User\ProfileController;
use App\Http\Controllers\API\User\UserAddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// api v1
Route::prefix('/v1')->group(function() {

    Route::post("/auth/register", [AuthController::class, 'register']);
    Route::post("/auth/login", [AuthController::class, 'login']);
    
    // products
    Route::get("/products", [ProductController::class , 'getProducts']);
    
    // authenticated api middleware
    Route::middleware(['auth:api'])->group(function() {
        // authenticate & authorize
        Route::post("/auth/logout", [AuthController::class, 'logout']);

        // user account and profile
        Route::get("/user", [ProfileController::class, 'me']);
        Route::get("/user/address", [UserAddressController::class, 'getAddresses']);
        Route::post("/user/address", [UserAddressController::class, 'createAddress']);
        Route::put("/user/address/{id}", [UserAddressController::class, 'updateAddress']);
        Route::delete("/user/address/{id}", [UserAddressController::class, 'deleteAddress']);

        // shopping carts
        Route::get("/carts", [ShoppingCartController::class, 'getShoppingCartItems']);
        Route::post("/carts", [ShoppingCartController::class, 'addToCart']);
        Route::delete("/carts/{id}", [ShoppingCartController::class, 'removeFromCart']);
        Route::put("/carts/{id}/increment", [ShoppingCartController::class, 'incrementQuantity']);
        Route::put("/carts/{id}/decrement", [ShoppingCartController::class, 'decrementQuantity']);

        
    });
});