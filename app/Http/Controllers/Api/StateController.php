<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingStateRequest;
use App\Models\State;
use App\Rules\Addressing\AddressingStateRule;
use App\Rules\StateRestrictOperationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateController extends ResourceController
{
    public int $process = 754;

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
        return $this->get($state, $request, State::class);
    }

    public function update(State $state, AddressingStateRequest $request): JsonResource
    {
        return $this->patch($state, $request);
    }

    public function destroy(State $state, Request $request): JsonResource
    {
        return $this->delete($state, $request);
    }

    public function rules($district, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new StateRestrictOperationRule($accessLevel),
        ];
    }

    protected function deleteRules(Model $model, Request $request)
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new StateRestrictOperationRule($accessLevel),
            new AddressingStateRule(),
        ];
    }
}
