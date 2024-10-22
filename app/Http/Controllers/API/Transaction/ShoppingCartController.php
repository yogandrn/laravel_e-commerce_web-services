<?php

namespace App\Http\Controllers\API\Transaction;

use App\Helpers\Formatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends Controller
{
    public function getShoppingCartItems(Request $request) : JsonResponse {
        try {
            $user = Auth::user();
            
            // cari keranjang (dari Transaction where status = ON_CART)
            $shoppingCart = Transaction::where('user_id', $user->id)->where('status', 'ON_CART')->sharedLock()->first();

            // jika belum ada, buat baru
            if (!$shoppingCart) {
                DB::transaction(function () use($user, &$shoppingCart) {
                    $orderCode = Transaction::generateOrderCode();
                    $shoppingCart = Transaction::create([
                        'user_id' => $user->id,
                        'order_code' => $orderCode,
                        'status' => 'ON_CART',
                    ]);
                });
            }
            
            // update subtotal cart item sesuai data product
            DB::transaction(function () use ($shoppingCart) {
                foreach ($shoppingCart->items as $item) {
                    $subtotal = intval($item->product->price * $item->quantity);
                    $item->subtotal = $subtotal;
                    $item->save();
                }
            });

            $result = $shoppingCart->items;
            
            return Formatter::responseJson(200, 'Berhasil mendapatkan data keranjang belanja.', $result);
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }
    
    public function addToCart(Request $request) : JsonResponse {
        // validate input
        $validator = Validator::make($request->only('product_id', 'quantity'), [
            'product_id' => 'required|integer|min:1', 
            'quantity' => 'required|integer|min:1', 
        ]);
        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            $user = Auth::user();
            
            // cari keranjang (dari Transaction where status = ON_CART)
            $shoppingCart = Transaction::where('user_id', $user->id)->where('status', 'ON_CART')->sharedLock()->first();

            // jika belum ada, buat baru
            if (!$shoppingCart) {
                DB::transaction(function () use($user, &$shoppingCart) {
                    $orderCode = Transaction::generateOrderCode();
                    $shoppingCart = Transaction::create([
                        'user_id' => $user->id,
                        'order_code' => $orderCode,
                        'status' => 'ON_CART',
                    ]);
                });
            }

            // cari produk yang akan ditambahkan
            $product = Product::where('id', $request->product_id)->lockForUpdate()->first();
            if (!$product) {
                return Formatter::responseJson(404, 'Tidak dapat menemukan produk!');
            }

            // cari apakah sudah ada item dengan produk yg sama
            $existItem = TransactionItem::where('transaction_id', $shoppingCart->id)->where('product_id', $product->id)->lockForUpdate()->first();
            
            // set maksimum quantity yang bisa ditambahkan
            // jika sudah ada item produk yang sama, kurangi dgn quantity lamanya, jika tidak gunakan stok produk
            $maxQty = $existItem ? intval($product->count_stock - $existItem->quantity) : intval($product->count_stock);
            
            // validasi quantity
            if (intval($request->quantity) > $maxQty ) {
                return Formatter::responseJson(400, 'Stok produk yang tersedia tidak mencukupi.');
            }

            // db begin transaction
            DB::transaction(function() use ($request, &$shoppingCart, &$existItem, $product) {
                $subtotal = intval($request->quantity * $product->price);

                // jika sudah ada, ubah datanya
                if ($existItem) {
                    $existItem->quantity += intval($request->quantity);
                    $existItem->subtotal += $subtotal;
                    $existItem->save();
                } else {
                    // jika belum, tambahkan item baru
                    TransactionItem::create([
                        'transaction_id' => $shoppingCart->id,
                        'product_id' => $product->id,
                        'quantity' => intval($request->quantity),
                        'subtotal' => $subtotal,
                    ]);
                }
                // update nominal transaksi
                $shoppingCart->subtotal += $subtotal;
                $shoppingCart->total += $subtotal;
                $shoppingCart->save();
            });
            
            return Formatter::responseJson(200, 'Berhasil menambahkan produk ke keranjang belanja.');
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function incrementQuantity($id) : JsonResponse {
        try {
            $user = Auth::user();

            // cari item keranjang
            $cartItem = TransactionItem::with(['transaction'])->lockForUpdate()->find($id);

            // jika item tidak ada, atau bukan termasuk keranjang (status != ON_CART)
            if (!$cartItem || $cartItem->transaction->status !== 'ON_CART') {
                return Formatter::responseJson(404, 'Item keranjang tidak ditemukan');
            }

            $product = Product::lockForUpdate()->find($cartItem->product_id);

            // jika item bukan milik pengguna
            if ($cartItem->transaction->user_id !== $user->id) {
                return Formatter::responseJson(403, 'Item keranjang ini bukan milik Anda!');
            }

            // cek apakah sudah jumlah maksimum
            // apakah jumlah item cart skrang +jika ditambah 1 sudah lebih dari stok produk
            $isMaximum = intval($cartItem->quantity + 1) > $product->count_stock ;
            
            // validasi quantity
            if ($isMaximum ) {
                return Formatter::responseJson(400, 'Jumlah item telah melewati batas maksimum!');
            }

            // db transaction
            DB::transaction(function () use ($id, $cartItem) {
                // ubah subtotal dan total transaksi
                $cartItem->transaction->subtotal += $cartItem->product->price;
                $cartItem->transaction->total += $cartItem->product->price;
                $cartItem->transaction->save();
                
                // ubah jumlah item cart
                $cartItem->subtotal += $cartItem->product->price;
                $cartItem->quantity += 1;
                $cartItem->save();
            });
            
            return Formatter::responseJson(200, 'Berhasil memperbarui jumlah item keranjang.');
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());

        }
    }

    public function decrementQuantity($id) : JsonResponse {
        try {
            $user = Auth::user();

            // cari item keranjang
            $cartItem = TransactionItem::with(['transaction'])->lockForUpdate()->find($id);

            // jika item tidak ada, atau bukan termasuk keranjang (status != ON_CART)
            if (!$cartItem || $cartItem->transaction->status !== 'ON_CART') {
                return Formatter::responseJson(404, 'Item keranjang tidak ditemukan');
            }

            $product = Product::lockForUpdate()->find($cartItem->product_id);

            // jika item bukan milik pengguna
            if ($cartItem->transaction->user_id !== $user->id) {
                return Formatter::responseJson(403, 'Item keranjang ini bukan milik Anda!');
            }

            if ($cartItem->quantity === 1) {
                return Formatter::responseJson(400, 'Jumlah item telah melewati batas minimum!');
            }

            // db transaction
            DB::transaction(function () use ($id, $cartItem) {
                $cartItem->transaction->subtotal -= $cartItem->product->price;
                $cartItem->transaction->total -= $cartItem->product->price;
                $cartItem->transaction->save();
                
                // ubah jumlah item cart
                $cartItem->subtotal -= $cartItem->product->price;
                $cartItem->quantity -= 1;
                $cartItem->save();
            });
            
            return Formatter::responseJson(200, 'Berhasil memperbarui jumlah item keranjang.');
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());

        }
    }

    public function removeFromCart($id) : JsonResponse {
        try {
            $user = Auth::user();

            // cari item keranjang
            $cartItem = TransactionItem::lockForUpdate()->find($id);

            // jika item tidak ada, atau bukan termasuk keranjang (status != ON_CART)
            if (!$cartItem || $cartItem->transaction->status !== 'ON_CART') {
                return Formatter::responseJson(404, 'Item keranjang tidak ditemukan');
            }

            $product = Product::lockForUpdate()->find($cartItem->product_id);


            // jika item bukan milik pengguna
            if ($cartItem->transaction->user_id !== $user->id) {
                return Formatter::responseJson(403, 'Item keranjang ini bukan milik Anda!');
            }

            // db transaction
            DB::transaction(function () use ($id, $cartItem) {
                // ubah subtotal dan total transaksi
                $cartItem->transaction->subtotal -= $cartItem->subtotal;
                $cartItem->transaction->total -= $cartItem->subtotal;
                $cartItem->transaction->save();

                // delete item from cart
                $cartItem->delete();
            });

            return Formatter::responseJson(200, 'Berhasil menghapus item produk dari keranjang.');

        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }
}
