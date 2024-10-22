<?php

namespace App\Http\Controllers\API\Transaction;

use App\Helpers\Formatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionAddress;
use App\Models\TransactionItem;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function getTransactionList(Request $request) : JsonResponse {
        try {
            $user = Auth::user();

            // initial query
            $transactions = Transaction::where('user_id', $user->id)->whereNot('status', 'ON_CART')->latest();

            // filter status
            if ($request->has('status')) {
                switch ($request->status) {
                    case '1':
                        $transactions->where('status', 'PENDING');
                        break;
                    case '2':
                        $transactions->where('status', 'ON_DELIVERY');
                        break;
                    case '3':
                        $transactions->where('status', 'SUCCESS');
                        break;
                    case '4':
                        $transactions->where('status', 'CANCELED');
                        break;
                    default:
                        break;
                }
            }

            // filter datetime
            if ($request->has('from') && $request->has('until')) {
                $transactions->whereBetween('created_at', [$request->from, $request->until]);
            }

            return Formatter::responseJson(200, 'Berhasil mendapatkan riwayat transaksi', $transactions->get());

        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function checkout() : JsonResponse {
        // validate input
        // $validator = Validator::make($request->all(), [
        //     'weight' => 'required|integer|min:100|max:100000',
        //     'address_id' => 'required|integer',
        // ], [
        //     'weight.min' => 'Berat (weight) tidak boleh kurang dari 100 gram!',
        //     'weight.max' => 'Berat (weight) tidak boleh lebih dari 100.000 gram/100kg!',
        //     'address_id.integer' => 'ID Alamat harus berupa angka!',
        // ]);
        // if ($validator->fails()) {
        //     return Formatter::responseJson(400, $validator->errors()->all());
        // }

        try {
            $user = Auth::user();

            // cek apakah user sudah menambahkan alamat
            $userHasAddress = UserAddress::hasAddress($user->id);
            if (!$userHasAddress) {
                return Formatter::responseJson(400, 'Pengguna belum manambahkan alamat pengiriman!');
            }
            
            // cari keranjang (dari Transaction where status = ON_CART)
            $shoppingCart = Transaction::where('user_id', $user->id)->where('status', 'ON_CART')->sharedLock()->first();

            if (!$shoppingCart || count($shoppingCart->items) === 0) {
                return Formatter::responseJson(400, 'Tidak ada item produk yang dapat diproses!');
            }
            
            // update subtotal cart item sesuai data product
            DB::transaction(function () use ($shoppingCart) {
                foreach ($shoppingCart->items as $item) {
                    $nominal = intval($item->product->price * $item->quantity);
                    $item->subtotal = $nominal;
                    $item->save();
                }
            });
            
            $result = [
                'items' => $shoppingCart->items,
                'additional_fee' => intval(env('SERVICE_FEE', 0)),
            ];
            
            return Formatter::responseJson(200, 'Produk dalam keranjang siap untuk di pesan.', $result);
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function createOrder(Request $request) : JsonResponse {
        // validate input
        $validator = Validator::make($request->only('delivery_fee', 'additional_fee', 'user_address_id'), [
            'delivery_fee' => 'required|integer|min:5000|max:1000000',
            'additional_fee' => 'required|integer|min:1000|max:100000',
            'user_address_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            // auth user data
            $user = Auth::user();

            // cari keranjang (dari Transaction where status = ON_CART)
            $shoppingCart = Transaction::with('items:id,product_id,quantity,subtotal')
                            ->where('user_id', $user->id)->where('status', 'ON_CART')
                            ->lockForUpdate()->first();

            // jika keranjang tidak ada atau itemnya kosong,
            if (!$shoppingCart || $shoppingCart->items === []) {
                return Formatter::responseJson(422, 'Tidak ada item produk yang dapat diproses!');
            }

            // cek apakah user sudah menambahkan alamat
            $userHasAddress = UserAddress::hasAddress($user->id);
            if (!$userHasAddress) {
                return Formatter::responseJson(422, 'Pengguna belum manambahkan alamat pengiriman!');
            }
            
            // cari alamat user
            $userAddress = UserAddress::sharedLock()->find($request->user_address_id);

            // jika tidak ada 
            if (!$userAddress) {
                return Formatter::responseJson(404, 'Alamat yang dipilih tidak dapat ditemukan!');
            }

            // jika alamat bukan milik user
            if ($userAddress->user_id !== $user->id) {
                return Formatter::responseJson(403, 'Alamat tidak dapat dipilih karena bukan milik Anda!');
            }

            $result = null; // variable for transaction result

            // db transaction start
            DB::transaction(function () use ($request, $user, $shoppingCart, $userAddress, &$result) {
                // generate thumbnail from first item product thumbnail
                $thumbnail = null;

                $subtotalOrders = 0;
                
                // handle lock data produk pada item keranjang
                foreach ($shoppingCart->items as $item) {
                    $lockedProduct = Product::lockForUpdate()->find($item->product_id);

                    // set thumbnail
                    $thumbnail = $lockedProduct->thumbnail;

                    // jika stok produk tidak mencukupi, return error
                    if ($item->quantity > $lockedProduct->count_stock ) {
                        break;
                        return Formatter::responseJson(422, 'Transaksi tidak dapat diproses karena stock produk tidak mencukupi atau habis!');
                    }

                    // ubah stock produk jadi berkurang sesuai quantity
                    $lockedProduct->count_stock -= intval($item->quantity);
                    $lockedProduct->save();

                    $subtotalOrders += $item->subtotal;
                }

                // generate new order code
                $newOrderCode = Transaction::generateOrderCode();
                $totalAmount = $subtotalOrders + $request->delivery_fee + $request->additional_fee;

                // ubah data transaksinya
                $shoppingCart->order_code = $newOrderCode;
                $shoppingCart->thumbnail = $thumbnail;
                $shoppingCart->subtotal = $subtotalOrders;
                $shoppingCart->delivery_fee = $request->delivery_fee;
                $shoppingCart->additional_fee = $request->additional_fee;
                $shoppingCart->total = $totalAmount;
                $shoppingCart->status = 'PENDING';
                $shoppingCart->save();

                // simpan alamat ke transaction_address
                $transactionAddress = TransactionAddress::create([
                    'transaction_id' => $shoppingCart->id,
                    'name' => $userAddress->name,
                    'address' => $userAddress->address,
                    'phone_number' => $userAddress->phone_number,
                    'postal_code' => $userAddress->postal_code,
                ]);

                // assign ke variable result
                $result = $shoppingCart;
                $result['address'] = $transactionAddress;
            });

            return Formatter::responseJson(200, 'Transaksi berhasil dibuat', $result);

        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }
}
