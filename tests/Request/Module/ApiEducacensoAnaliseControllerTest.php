<?php

use App\Models\LegacyInstitution;
use Database\Factories\SchoolInepFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/EducacensoAnalise', [
    'valida-instituicao',
    'school-is-active',
]);

describe('com uma escola pronta para o Censo', function () {
    beforeEach(function () {
        $this->scenario = EnrollmentScenario::for($this->user, 2030);
        SchoolInepFactory::new()->create(['cod_escola' => $this->scenario->school, 'cod_escola_inep' => 42000001]);

        // Só entram na análise as enturmações anteriores à data de referência do Censo.
        LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['data_educacenso' => '2030-05-29']);

        $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Censo', 'turma_turno_id' => $this->scenario->period(1)->getKey(), 'tipo_atendimento' => '{0}', 'etapa_educacenso' => 14, 'tipo_mediacao_didatico_pedagogico' => 1]);
        $this->scenario->enrolled('Ana Censo', $schoolClass, ['data_enturmacao' => '2030-02-10']);

        EmployeeScenario::named('Bia Docente', $this->scenario->institution)
            ->classLink($schoolClass, [], [tap($this->scenario->discipline('Matemática'), fn ($discipline) => $discipline->update(['codigo_educacenso' => 3]))]);

        // Uma disciplina sem docente vira aviso na análise do registro 20.
        $this->scenario->discipline('História')->update(['codigo_educacenso' => 12]);

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
