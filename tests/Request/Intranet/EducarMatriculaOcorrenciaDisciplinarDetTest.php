<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves')]);
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']));

    $this->occurrence = LegacyRegistrationDisciplinaryOccurrenceTypeFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'ref_cod_tipo_ocorrencia_disciplinar' => LegacyDisciplinaryOccurrenceTypeFactory::new()->create([
            'institution_id' => $this->scenario->institution,
            'name' => 'Atraso',
        ]),
        'observacao' => "Chegou depois do sinal.\nAvisado pela segunda vez.",
        'data_cadastro' => '2030-03-10 07:45:00',
    ]);

    $this->query = "ref_cod_matricula={$this->registration->getKey()}&ref_cod_tipo_ocorrencia_disciplinar={$this->occurrence->ref_cod_tipo_ocorrencia_disciplinar}&sequencial={$this->occurrence->refresh()->sequencial}";
    $this->url = '/intranet/educar_matricula_ocorrencia_disciplinar_det.php?' . $this->query;
    $this->list = 'educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula=' . $this->registration->getKey();
});

test('mostra a matrícula, o aluno, a série, a data, a hora, o tipo e a observação', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder([
            'Matrícula', (string) $this->registration->getKey(),
            'Nome do Aluno', 'Ana Alves',
            'Série', 'Primeiro Ano',
            'Hora Ocorrência', '07:45',
            'Data Ocorrência', '10/03/2030',
            'Tipo Ocorrência', 'Atraso',
            'Observação', 'Chegou depois do sinal.',
        ])
        ->assertSee("Chegou depois do sinal.<br />\nAvisado pela segunda vez.", false)
        ->assertSee('educar_matricula_ocorrencia_disciplinar_cad.php?' . $this->query, false);
});

// Defeito conhecido: a tela procura a turma em uma chave (`det_turma`) que a
// listagem de enturmações não devolve; a turma do aluno nunca aparece.
test('defeito: não mostra a turma do aluno', function () {
    $this->get($this->url)->assertOk()->assertDontSee('Turma Alfa');
});

test('não oferece editar a quem só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertOk()->assertSee('Atraso')->assertDontSee('educar_matricula_ocorrencia_disciplinar_cad.php', false);
});

test('volta para a listagem da matrícula quando a ocorrência não existe ou foi excluída', function () {
    $this->get("/intranet/educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_tipo_ocorrencia_disciplinar=0&sequencial=1")
        ->assertRedirect($this->list);

    $this->occurrence->update(['ativo' => 0]);

    $this->get($this->url)->assertRedirect($this->list);
});
