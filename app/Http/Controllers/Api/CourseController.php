<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyCourse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseController extends ResourceController
{
    public int $process = 566;

    public function index(LegacyCourse $course, Request $request): JsonResource
    {
        $this->skipAuthorization();

        return $this->all($course, $request);
    }

    public function store(LegacyCourse $course, Request $request): JsonResource
    {
        return $this->post($course, $request);
    }

    public function show(int $course, Request $request): JsonResource
    {
        return $this->get($course, $request, LegacyCourse::class);
    }

    public function update(LegacyCourse $course, Request $request): JsonResource
    {
        return $this->patch($course, $request);
    }

    public function destroy(LegacyCourse $course, Request $request): JsonResource
    {
        return $this->delete($course, $request);
    }
}
