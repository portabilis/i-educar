<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingDistrictRequest;
use App\Models\District;
use App\Rules\DistrictRestrictOperationRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;

#[Controller]
class DistrictController extends ResourceController
{
    public int $process = 759;

    #[
        GET('/api/district', ['District'], 'Get all districts'),
        Response(200, schemaType: SchemaType::ARRAY, ref: District::class)
    ]
    public function index(District $district, Request $request): JsonResource
    {
        return $this->all($district, $request);
    }

    #[
        POST('/api/district', ['District'], 'Create a District'),
        Property(Type::INT, 'city_id', 'ID of the city', 1),
        Property(Type::STRING, 'name', 'Name of the District', 'São Miguel'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the district', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: District::class),
    ]
    public function store(District $district, AddressingDistrictRequest $request): JsonResource
    {
        return $this->post($district, $request);
    }

    #[
        GET('/api/district/{id}', ['District'], 'Get district with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: District::class)
    ]
    public function show(int $district, Request $request): JsonResource
    {
        return $this->get($district, $request, District::class);
    }

    #[
        PUT('/api/district/{id}', ['District'], 'Update district with ID'),
        Property(Type::INT, 'city_id', 'ID of the city', 1),
        Property(Type::STRING, 'name', 'Name of the District', 'São Miguel'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the district', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: District::class),
    ]
    public function update(District $district, AddressingDistrictRequest $request): JsonResource
    {
        return $this->patch($district, $request);
    }

    #[
        DELETE('/api/district/{id}', ['District'], 'Delete district with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: District::class),
    ]
    public function destroy(District $district, Request $request): JsonResource
    {
        return $this->delete($district, $request);
    }

    public function rules($district, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new DistrictRestrictOperationRule($accessLevel)
        ];
    }
}
