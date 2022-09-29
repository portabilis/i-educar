<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Http\Requests\Api\Addressing\AddressingCountryRequest;
use App\Models\Country;
use App\Rules\Addressing\AddressingCountryRule;
use App\Rules\CountryRestrictOperationRule;
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
class CountryController extends ResourceController
{
    public int $process = 753;

    #[
        GET('/api/country', ['Country'], 'Get all countries'),
        Response(200, schemaType: SchemaType::ARRAY, ref: Country::class)
    ]
    public function index(Country $country, Request $request): JsonResource
    {
        return $this->all($country, $request);
    }

    #[
        POST('/api/country', ['Country'], 'Create a country'),
        Property(Type::STRING, 'name', 'Name of the country', 'Brasil'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the country', 12345),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: Country::class),
    ]
    public function store(Country $country, AddressingCountryRequest $request): JsonResource
    {
        return $this->post($country, $request);
    }

    #[
        GET('/api/country/{id}', ['Country'], 'Get country with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, ref: Country::class)
    ]
    public function show(int $country, Request $request): JsonResource
    {
        return $this->get($country, $request, Country::class);
    }

    #[
        PUT('/api/country/{id}', ['Country'], 'Update country with ID'),
        Property(Type::STRING, 'name', 'Name of the country', 'Brasil'),
        Property(Type::INT, 'ibge_code', 'IBGE code of the country', 12345),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: Country::class),
    ]
    public function update(Country $country, AddressingCountryRequest $request): JsonResource
    {
        return $this->patch($country, $request);
    }

    #[
        DELETE('/api/country/{id}', ['Country'], 'Delete country with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: Country::class),
    ]
    public function destroy(Country $country, Request $request): JsonResource
    {
        return $this->delete($country, $request);
    }

    public function rules($district, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new CountryRestrictOperationRule($accessLevel)
        ];
    }

    protected function deleteRules(Model $model, Request $request)
    {
        $accessLevel = $request->user()->getLevel();

        return [
            new CountryRestrictOperationRule($accessLevel),
            new AddressingCountryRule()
        ];
    }
}
