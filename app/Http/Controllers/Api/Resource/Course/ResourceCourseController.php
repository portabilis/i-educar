<?php

namespace App\Http\Controllers\Api\Resource\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\Course\ResourceCourseRequest;
use App\Models\LegacyCourse;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCourseController extends Controller
{
    public function index(ResourceCourseRequest $request): JsonResource
    {
        return JsonResource::collection(LegacyCourse::query()->getResource($request->all()));
    }
}
