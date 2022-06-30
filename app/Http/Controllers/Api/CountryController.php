<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryController extends ResourceController
{
    public function index(Country $country, Request $request): JsonResource
    {
        return $this->all($country, $request);
    }

    public function store(Country $country, Request $request): JsonResource
    {
        return $this->post($country, $request);
    }

    public function show(Country $country, Request $request): JsonResource
    {
        return $this->get($country, $request);
    }

    public function update(Country $country, Request $request): JsonResource
    {
        return $this->patch($country, $request);
    }

    public function destroy(Country $country, Request $request): JsonResource
    {
        return $this->delete($country, $request);
    }
}
