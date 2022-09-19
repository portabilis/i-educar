<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingCountryRequest;
use App\Models\Country;
use App\Rules\Addressing\AddressingCountryRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryController extends ResourceController
{
    public int $process = 753;

    public function index(Country $country, Request $request): JsonResource
    {
        return $this->all($country, $request);
    }

    public function store(Country $country, AddressingCountryRequest $request): JsonResource
    {
        return $this->post($country, $request);
    }

    public function show(int $country, Request $request): JsonResource
    {
        return $this->get($country, $request,Country::class);
    }

    public function update(Country $country, AddressingCountryRequest $request): JsonResource
    {
        return $this->patch($country, $request);
    }

    public function destroy(Country $country, Request $request): JsonResource
    {
        return $this->delete($country, $request);
    }

    protected function deleteRules(Model $model, Request $request)
    {
        return [
            new AddressingCountryRule()
        ];
    }
}
