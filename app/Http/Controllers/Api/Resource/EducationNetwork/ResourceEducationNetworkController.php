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
        $institution = $request->get('institution');

        $education_networks = LegacyEducationNetwork::whereInstitution($institution)
            ->active()->orderByName()
            ->get(['cod_escola_rede_ensino as id', 'nm_rede as name']);

        JsonResource::withoutWrapping();
        return JsonResource::collection($education_networks);
    }
}
