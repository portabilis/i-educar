<?php

namespace Tests\Unit\Model\Concerns;

use App\Models\Builders\LegacySchoolBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class LegacyAttributeTest extends TestCase
{
    public function testeBuilderCustom(): void
    {
        $class = new class() extends Model
        {
            use HasBuilder;

            public static string $builder = LegacySchoolBuilder::class;
        };

        $this->assertEquals(LegacySchoolBuilder::class, get_class($class->newQuery()));
    }

    public function testeBuilderDefault(): void
    {
        $class = new class() extends Model
        {
            use HasBuilder;
        };

        $this->assertEquals(Builder::class, get_class($class->newQuery()));
    }
}
