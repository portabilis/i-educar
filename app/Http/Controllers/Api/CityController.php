<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\City;
use App\Rules\CityRestricOperationRule;
use clsPermissoes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CityController extends ResourceController
{
    public function index(City $city, Request $request): JsonResource
    {
        return $this->all($city, $request);
    }

    public function store(City $city, Request $request): JsonResource
    {
        return $this->post($city, $request);
    }

    public function show(City $city, Request $request): JsonResource
    {
        return $this->get($city, $request);
    }

    public function update(City $city, Request $request): JsonResource
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
            new CityRestricOperationRule($accessLevel)
        ];
    }
}
