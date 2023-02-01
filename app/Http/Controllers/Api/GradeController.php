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

    public function store(LegacyGrade $grade, Request $request): JsonResource
    {
        return $this->post($grade, $request);
    }

    public function show(int $grade, Request $request): JsonResource
    {
        return $this->get($grade, $request, LegacyGrade::class);
    }

    public function update(LegacyGrade $grade, Request $request): JsonResource
    {
        return $this->patch($grade, $request);
    }

    public function destroy(LegacyGrade $grade, Request $request): JsonResource
    {
        return $this->delete($grade, $request);
    }
}
