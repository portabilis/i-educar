<?php

use App\Models\LegacyPeriod;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacySchoolClassFactory;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('preenche o turno das turmas sem turno a partir do horário', function () {
    foreach ([Period::MORNING => 'Matutino', Period::NIGTH => 'Noturno'] as $id => $name) {
        LegacyPeriod::query()->find($id) ?? LegacyPeriodFactory::new()->create(['id' => $id, 'nome' => $name]);
    }

    $morning = LegacySchoolClassFactory::new()->create(['turma_turno_id' => null, 'hora_inicial' => '07:30', 'hora_final' => '11:30', 'ano' => 2030]);
    $night = LegacySchoolClassFactory::new()->create(['turma_turno_id' => null, 'hora_inicial' => '19:00', 'hora_final' => '22:30', 'ano' => 2030]);
    $old = LegacySchoolClassFactory::new()->create(['turma_turno_id' => null, 'hora_inicial' => '07:30', 'hora_final' => '11:30', 'ano' => 2018]);

    $this->artisan('update:school-class-period')->assertExitCode(0);

    expect($morning->refresh()->turma_turno_id)->toBe(Period::MORNING)
        ->and($night->refresh()->turma_turno_id)->toBe(Period::NIGTH)
        ->and($old->refresh()->turma_turno_id)->toBeNull();
});
