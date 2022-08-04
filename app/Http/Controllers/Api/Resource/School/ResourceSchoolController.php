<?php

namespace App\Http\Controllers\Api\Resource\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\School\ResourceSchoolRequest;
use App\Models\LegacySchool;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSchoolController extends Controller
{
    public function index(ResourceSchoolRequest $request): JsonResource
    {
        return JsonResource::collection(LegacySchool::query()->getResource($request->all()));
    }
}
