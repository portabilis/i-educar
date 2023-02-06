<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyInstitution;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionController extends ResourceController
{
    public int $process = 753;

    public function index(LegacyInstitution $institution, Request $request): JsonResource
    {
        $this->skipAuthorization();

        return $this->all($institution, $request);
    }
}
