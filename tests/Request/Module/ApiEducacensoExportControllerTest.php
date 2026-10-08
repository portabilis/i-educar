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

    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Censo']);
    $this->scenario->enrolled('Ana Censo', $this->schoolClass, ['data_enturmacao' => '2030-02-10']);

    $this->teacher = EmployeeScenario::named('Bia Docente', $this->scenario->institution);
    $this->teacher->classLink($this->schoolClass, [], [$this->scenario->discipline('Matemática')]);

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
