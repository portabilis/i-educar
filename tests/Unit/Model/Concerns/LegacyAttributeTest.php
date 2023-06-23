<?php

namespace Tests\Unit\Model\Concerns;

use App\Models\Builders\LegacySchoolBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class LegacyAttributeTest extends TestCase
{
    public function testeBuilderCustom(): void
    {
        $class = new class() extends Model
        {
            use LegacyAttribute;

            public string $builder = LegacySchoolBuilder::class;
        };

        $this->assertEquals(LegacySchoolBuilder::class, get_class($class->newQuery()));
    }

    public function testeBuilderDefault(): void
    {
        $class = new class() extends Model
        {
            use LegacyAttribute;
        };

        $this->assertEquals(Builder::class, get_class($class->newQuery()));
    }
}
