<?php

namespace App\Http\Controllers\Api\Resource\EducationNetwork;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\EducationNetwork\ResourceEducationNetworkRequest;
use App\Models\LegacyEducationNetwork;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceEducationNetworkController extends Controller
{
    public function index(ResourceEducationNetworkRequest $request): JsonResource
    {
        return JsonResource::collection(LegacyEducationNetwork::query()->getResource($request->all()));
    }
}
