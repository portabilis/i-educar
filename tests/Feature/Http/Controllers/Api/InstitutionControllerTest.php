<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacyInstitution;
use Database\Factories\LegacyInstitutionFactory;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

#[Controller]
class InstitutionControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/institution';

    protected string $model = LegacyInstitution::class;

    protected string $factory = LegacyInstitutionFactory::class;

    #[
        GET('/api/institution', ['Institution'], 'Get all institutions'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Institution')
    ]
    public function testIndex(): void
    {
        $response = $this->get($this->getUri());
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }
}
