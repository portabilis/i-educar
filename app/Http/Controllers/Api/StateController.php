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
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\POST;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;

#[Controller]
class StateController extends ResourceController
{
    public int $process = 754;

    #[
        GET('/api/state', ['State'], 'Get all states'),
        Response(200, schemaType: SchemaType::ARRAY, ref: State::class)
    ]
    public function index(State $state, Request $request): JsonResource
    {
        return $this->all($state, $request);
    }

    #[
        POST('/api/state', ['State'], 'Create a state'),
        Property(Type::INT, 'country_id', 'ID of the country', 1),
        Property(Type::STRING, 'name', 'Name of the State', 'Paraná'),
        Property(Type::STRING, 'abbreviation', 'Abbreviation of the State', 'PR'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the state', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: State::class),
    ]
    public function store(State $state, AddressingStateRequest $request): JsonResource
    {
        return $this->post($state, $request);
    }

    #[
        GET('/api/state/{id}', ['State'], 'Get state with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: State::class)
    ]
    public function show(int $state, Request $request): JsonResource
    {
        return $this->get($state, $request, State::class);
    }

    #[
        PUT('/api/state/{id}', ['State'], 'Update state with ID'),
        Property(Type::INT, 'country_id', 'ID of the country', 1),
        Property(Type::STRING, 'name', 'Name of the State', 'Paraná'),
        Property(Type::STRING, 'abbreviation', 'Abbreviation of the State', 'PR'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the state', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: State::class),
    ]
    public function update(State $state, AddressingStateRequest $request): JsonResource
    {
        return $this->patch($state, $request);
    }

    #[
        DELETE('/api/state/{id}', ['State'], 'Delete state with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: State::class),
    ]
    public function destroy(State $state, Request $request): JsonResource
    {
        return $this->delete($state, $request);
    }

    public function rules($district, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new StateRestrictOperationRule($accessLevel)
        ];
    }

    protected function deleteRules(Model $model, Request $request)
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new StateRestrictOperationRule($accessLevel),
            new AddressingStateRule()
        ];
    }
}
