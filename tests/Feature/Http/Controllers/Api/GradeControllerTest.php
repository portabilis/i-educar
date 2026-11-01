<?php

use App\Models\LegacyGrade;
use Database\Factories\LegacyGradeFactory;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/grade';
    $this->model = LegacyGrade::class;
    $this->factory = LegacyGradeFactory::class;
});

test('index', #[
    GET('/api/grade', ['Grade'], 'Get all grades'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Grade')
] function () {
    $this->index();
});
