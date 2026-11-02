<?php

use App\Models\Enums\AbsenceDelayType;
use App\Models\LegacyAbsenceDelay;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyAbsenceDelayFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->cenario = function (AbsenceDelayType $type): array {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        $school = LegacySchoolFactory::new()->create();
        $employee = EmployeeFactory::new()->create();
        $employeeRole = LegacyEmployeeRoleFactory::new()->create([
            'ref_cod_servidor' => $employee->getKey(),
        ]);

        return [
            'school' => $school,
            'employee' => $employee,
            'payload' => [
                'tipoacao' => 'Novo',
                'ref_cod_instituicao' => $school->ref_cod_instituicao,
                'ref_cod_escola' => $school->getKey(),
                'ref_cod_servidor' => $employee->getKey(),
                'ref_cod_servidor_funcao' => $employeeRole->getKey(),
                'tipo' => $type->value,
                'data_falta_atraso' => '12/08/2026',
                'justificada' => 0,
            ],
        ];
    };

    $this->payloadDeEdicao = function (LegacyAbsenceDelay $absenceDelay): array {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        return [
            'tipoacao' => 'Editar',
            'cod_falta_atraso' => $absenceDelay->getKey(),
            'ref_cod_instituicao' => $absenceDelay->ref_ref_cod_instituicao,
            'ref_cod_escola' => $absenceDelay->ref_cod_escola,
            'ref_cod_servidor' => $absenceDelay->ref_cod_servidor,
            'ref_cod_servidor_funcao' => $absenceDelay->ref_cod_servidor_funcao,
            'tipo' => $absenceDelay->tipo,
            'data_falta_atraso' => $absenceDelay->data_falta_atraso->format('d/m/Y'),
            'justificada' => $absenceDelay->justificada,
        ];
    };
});

test('cadastro de falta nao grava horas', function () {
    ['payload' => $payload, 'school' => $school] = ($this->cenario)(AbsenceDelayType::ABSENCE);

    $this->post('/intranet/educar_falta_atraso_cad.php', $payload)
        ->assertRedirectContains('educar_falta_atraso_lst.php');

    $this->assertDatabaseHas('pmieducar.falta_atraso', [
        'ref_cod_escola' => $school->getKey(),
        'tipo' => AbsenceDelayType::ABSENCE->value,
        'qtd_horas' => null,
        'qtd_min' => null,
    ]);
});

test('cadastro de falta ignora horas enviadas no formulario', function () {
    ['payload' => $payload, 'school' => $school] = ($this->cenario)(AbsenceDelayType::ABSENCE);

    $payload['qtd_horas'] = 5;
    $payload['qtd_min'] = 10;

    $this->post('/intranet/educar_falta_atraso_cad.php', $payload)
        ->assertRedirectContains('educar_falta_atraso_lst.php');

    $this->assertDatabaseHas('pmieducar.falta_atraso', [
        'ref_cod_escola' => $school->getKey(),
        'tipo' => AbsenceDelayType::ABSENCE->value,
        'qtd_horas' => null,
        'qtd_min' => null,
    ]);
});

test('cadastro de falta nao grava horas com alocacao no dia', function () {
    ['payload' => $payload, 'school' => $school, 'employee' => $employee] = ($this->cenario)(AbsenceDelayType::ABSENCE);

    EmployeeAllocationFactory::new()->create([
        'ref_cod_servidor' => $employee->getKey(),
        'ref_cod_escola' => $school->getKey(),
        'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
        'hora_inicial' => '07:00:00',
        'hora_final' => '11:00:00',
        'dia_semana' => 4,
        'ativo' => 1,
    ]);

    $this->post('/intranet/educar_falta_atraso_cad.php', $payload)
        ->assertRedirectContains('educar_falta_atraso_lst.php');

    $this->assertDatabaseHas('pmieducar.falta_atraso', [
        'ref_cod_escola' => $school->getKey(),
        'tipo' => AbsenceDelayType::ABSENCE->value,
        'qtd_horas' => null,
        'qtd_min' => null,
    ]);
});

test('cadastro de atraso grava horas informadas', function () {
    ['payload' => $payload, 'school' => $school] = ($this->cenario)(AbsenceDelayType::DELAY);

    $payload['qtd_horas'] = 2;
    $payload['qtd_min'] = 30;

    $this->post('/intranet/educar_falta_atraso_cad.php', $payload)
        ->assertRedirectContains('educar_falta_atraso_lst.php');

    $this->assertDatabaseHas('pmieducar.falta_atraso', [
        'ref_cod_escola' => $school->getKey(),
        'tipo' => AbsenceDelayType::DELAY->value,
        'qtd_horas' => 2,
        'qtd_min' => 30,
    ]);
});

test('edicao de falta anula horas gravadas', function () {
    $absence = LegacyAbsenceDelayFactory::new()->create([
        'tipo' => AbsenceDelayType::ABSENCE->value,
        'qtd_horas' => 36,
        'qtd_min' => 0,
    ]);

    $payload = ($this->payloadDeEdicao)($absence);

    $this->post('/intranet/educar_falta_atraso_cad.php', $payload)
        ->assertRedirectContains('educar_falta_atraso_lst.php');

    $this->assertDatabaseHas('pmieducar.falta_atraso', [
        'cod_falta_atraso' => $absence->getKey(),
        'qtd_horas' => null,
        'qtd_min' => null,
    ]);
});

test('edicao de atraso mantem horas informadas', function () {
    $delay = LegacyAbsenceDelayFactory::new()->create([
        'tipo' => AbsenceDelayType::DELAY->value,
        'qtd_horas' => 3,
        'qtd_min' => 45,
    ]);

    $payload = ($this->payloadDeEdicao)($delay);
    $payload['qtd_horas'] = 1;
    $payload['qtd_min'] = 15;

    $this->post('/intranet/educar_falta_atraso_cad.php', $payload)
        ->assertRedirectContains('educar_falta_atraso_lst.php');

    $this->assertDatabaseHas('pmieducar.falta_atraso', [
        'cod_falta_atraso' => $delay->getKey(),
        'qtd_horas' => 1,
        'qtd_min' => 15,
    ]);
});
