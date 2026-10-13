<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyInstitutionFactory;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/institution';
    $this->model = LegacyInstitution::class;
    $this->factory = LegacyInstitutionFactory::class;
});

test('index', #[
    GET('/api/institution', ['Institution'], 'Get all institutions'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Institution')
] function () {
    $response = $this->get($this->getUri());
    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});
