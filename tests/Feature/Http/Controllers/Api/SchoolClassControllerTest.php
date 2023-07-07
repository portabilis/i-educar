<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacySchoolClass;
use Database\Factories\LegacySchoolClassFactory;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

#[Controller]
class SchoolClassControllerTest extends ResourceTestCase
{
    protected string $uri = '/api/school-class';

    protected string $model = LegacySchoolClass::class;

    protected string $factory = LegacySchoolClassFactory::class;

    #[
        GET('/api/school-class', ['SchoolClass'], 'Get all school-classes'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'SchoolClass')
    ]
    public function testIndex(): void
    {
        $this->index();
    }
}
