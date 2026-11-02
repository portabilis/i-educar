<?php

use App\Models\LegacyDiscipline;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('o componente do vínculo expõe a carga horária e o componente curricular', function () {
    $link = LegacySchoolClassTeacherDisciplineFactory::new()->create();
    $link->carga_horaria = 40;

    expect($link->workload)->toBe(40)
        ->and($link->discipline)->toBeInstanceOf(LegacyDiscipline::class)
        ->and($link->discipline->getKey())->toBe($link->componente_curricular_id);
});
