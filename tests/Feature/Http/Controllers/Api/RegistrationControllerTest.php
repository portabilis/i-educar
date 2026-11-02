<?php

use App\Models\LegacyRegistration;
use Database\Factories\LegacyRegistrationFactory;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/registration';
    $this->model = LegacyRegistration::class;
    $this->factory = LegacyRegistrationFactory::class;
});

test('index', #[
    GET('/api/registration', ['Registration'], 'Get all registrations'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Registration')
] function () {
    $this->index();
});
