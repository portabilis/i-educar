<?php

namespace App\Http\Controllers\Api\Resource\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\Country\ResourceCountryRequest;
use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCountryController extends Controller
{
    public function index(ResourceCountryRequest $request): JsonResource
    {
        return JsonResource::collection(Country::query()->getResource($request->all()));
    }
}
