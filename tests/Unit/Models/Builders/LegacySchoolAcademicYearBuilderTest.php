<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('filtra os anos letivos finalizados, o ano atual e o anterior', function () {
    $current = LegacySchoolAcademicYearFactory::new()->create(['ano' => now()->year, 'andamento' => LegacySchoolAcademicYear::IN_PROGRESS]);
    $last = LegacySchoolAcademicYearFactory::new()->create(['ano' => now()->year - 1, 'andamento' => LegacySchoolAcademicYear::FINALIZED]);
    $keys = fn ($query) => $query->whereKey([$current->getKey(), $last->getKey()])->pluck('id')->all();

    expect($keys(LegacySchoolAcademicYear::query()->notInProgress()))->toBe([$last->getKey()])
        ->and($keys(LegacySchoolAcademicYear::query()->currentYear()))->toBe([$current->getKey()])
        ->and($keys(LegacySchoolAcademicYear::query()->lastYear()))->toBe([$last->getKey()]);
});

test('filtra as etapas a partir de um ano', function () {
    $academicYear = LegacySchoolAcademicYearFactory::new()->create(['ano' => 2030]);
    $stage = LegacyAcademicYearStageFactory::new()->create(['escola_ano_letivo_id' => $academicYear, 'ref_ref_cod_escola' => $academicYear->ref_cod_escola, 'ref_ano' => 2030]);

    expect(LegacyAcademicYearStage::query()->whereYearGte(2030)->whereKey($stage->getKey())->exists())->toBeTrue()
        ->and(LegacyAcademicYearStage::query()->whereYearGte(2031)->whereKey($stage->getKey())->exists())->toBeFalse();
});
