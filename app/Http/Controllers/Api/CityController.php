<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingCityRequest;
use App\Models\City;
use App\Rules\Addressing\AddressingCityDistrictRule;
use App\Rules\Addressing\AddressingCityPlaceRule;
use App\Rules\CityRestrictOperationRule;
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
class CityController extends ResourceController
{
    public int $process = 755;

    #[
        GET('/api/city', ['City'], 'Get all cities'),
        Response(200, schemaType: SchemaType::ARRAY, ref: City::class)
    ]
    public function index(City $city, Request $request): JsonResource
    {
        return $this->all($city, $request);
    }

    #[
        POST('/api/city', ['City'], 'Create a city'),
        Property(Type::INT, 'state_id', 'ID of the state', 1),
        Property(Type::STRING, 'name', 'Name of the City', 'Francisco Beltrão'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the city', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: City::class),
    ]
    public function store(City $city, AddressingCityRequest $request): JsonResource
    {
        return $this->post($city, $request);
    }

    #[
        GET('/api/city/{id}', ['City'], 'Get city with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: City::class)
    ]
    public function show(int $city, Request $request): JsonResource
    {
        return $this->get($city, $request, City::class);
    }

    #[
        PUT('/api/city/{id}', ['City'], 'Update city with ID'),
        Property(Type::INT, 'state_id', 'ID of the state', 1),
        Property(Type::STRING, 'name', 'Name of the City', 'Francisco Beltrão'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the city', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: City::class),
    ]
    public function update(City $city, AddressingCityRequest $request): JsonResource
    {
        return $this->patch($city, $request);
    }

    #[
        DELETE('/api/city/{id}', ['City'], 'Delete city with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: City::class),
    ]
    public function destroy(City $city, Request $request): JsonResource
    {
        return $this->delete($city, $request);
    }

    public function rules(City|Model $city, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new CityRestrictOperationRule($accessLevel)
        ];
    }

    protected function deleteRules(Model $model, Request $request)
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new CityRestrictOperationRule($accessLevel),
            new AddressingCityDistrictRule(),
            new AddressingCityPlaceRule()
        ];
    }
}
