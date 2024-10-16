<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class Formatter {
    public static function responseJson(int $statusCode, $message, $data = null) : JsonResponse {
        return response()->json([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function datetimeFormat($datetime) : string {
        return Carbon::parse($datetime)->setTimezone(env('DB_TIMEZONE', "+07:00"))->format('Y-m-d H:i:s');
    }
}