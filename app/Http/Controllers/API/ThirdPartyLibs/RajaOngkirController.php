<?php

namespace App\Http\Controllers\API\ThirdPartyLibs;

use App\Helpers\Formatter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class RajaOngkirController extends Controller
{
    public function getShippingRates(Request $request) : JsonResponse {
        // validate input
        $validator = Validator::make($request->all(), [
            'weight' => 'required|integer|min:100|max:100000',
            'destination' => 'required|string|digits:5|regex:/^[0-9]+$/',
        ], [
            'weight.min' => 'Berat (weight) tidak boleh kurang dari 100 gram!',
            'weight.max' => 'Berat (weight) tidak boleh lebih dari 100.000 gram/100kg!',
            'destination.regex' => 'Destinasi harus berupa kode pos 5 digit!'
        ]);
        if ($validator->fails()) {
            return Formatter::responseJson(400, $validator->errors()->all());
        }

        try {
            // fetch from API
            $apiKey = env('RAJA_ONGKIR_API_KEY', '####');
            $url = env('RAJA_ONGKIR_ENDPOINT');
            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'key' =>  $apiKey,
            ])->post($url, [
                'origin' => env('RAJA_ONGKIR_ORIGIN_POSTAL_CODE'),
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => env('RAJA_ONGKIR_COURIER'),
            ]);

            // parse response to json
            $responseJson = $response->json()['rajaongkir'];

            if ($response->clientError()) {
                $message = $responseJson['status']['description'];
                return Formatter::responseJson(400, $message);
            }
            
            if ($response->serverError()) {
                $message = $responseJson['status']['description'];
                return Formatter::responseJson(500, $message);
            }

            $costResults = array();
            foreach ($responseJson['results'][0]['costs'] as $cost) {
                $data['service'] = $cost['service'];
                $data['description'] = $cost['description'];
                $data['cost'] = $cost['cost'][0]['value'];
                $data['etd'] = $cost['cost'][0]['etd'];
                array_push($costResults, $data);
            }

            $result = [
                'origin' => $responseJson['origin_details'],
                'destination' => $responseJson['destination_details'],
                'results' => [
                    'code' => $responseJson['results'][0]['code'],
                    'name' => $responseJson['results'][0]['name'],
                    'costs' => $costResults,
                ],
            ];
            return Formatter::responseJson(200, 'Berhasil mendapatkan biaya pengiriman.', $result);
        } catch (Exception $e) {
            return Formatter::responseJson(500, $e->getMessage());
        }
    }
}
