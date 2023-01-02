<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyDeficiency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacyDeficiencyController extends ResourceController
{
    public function index(LegacyDeficiency $deficiency, Request $request): JsonResource
    {
        return $this->all($deficiency, $request);
    }

    public function store(LegacyDeficiency $deficiency, Request $request): JsonResource
    {
        return $this->post($deficiency, $request);
    }

    public function show(int $id, Request $request): JsonResource
    {
        return $this->get($id, $request, LegacyDeficiency::class);
    }

    public function update(LegacyDeficiency $deficiency, Request $request): JsonResource
    {
        return $this->patch($deficiency, $request);
    }

    public function destroy(LegacyDeficiency $deficiency, Request $request): JsonResource
    {
        return $this->delete($deficiency, $request);
    }
}
