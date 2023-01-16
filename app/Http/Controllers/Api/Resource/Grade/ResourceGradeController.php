<?php

namespace App\Http\Controllers\Api\Resource\Grade;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\Grade\ResourceGradeRequest;
use App\Models\LegacyGrade;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceGradeController extends Controller
{
    public function index(ResourceGradeRequest $request): JsonResource
    {
        return JsonResource::collection(LegacyGrade::query()->getResource($request->all()));
    }
}
