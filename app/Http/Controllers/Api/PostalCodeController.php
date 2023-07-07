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
     * @param int    $postalCode
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
            'complement' => $this->getClearComplementByRuleEducacenso($data->complemento),
            'neighborhood' => $data->bairro,
            'city_name' => $data->localidade,
            'state_abbreviation' => $data->uf,
            'city_ibge_code' => intval($data->ibge),
            'city' => $city->only(['id', 'state_id', 'name', 'ibge_code']),
        ]);
    }

    private function getClearComplementByRuleEducacenso(string $complement): string
    {
        $comAcentos = ['à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú'];
        $semAcentos = ['a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U'];

        return preg_replace("/[^a-zA-Z0-9 \/–\ .]/", '', str_replace($comAcentos, $semAcentos, $complement));
    }
}
