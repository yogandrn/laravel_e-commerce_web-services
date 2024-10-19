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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) : JsonResponse {
        // validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:255|regex:/^[a-zA-Z\s.]+$/',
            'email' => 'required|string|email|min:4|max:255|unique:users,email',
            'phone_number' => 'required|string|min:10|max:15|regex:/^08\d{8,13}$/|unique:users,phone_number',
            'password' => 'required|string|min:8|max:255|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])/',
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf!',
            'phone_number.regex' => 'Format nomor telepon tidak sesuai!',
            'password.regex' => 'Password harus berisi kombinasi huruf besar, huruf kecil, dan angka!'
        ]);
        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            $newUser = null;

            DB::transaction(function () use ($request, &$newUser) {
                $newUser = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                    'access_role' => 'USER',
                ]);
            });

            return Formatter::responseJson(201, 'Berhasil registrasi.', $newUser);

        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function login(Request $request) : JsonResponse {
        // validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|min:4|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);
        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            // jwt check auth
            $access_token = JWTAuth::attempt($request->only('email', 'password'));

            if (!$access_token) {
                return Formatter::responseJson(401, 'Email dan password tidak sesuai!');
            }
            
            $user = Auth::user();

            $result = [
                'user' => $user,
                'token' => [
                    'access_token' => $access_token,
                    'token_type' => 'Bearer',
                ]
                ];

            return Formatter::responseJson(200, 'Autentikasi berhasil.', $result);

        } catch (JWTException $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse {
        try {
            
            auth('api')->logout(true);

            return Formatter::responseJson(200, 'Berhasil logout');
        } catch (JWTException $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }
}
