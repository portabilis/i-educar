<?php

use App\Models\LegacySchool;
use Database\Factories\LegacySchoolFactory;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/school';
    $this->model = LegacySchool::class;
    $this->factory = LegacySchoolFactory::class;
});

test('index', #[
    GET('/api/school', ['School'], 'Get all schools'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'School')
] function () {
    $this->index();
});
