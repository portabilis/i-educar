<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacyRegistration;
use Database\Factories\LegacyRegistrationFactory;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

#[Controller]
class RegistrationControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/registration';

    protected string $model = LegacyRegistration::class;

    protected string $factory = LegacyRegistrationFactory::class;

    #[
        GET('/api/registration', ['Registration'], 'Get all registrations'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Registration')
    ]
    public function testIndex(): void
    {
        $this->index();
    }
}
