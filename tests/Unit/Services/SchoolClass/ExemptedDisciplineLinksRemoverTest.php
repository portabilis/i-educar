<?php

use App\Models\LegacySchoolClassTeacherDiscipline;
use App\Services\SchoolClass\ExemptedDisciplineLinksRemover;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('remove dos vínculos de professor da turma o componente dispensado, marcando os vínculos tocados', function () {
    $exempted = LegacyDisciplineFactory::new()->create();
    $other = LegacyDisciplineFactory::new()->create();
    $schoolClass = LegacySchoolClassFactory::new()->create(['ref_cod_disciplina_dispensada' => $exempted->getKey()]);

    $affected = LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $schoolClass, 'updated_at' => '2030-01-01 00:00:00']);
    $untouched = LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $schoolClass, 'updated_at' => '2030-01-01 00:00:00']);
    $elsewhere = LegacySchoolClassTeacherFactory::new()->create(['updated_at' => '2030-01-01 00:00:00']);

    foreach ([[$affected, $exempted], [$affected, $other], [$untouched, $other], [$elsewhere, $exempted]] as [$link, $discipline]) {
        LegacySchoolClassTeacherDisciplineFactory::new()->create(['professor_turma_id' => $link, 'componente_curricular_id' => $discipline]);
    }

    (new ExemptedDisciplineLinksRemover)->remove($schoolClass);

    $disciplines = fn ($link) => LegacySchoolClassTeacherDiscipline::query()->where('professor_turma_id', $link->getKey())->pluck('componente_curricular_id')->all();

    expect($disciplines($affected))->toBe([$other->getKey()])
        ->and($disciplines($untouched))->toBe([$other->getKey()])
        ->and($disciplines($elsewhere))->toBe([$exempted->getKey()])
        ->and($affected->refresh()->updated_at->year)->toBe(now()->year)
        ->and($untouched->refresh()->updated_at->year)->toBe(2030)
        ->and($elsewhere->refresh()->updated_at->year)->toBe(2030);
});
