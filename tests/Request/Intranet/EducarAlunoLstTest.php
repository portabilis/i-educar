<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('lista os alunos filtrando pelo código', function () {
    $student = LegacyStudentFactory::new()->create();
    $other = LegacyStudentFactory::new()->create();

    $this->get('/intranet/educar_aluno_lst.php?cod_aluno=' . $student->getKey())
        ->assertOk()
        ->assertSee($student->individual->person->nome)
        ->assertDontSee($other->individual->person->nome);
});

test('recusa uma data de nascimento inválida no filtro', function () {
    $this->get('/intranet/educar_aluno_lst.php?data_nascimento=31/02/1800')
        ->assertOk()
        ->assertSee('Aluno - Listagem');
});

test('lista as séries do vínculo com turma do professor ao filtrar por escola e curso', function () {
    $institution = LegacyInstitutionFactory::new()->current();

    DB::table('pmieducar.instituicao')
        ->where('cod_instituicao', $institution->getKey())
        ->update(['exigir_vinculo_turma_professor' => 1]);

    $schoolClass = LegacySchoolClassFactory::new()->create();
    $employee = EmployeeFactory::new()->create();

    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $employee->getKey()]);
    LegacyEmployeeRoleFactory::new()->create(['ref_cod_servidor' => $employee->getKey()]);
    LegacySchoolClassTeacherFactory::new()->create([
        'instituicao_id' => $institution->getKey(),
        'turma_id' => $schoolClass->getKey(),
        'servidor_id' => $employee->getKey(),
        'ano' => $schoolClass->ano,
    ]);

    $this->actingAs(LegacyUserFactory::new()->admin()->create(['cod_usuario' => $employee->getKey()]));

    $this->get('/intranet/educar_aluno_lst.php?' . http_build_query([
        'ref_cod_instituicao' => $institution->getKey(),
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
        'ano' => $schoolClass->ano,
    ]))
        ->assertOk()
        ->assertSee($schoolClass->grade->name);
});
