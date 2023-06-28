<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingCityRequest;
use App\Models\City;
use App\Rules\Addressing\AddressingCityDistrictRule;
use App\Rules\Addressing\AddressingCityPlaceRule;
use App\Rules\CityRestrictOperationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityController extends ResourceController
{
    public int $process = 755;

    public function index(City $city, Request $request): JsonResource
    {
        return $this->all($city, $request);
    }

    public function store(City $city, AddressingCityRequest $request): JsonResource
    {
        return $this->post($city, $request);
    }

    public function show(int $city, Request $request): JsonResource
    {
        return $this->get($city, $request, City::class);
    }

    public function update(City $city, AddressingCityRequest $request): JsonResource
    {
        return $this->patch($city, $request);
    }

    public function destroy(City $city, Request $request): JsonResource
    {
        return $this->delete($city, $request);
    }

    public function rules(City|Model $city, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new CityRestrictOperationRule($accessLevel),
        ];
    }

    protected function deleteRules(Model $model, Request $request)
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new CityRestrictOperationRule($accessLevel),
            new AddressingCityDistrictRule(),
            new AddressingCityPlaceRule(),
        ];
    }
}
