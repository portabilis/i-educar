<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Throwable;

class PostalCodeController extends Controller
{
    /**
     * @return JsonResponse
     */
    protected function notFound()
    {
        return response()->json([
            'message' => 'Not found',
        ], 404);
    }

    /**
     * @param Client $http
     * @param int    $postalCode
     *
     * @return JsonResponse
     */
    public function search(Client $http, $postalCode)
    {
        $postalCode = intval($postalCode);

        try {
            $response = $http->get("https://viacep.com.br/ws/{$postalCode}/json/");
        } catch (Throwable $throwable) {
            return $this->notFound();
        }

        $data = json_decode($response->getBody()->getContents());

        if (isset($data->erro)) {
            return $this->notFound();
        }

        if (empty($city = City::findByIbgeCode($data->ibge))) {
            return $this->notFound();
        }

        return response()->json([
            'postal_code' => $data->cep,
            'address' => $data->logradouro,
            'complement' => $data->complemento,
            'neighborhood' => $data->bairro,
            'city_name' => $data->localidade,
            'state_abbreviation' => $data->uf,
            'city_ibge_code' => intval($data->ibge),
            'city' => $city->only(['id', 'state_id', 'name', 'ibge_code']),
        ]);
    }
}
