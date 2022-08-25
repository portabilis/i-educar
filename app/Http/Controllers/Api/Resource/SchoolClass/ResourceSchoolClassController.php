<?php

namespace App\Http\Controllers\Api\Resource\SchoolClass;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\SchoolClass\ResourceSchoolClassRequest;
use App\Models\LegacySchoolClass;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSchoolClassController extends Controller
{
    public function index(ResourceSchoolClassRequest $request): JsonResource
    {
        return JsonResource::collection(LegacySchoolClass::query()->getResource($request->all()));
    }
}
