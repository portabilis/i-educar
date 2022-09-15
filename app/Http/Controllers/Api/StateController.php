<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingStateRequest;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateController extends ResourceController
{
    public function index(State $state, Request $request): JsonResource
    {
        return $this->all($state, $request);
    }

    public function store(State $state, AddressingStateRequest $request): JsonResource
    {
        return $this->post($state, $request);
    }

    public function show(int $state, Request $request): JsonResource
    {
        return $this->get($state, $request,State::class);
    }

    public function update(State $state, AddressingStateRequest $request): JsonResource
    {
        return $this->patch($state, $request);
    }

    public function destroy(State $state, Request $request): JsonResource
    {
        return $this->delete($state, $request);
    }
}
