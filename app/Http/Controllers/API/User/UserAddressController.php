<?php

namespace App\Http\Controllers\API\User;

use App\Helpers\Formatter;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    public function getAddresses(Request $request) : JsonResponse {
        try {
            $user = Auth::user();

            $addresses = $user->address_list;

            return Formatter::responseJson(200, 'Berhasil mendapatkan data alamat.', $addresses);
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function createAddress(Request $request) : JsonResponse {
        // validate input 
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:255|regex:/^[a-zA-Z\s.]+$/',
            'phone_number' => 'required|string|min:10|max:15|regex:/^08\d{8,13}$/',
            'address' => 'required|string|min:10|max:255|regex:/^[a-zA-Z0-9\s.,-]+$/',
            'postal_code' => 'required|string|digits:5|regex:/^[0-9]+$/',
        ], [
            'name.regex' => 'Nama penerima hanya boleh huruf saja!',
            'phone_number.regex' => 'Format nomor telepon tidak sesuai!',
            'address.regex' => 'Alamat hanya boleh berisi huruf, angka dan beberapa tanda baca (.,-)!',
            'postal_code.regex' => 'Kode pos harus berisi angka 5 digit!'
        ]);

        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            $user = Auth::user();

            // check existing address count
            $addressCount =  UserAddress::countUserAddress($user->id);
            if ($addressCount >= 3) {
                return Formatter::responseJson(400, 'Jumlah alamat yang tersimpan sudah mencapai batas maksimal (3 alamat)');
            }

            $newAddress = null; 

            // save new address
            DB::transaction(function() use ($request, $user, &$newAddress) {
                $newAddress = UserAddress::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'postal_code' => $request->postal_code,
                ]);
            });

            return Formatter::responseJson(201, 'Berhasil menambahkan alamat baru.', $newAddress);

        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function updateAddress(Request $request, $id) : JsonResponse {
        // validate input 
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:4|max:255|regex:/^[a-zA-Z\s.]+$/',
            'phone_number' => 'string|min:10|max:15|regex:/^08\d{8,13}$/',
            'address' => 'string|min:10|max:255|regex:/^[a-zA-Z0-9\s.,-]+$/',
            'postal_code' => 'string|digits:5|regex:/^[0-9]+$/',
        ], [
            'name.regex' => 'Nama penerima hanya boleh huruf saja!',
            'phone_number.regex' => 'Format nomor telepon tidak sesuai!',
            'address.regex' => 'Alamat hanya boleh berisi huruf, angka dan beberapa tanda baca (.,-)!',
            'postal_code.regex' => 'Kode pos harus berisi angka 5 digit!'
        ]);

        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            $user = Auth::user();

            // db starttransaction
            DB::beginTransaction();

            // find data to update
            $address = UserAddress::where('id', $id)->lockForUpdate()->first();
        
            if (!$address) {
                return Formatter::responseJson(404, 'Data alamat tidak ditemukan!');
            }

            // check user access
            if ($address->user_id !== $user->id) {
                return Formatter::responseJson(403, 'Anda tidak mempunyai akses untuk alamat ini!');
            }

            // change address data
            if ($request->has('name')) {
                $address->name = $request->name;
            }
            if ($request->has('phone_number')) {
                $address->phone_number = $request->phone_number;
            }
            if ($request->has('address')) {
                $address->address = $request->address;
            }
            if ($request->has('postal_code')) {
                $address->postal_code = $request->postal_code;
            }
            
            // save data 
            $address->save();
            DB::commit();

            return Formatter::responseJson(200, 'Berhasil memperbarui data alamat.', $address);

        } catch (Exception $e) {
            DB::rollBack();
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function deleteAddress($id) : JsonResponse {
        try {
            $user = Auth::user();

            // find data to update
            $address = UserAddress::where('id', $id)->lockForUpdate()->first();
            if (!$address) {
                return Formatter::responseJson(404, 'Data alamat tidak ditemukan!');
            }

            // check user access
            if ($address->user_id !== $user->id) {
                return Formatter::responseJson(403, 'Anda tidak mempunyai akses untuk alamat ini!');
            }

            DB::transaction(function () use ($id) {
                UserAddress::destroy($id);
            });

            return Formatter::responseJson(200, 'Berhasil menghapus data alamat.');
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }


}
