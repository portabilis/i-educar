<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\View\Discipline;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisciplineController extends ResourceController
{
    public function index(Discipline $discipline, Request $request): JsonResource
    {
        return $this->all($discipline, $request);
    }
}
