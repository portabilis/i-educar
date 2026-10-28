<?php

use App\Models\Builders\LegacySchoolBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

uses(TestCase::class);

test('builder custom', function () {
    $class = new class extends Model
    {
        use HasBuilder;

        public static string $builder = LegacySchoolBuilder::class;
    };

    $this->assertEquals(LegacySchoolBuilder::class, get_class($class->newQuery()));
});

test('builder default', function () {
    $class = new class extends Model
    {
        use HasBuilder;
    };

    $this->assertEquals(Builder::class, get_class($class->newQuery()));
});
