<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateController extends ResourceController
{
    public function index(State $state, Request $request): JsonResource
    {
        return $this->all($state, $request);
    }

    public function store(State $state, Request $request): JsonResource
    {
        return $this->post($state, $request);
    }

    public function show(State $state, Request $request): JsonResource
    {
        return $this->get($state, $request);
    }

    public function update(State $state, Request $request): JsonResource
    {
        return $this->patch($state, $request);
    }

    public function destroy(State $state, Request $request): JsonResource
    {
        return $this->delete($state, $request);
    }
}
