<?php

namespace Tests\Api;

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

class LegacyAbsenceDelayCadTest extends TestCase
{
    use DatabaseTransactions;

    public const ROUTE = '/intranet/educar_falta_atraso_cad.php';

    public function test_cadastro_de_falta_nao_grava_horas(): void
    {
        ['payload' => $payload, 'school' => $school] = $this->cenario(AbsenceDelayType::ABSENCE);

        $this->post(self::ROUTE, $payload)
            ->assertRedirectContains('educar_falta_atraso_lst.php');

        $this->assertDatabaseHas('pmieducar.falta_atraso', [
            'ref_cod_escola' => $school->getKey(),
            'tipo' => AbsenceDelayType::ABSENCE->value,
            'qtd_horas' => null,
            'qtd_min' => null,
        ]);
    }

    public function test_cadastro_de_falta_ignora_horas_enviadas_no_formulario(): void
    {
        ['payload' => $payload, 'school' => $school] = $this->cenario(AbsenceDelayType::ABSENCE);

        $payload['qtd_horas'] = 5;
        $payload['qtd_min'] = 10;

        $this->post(self::ROUTE, $payload)
            ->assertRedirectContains('educar_falta_atraso_lst.php');

        $this->assertDatabaseHas('pmieducar.falta_atraso', [
            'ref_cod_escola' => $school->getKey(),
            'tipo' => AbsenceDelayType::ABSENCE->value,
            'qtd_horas' => null,
            'qtd_min' => null,
        ]);
    }

    public function test_cadastro_de_falta_nao_grava_horas_com_alocacao_no_dia(): void
    {
        ['payload' => $payload, 'school' => $school, 'employee' => $employee] = $this->cenario(AbsenceDelayType::ABSENCE);

        EmployeeAllocationFactory::new()->create([
            'ref_cod_servidor' => $employee->getKey(),
            'ref_cod_escola' => $school->getKey(),
            'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
            'hora_inicial' => '07:00:00',
            'hora_final' => '11:00:00',
            'dia_semana' => 4,
            'ativo' => 1,
        ]);

        $this->post(self::ROUTE, $payload)
            ->assertRedirectContains('educar_falta_atraso_lst.php');

        $this->assertDatabaseHas('pmieducar.falta_atraso', [
            'ref_cod_escola' => $school->getKey(),
            'tipo' => AbsenceDelayType::ABSENCE->value,
            'qtd_horas' => null,
            'qtd_min' => null,
        ]);
    }

    public function test_cadastro_de_atraso_grava_horas_informadas(): void
    {
        ['payload' => $payload, 'school' => $school] = $this->cenario(AbsenceDelayType::DELAY);

        $payload['qtd_horas'] = 2;
        $payload['qtd_min'] = 30;

        $this->post(self::ROUTE, $payload)
            ->assertRedirectContains('educar_falta_atraso_lst.php');

        $this->assertDatabaseHas('pmieducar.falta_atraso', [
            'ref_cod_escola' => $school->getKey(),
            'tipo' => AbsenceDelayType::DELAY->value,
            'qtd_horas' => 2,
            'qtd_min' => 30,
        ]);
    }

    public function test_edicao_de_falta_anula_horas_gravadas(): void
    {
        $absence = LegacyAbsenceDelayFactory::new()->create([
            'tipo' => AbsenceDelayType::ABSENCE->value,
            'qtd_horas' => 36,
            'qtd_min' => 0,
        ]);

        $payload = $this->payloadDeEdicao($absence);

        $this->post(self::ROUTE, $payload)
            ->assertRedirectContains('educar_falta_atraso_lst.php');

        $this->assertDatabaseHas('pmieducar.falta_atraso', [
            'cod_falta_atraso' => $absence->getKey(),
            'qtd_horas' => null,
            'qtd_min' => null,
        ]);
    }

    public function test_edicao_de_atraso_mantem_horas_informadas(): void
    {
        $delay = LegacyAbsenceDelayFactory::new()->create([
            'tipo' => AbsenceDelayType::DELAY->value,
            'qtd_horas' => 3,
            'qtd_min' => 45,
        ]);

        $payload = $this->payloadDeEdicao($delay);
        $payload['qtd_horas'] = 1;
        $payload['qtd_min'] = 15;

        $this->post(self::ROUTE, $payload)
            ->assertRedirectContains('educar_falta_atraso_lst.php');

        $this->assertDatabaseHas('pmieducar.falta_atraso', [
            'cod_falta_atraso' => $delay->getKey(),
            'qtd_horas' => 1,
            'qtd_min' => 15,
        ]);
    }

    private function cenario(AbsenceDelayType $type): array
    {
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
    }

    private function payloadDeEdicao(LegacyAbsenceDelay $absenceDelay): array
    {
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
    }
}
