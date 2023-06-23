<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacySchool;
use Database\Factories\LegacySchoolFactory;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

#[Controller]
class SchoolControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/school';

    protected string $model = LegacySchool::class;

    protected string $factory = LegacySchoolFactory::class;

    #[
        GET('/api/school', ['School'], 'Get all schools'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'School')
    ]
    public function testIndex(): void
    {
        $this->index();
    }
}
