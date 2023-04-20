<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacySchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolController extends ResourceController
{
    public int $process = 561;

    public function index(LegacySchool $school, Request $request): JsonResource
    {
        $this->skipAuthorization();

        return $this->all($school, $request);
    }

    public function filter(Builder $builder, Request $request): void
    {
        $builder->orderByName()->filter($request->all());
    }
}
