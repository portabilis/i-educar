<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolInepFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->schoolClass = LegacySchoolClassFactory::new()->create();
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);

    // Os parâmetros que os recursos da API costumam ler; cada recurso usa os seus.
    $this->params = [
        'access_key' => config('legacy.apis.access_key'),
        'secret_key' => config('legacy.apis.secret_key'),
        'instituicao_id' => $this->schoolClass->ref_cod_instituicao,
        'escola_id' => $this->schoolClass->ref_ref_cod_escola,
        'curso_id' => $this->schoolClass->ref_cod_curso,
        'serie_id' => $this->schoolClass->ref_ref_cod_serie,
        'turma_id' => $this->schoolClass->getKey(),
        'ano' => $this->schoolClass->ano,
        'ano_letivo' => $this->schoolClass->ano,
        'matricula_id' => $this->registration->getKey(),
        'aluno_id' => $this->registration->ref_cod_aluno,
        'query' => 'a',
    ];
});

test('responde ao recurso', function (string $resource) {
    $this->get('/module/Api/EducacensoAnalise?' . http_build_query(['oper' => 'get', 'resource' => $resource] + $this->params))
        ->assertOk()
        ->assertJsonPath('resource', $resource);
})->with([
    'valida-instituicao',
    'school-is-active',
]);

describe('com uma escola pronta para o Censo', function () {
    beforeEach(function () {
        $this->scenario = EnrollmentScenario::for($this->user, 2030);
        SchoolInepFactory::new()->create(['cod_escola' => $this->scenario->school, 'cod_escola_inep' => 42000001]);

        // Só entram na análise as enturmações anteriores à data de referência do Censo.
        LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['data_educacenso' => '2030-05-29']);

        $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Censo']);
        $this->scenario->enrolled('Ana Censo', $schoolClass, ['data_enturmacao' => '2030-02-10']);

        EmployeeScenario::named('Bia Docente', $this->scenario->institution)
            ->classLink($schoolClass, [], [$this->scenario->discipline('Matemática')]);

        $manager = EmployeeScenario::named('Caio Gestor', $this->scenario->institution);
        SchoolManagerFactory::new()->create(['school_id' => $this->scenario->school, 'employee_id' => $manager->employee]);

        $this->analyse = fn (string $resource) => $this->get('/module/Api/EducacensoAnalise?' . http_build_query([
            'oper' => 'get',
            'resource' => $resource,
            'escola' => $this->scenario->school->getKey(),
            'ano' => 2030,
            'data_ini' => '01/01/2030',
            'data_fim' => '31/12/2030',
        ]))->assertOk();
    });

    test('analisa cada registro da exportação', function (string $resource) {
        ($this->analyse)($resource)
            ->assertJsonPath('resource', $resource)
            ->assertJsonPath('title', fn ($title) => str_starts_with($title, 'Análise exportação - Registro'));
    })->with(['registro-00', 'registro-10', 'registro-20', 'registro-30', 'registro-40', 'registro-50', 'registro-60', 'registro-89', 'registro-90', 'registro-91']);
});
