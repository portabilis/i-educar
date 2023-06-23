<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacyGrade;
use Database\Factories\LegacyGradeFactory;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

#[Controller]
class GradeControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/grade';

    protected string $model = LegacyGrade::class;

    protected string $factory = LegacyGradeFactory::class;

    #[
        GET('/api/grade', ['Grade'], 'Get all grades'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Grade')
    ]
    public function testIndex(): void
    {
        $this->index();
    }
}
