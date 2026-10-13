<?php

namespace Tests\Request\Support;

use App\Models\Employee;
use App\Models\EmployeeAllocation;
use App\Models\EmployeeWithdrawal;
use App\Models\Enums\AbsenceDelayType;
use App\Models\LegacyAbsenceDelay;
use App\Models\LegacyAbsenceDelayCompensate;
use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyIndividual;
use App\Models\LegacyRole;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\WithdrawalReason;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\EmployeeInepFactory;
use Database\Factories\LegacyAbsenceDelayCompensateFactory;
use Database\Factories\LegacyAbsenceDelayFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\WithdrawalReasonFactory;
use Illuminate\Support\Facades\DB;

/**
 * Um servidor de nome conhecido numa instituição e, a pedido, o que as telas
 * de servidor mostram em volta dele: funções com matrícula, componentes que
 * leciona, alocações em escolas, vínculos com turmas, faltas e atrasos,
 * compensações, afastamentos e o código do Inep.
 *
 * As factories soltas criam servidor, função e alocação para pessoas e
 * instituições diferentes; aqui tudo aponta para o mesmo servidor, e os textos
 * são fixos para que os testes afirmem o que a tela mostra.
 */
final class EmployeeScenario
{
    public LegacyIndividual $individual;

    public Employee $employee;

    private function __construct(public int $institution) {}

    /**
     * Um servidor com esse nome na instituição, com escolaridade "Ensino
     * Superior" e 40 horas, sem função nem alocação.
     *
     * @param array<string, mixed> $employee   Colunas de `pmieducar.servidor`.
     * @param array<string, mixed> $individual Colunas de `cadastro.fisica`.
     */
    public static function named(string $name, int $institution, array $employee = [], array $individual = []): self
    {
        $scenario = new self($institution);

        $scenario->individual = StudentScenario::individual($name, $individual + ['sexo' => 'F']);
        $scenario->employee = EmployeeFactory::new()->create($employee + [
            'cod_servidor' => $scenario->individual->getKey(),
            'ref_cod_instituicao' => $institution,
            'carga_horaria' => 40,
            'ref_idesco' => LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Ensino Superior']),
        ]);

        return $scenario;
    }

    /**
     * Uma função do servidor, com a matrícula dada; cria a função na
     * instituição quando recebe só o nome.
     */
    public function role(string|LegacyRole $role, string $registration, bool $teacher = true): LegacyEmployeeRole
    {
        if (is_string($role)) {
            $role = LegacyRoleFactory::new()->create([
                'nm_funcao' => $role,
                'abreviatura' => mb_substr($role, 0, 3),
                'professor' => (int) $teacher,
                'ref_cod_instituicao' => $this->institution,
            ]);
        }

        return LegacyEmployeeRoleFactory::new()->create([
            'ref_cod_servidor' => $this->employee->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_funcao' => $role->getKey(),
            'matricula' => $registration,
        ]);
    }

    /**
     * Uma alocação do servidor na escola: 20 horas no turno da manhã do ano
     * corrente, salvo o que vier em `$attributes`.
     *
     * @param array<string, mixed> $attributes Colunas de `pmieducar.servidor_alocacao`.
     */
    public function allocation(LegacySchool $school, array $attributes = []): EmployeeAllocation
    {
        return EmployeeAllocationFactory::new()->create($attributes + [
            'ref_cod_servidor' => $this->employee->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_servidor_funcao' => null,
            'ref_cod_funcionario_vinculo' => null,
            'periodo' => 1,
            'carga_horaria' => '20:00:00',
            'ano' => now()->year,
            'data_admissao' => null,
            'data_saida' => null,
            'ativo' => 1,
        ]);
    }

    /**
     * Um afastamento do servidor, em aberto (sem data de retorno) salvo o que
     * vier em `$attributes`.
     *
     * @param array<string, mixed> $attributes Colunas de `pmieducar.servidor_afastamento`.
     */
    public function withdrawal(string|WithdrawalReason $reason = 'Licença médica', array $attributes = []): EmployeeWithdrawal
    {
        if (is_string($reason)) {
            $reason = WithdrawalReasonFactory::new()->create(['nm_motivo' => $reason, 'ref_cod_instituicao' => $this->institution]);
        }

        return EmployeeWithdrawal::query()->create($attributes + [
            'ref_cod_servidor' => $this->employee->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_motivo_afastamento' => $reason->getKey(),
            'ref_usuario_cad' => $this->employee->getKey(),
            'data_saida' => '2030-03-02',
            'data_retorno' => null,
            'ativo' => 1,
        ]);
    }

    /**
     * Um componente curricular que o servidor leciona num curso, pela função
     * dada; marca também o curso como ministrado.
     */
    public function discipline(LegacyEmployeeRole $role, LegacyDiscipline $discipline, LegacyCourse $course): self
    {
        DB::table('pmieducar.servidor_disciplina')->insert([
            'ref_cod_disciplina' => $discipline->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_servidor' => $this->employee->getKey(),
            'ref_cod_curso' => $course->getKey(),
            'ref_cod_funcao' => $role->getKey(),
        ]);

        DB::table('pmieducar.servidor_curso_ministra')->insertOrIgnore([
            'ref_cod_curso' => $course->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_servidor' => $this->employee->getKey(),
        ]);

        return $this;
    }

    /**
     * O vínculo do servidor com a turma: docente, com contrato temporário, no
     * ano da turma, lecionando os componentes dados.
     *
     * @param array<string, mixed>         $attributes  Colunas de `modules.professor_turma`.
     * @param array<int, LegacyDiscipline> $disciplines
     */
    public function classLink(LegacySchoolClass $schoolClass, array $attributes = [], array $disciplines = []): LegacySchoolClassTeacher
    {
        $link = LegacySchoolClassTeacherFactory::new()->create($attributes + [
            'ano' => $schoolClass->ano,
            'instituicao_id' => $this->institution,
            'turma_id' => $schoolClass->getKey(),
            'servidor_id' => $this->employee->getKey(),
            'funcao_exercida' => 1,
            'tipo_vinculo' => 2,
            'turno_id' => null,
        ]);

        foreach ($disciplines as $discipline) {
            LegacySchoolClassTeacherDisciplineFactory::new()->create([
                'professor_turma_id' => $link->getKey(),
                'componente_curricular_id' => $discipline->getKey(),
            ]);
        }

        return $link;
    }

    /**
     * Uma falta ou um atraso do servidor na escola: por padrão um atraso de
     * 1 hora e 30 minutos em 10/03/2030, sem função.
     *
     * @param array<string, mixed> $attributes Colunas de `pmieducar.falta_atraso`.
     */
    public function absence(LegacySchool $school, array $attributes = []): LegacyAbsenceDelay
    {
        return LegacyAbsenceDelayFactory::new()->create($attributes + [
            'ref_cod_servidor' => $this->employee->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_servidor_funcao' => null,
            'tipo' => AbsenceDelayType::DELAY->value,
            'data_falta_atraso' => '2030-03-10',
            'qtd_horas' => 1,
            'qtd_min' => 30,
            'justificada' => 0,
        ]);
    }

    /**
     * Um período em que o servidor compensou horas na escola.
     */
    public function compensation(LegacySchool $school, string $start, string $end): LegacyAbsenceDelayCompensate
    {
        return LegacyAbsenceDelayCompensateFactory::new()->create([
            'ref_cod_servidor' => $this->employee->getKey(),
            'ref_ref_cod_instituicao' => $this->institution,
            'ref_cod_escola' => $school->getKey(),
            'data_inicio' => $start,
            'data_fim' => $end,
        ]);
    }

    public function inep(int $code, ?string $name = null): self
    {
        EmployeeInepFactory::new()->create(['cod_servidor' => $this->employee->getKey(), 'cod_docente_inep' => $code, 'nome_inep' => $name]);

        return $this;
    }
}
