<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacyCourse;
use Database\Factories\LegacyCourseFactory;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

#[Controller]
class CourseControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/course';

    protected string $model = LegacyCourse::class;

    protected string $factory = LegacyCourseFactory::class;

    #[
        GET('/api/course', ['Course'], 'Get all courses'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Course')
    ]
    public function testIndex(): void
    {
        $this->index();
    }
}
