<?php

namespace App\Http\Controllers\API\User;

use App\Helpers\Formatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class ProfileController extends Controller
{
    public function me(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            return Formatter::responseJson(200, 'Berhasil mendapatkan data pengguna.', $user);
        } catch (JWTException $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function updateProfile(Request $request) : JsonResponse {
        try {
            $user = Auth::user();

            // validate input
            $validator = Validator::make($request->all(), [
                'name' => 'string|min:4|max:255|regex:/^[a-zA-Z\s.]+$/',
                'email' => 'string|min:4|max:255|email|unique:users,email,' . $user->id,
                'phone_number' => 'string|min:10|max:15|unique:users,phone_number,'. $user->id . '|regex:/^08\d{8,13}$/',
            ], [
                'name.regex' => 'Nama penerima hanya boleh huruf saja!',
                'phone_number.regex' => 'Format nomor telepon tidak sesuai!',
            ]);
            if ($validator->fails()) {
                return Formatter::responseJson(400, $validator->errors()->all());
            }

            // update data 
            DB::transaction(function() use ($request, &$user) {
                $user = User::where('id', $user->id)->lockForUpdate()->first();
                
                if ($request->has('name')) {
                    $user->name = $request->name;
                }
                if ($request->has('phone_number')) {
                    $user->phone_number = $request->phone_number;
                }
                if ($request->has('email')) {
                    $user->email = $request->email;
                }
                $user->save();
            });

            return Formatter::responseJson(200, 'Berhasil memperbarui data pengguna.', $user);
            
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        } 
    }
}
