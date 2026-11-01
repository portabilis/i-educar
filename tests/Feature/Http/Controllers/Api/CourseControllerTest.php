<?php

use App\Models\LegacyCourse;
use Database\Factories\LegacyCourseFactory;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/course';
    $this->model = LegacyCourse::class;
    $this->factory = LegacyCourseFactory::class;
});

test('index', #[
    GET('/api/course', ['Course'], 'Get all courses'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Course')
] function () {
    $this->index();
});
