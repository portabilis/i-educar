<?php

use App\Services\CyclicRegimeService;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('junta as matrículas em andamento ou aprovadas do aluno em cada série ativa do ciclo, na ordem das etapas', function () {
    $course = LegacyCourseFactory::new()->create();
    $student = LegacyStudentFactory::new()->create();
    $second = LegacyGradeFactory::new()->create(['ref_cod_curso' => $course, 'etapa_curso' => 2]);
    $first = LegacyGradeFactory::new()->create(['ref_cod_curso' => $course, 'etapa_curso' => 1]);
    LegacyGradeFactory::new()->create(['ref_cod_curso' => $course, 'etapa_curso' => 3]);
    LegacyGradeFactory::new()->create(['ref_cod_curso' => $course, 'etapa_curso' => 4, 'ativo' => 0]);

    $registration = fn ($grade, $status) => LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $student,
        'ref_cod_curso' => $course,
        'ref_ref_cod_serie' => $grade,
        'aprovado' => $status,
    ]);

    $approved = $registration($first, App_Model_MatriculaSituacao::APROVADO);
    $ongoing = $registration($second, App_Model_MatriculaSituacao::EM_ANDAMENTO);
    $registration($second, App_Model_MatriculaSituacao::REPROVADO);

    $service = new CyclicRegimeService;

    expect(collect($service->getAllRegistrationsOfCycle($ongoing->getKey()))->map->getKey()->all())->toBe([$approved->getKey(), $ongoing->getKey()])
        ->and($service->getAllGradesOfCycleByRegistration($ongoing)->modelKeys())->toHaveCount(3);
});
