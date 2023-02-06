<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyGrade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeController extends ResourceController
{
    public int $process = 583;

    public function index(LegacyGrade $grade, Request $request): JsonResource
    {
        $this->skipAuthorization();

        return $this->all($grade, $request);
    }
}
