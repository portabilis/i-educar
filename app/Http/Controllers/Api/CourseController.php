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
}
