<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolInepFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    SchoolInepFactory::new()->create(['cod_escola' => $this->scenario->school, 'cod_escola_inep' => 42000001]);

    // Só entram na exportação as enturmações anteriores à data de referência do Censo.
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['data_educacenso' => '2030-05-29']);

    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Censo', 'tipo_atendimento' => '{0}', 'etapa_educacenso' => 14, 'tipo_mediacao_didatico_pedagogico' => 1]);
    $this->scenario->enrolled('Ana Censo', $this->schoolClass, ['data_enturmacao' => '2030-02-10']);

    $this->teacher = EmployeeScenario::named('Bia Docente', $this->scenario->institution);
    $this->teacher->classLink($this->schoolClass, [], [tap($this->scenario->discipline('Matemática'), fn ($discipline) => $discipline->update(['codigo_educacenso' => 3]))]);

    $manager = EmployeeScenario::named('Caio Gestor', $this->scenario->institution);
    SchoolManagerFactory::new()->create(['school_id' => $this->scenario->school, 'employee_id' => $manager->employee]);

    $this->export = fn (array $query = []) => $this->get('/module/Api/EducacensoExport?' . http_build_query($query + [
        'oper' => 'get',
        'resource' => 'educacenso-export',
        'escola' => $this->scenario->school->getKey(),
        'ano' => 2030,
        'data_ini' => '01/01/2030',
        'data_fim' => '31/12/2030',
    ]))->assertOk();
});

test('exporta os registros da escola, das turmas, das pessoas, dos gestores, dos docentes e dos alunos', function () {
    $lines = explode(PHP_EOL, trim(($this->export)()->json('conteudo')));
    $types = array_map(fn ($line) => strtok($line, '|'), $lines);

    expect($types)->toBe(['00', '10', '20', '30', '30', '30', '40', '50', '60', '99'])
        ->and($lines[0])->toStartWith('00|42000001|1|01/02/2030|15/12/2030|ESCOLA AURORA|')
        ->and($lines[2])->toContain('|TURMA CENSO|')
        ->and(implode(PHP_EOL, $lines))->toContain('|ANA CENSO|')->toContain('|BIA DOCENTE|')->toContain('|CAIO GESTOR|');
});

test('a turma integral sai uma vez por turno dos alunos, ou uma vez só quando todos estão no integral', function () {
    foreach ([1, 2, 4] as $period) {
        $this->scenario->period($period);
    }

    $integral = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Integral',
        'turma_turno_id' => 4,
        'hora_inicial_matutino' => '07:30',
        'hora_final_matutino' => '11:30',
        'hora_inicial_vespertino' => '13:00',
        'hora_final_vespertino' => '17:00',
    ]);
    $this->scenario->enrolled('Davi Manhã', $integral, ['data_enturmacao' => '2030-02-10', 'turno_id' => 1]);
    $this->scenario->enrolled('Eva Tarde', $integral, ['data_enturmacao' => '2030-02-10', 'turno_id' => 2]);

    $this->teacher->classLink($integral);

    // Com todos os alunos no integral, a turma sai uma vez só.
    $plena = $this->scenario->schoolClass(['nm_turma' => 'Turma Plena', 'turma_turno_id' => 4]);
    $this->scenario->enrolled('Fabio Integral', $plena, ['data_enturmacao' => '2030-02-10', 'turno_id' => null]);
    $this->teacher->classLink($plena);

    $content = ($this->export)()->json('conteudo');

    expect($content)
        ->toContain("|{$integral->getKey()}-1||TURMA INTEGRAL - MATUTINO|")
        ->toContain("|{$integral->getKey()}-2||TURMA INTEGRAL - VESPERTINO|")
        ->toContain("|{$plena->getKey()}||TURMA PLENA|")
        ->toContain("|{$integral->getKey()}-1|")
        ->toContain("|{$integral->getKey()}-2|");
});
