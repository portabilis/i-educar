<?php

use App\Models\LegacySchoolGrade;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

/**
 * A tela de confirmação da enturmação: chega por POST da lista de turmas,
 * com a matrícula e a turma escolhida, e oferece enturmar, transferir ou
 * remover conforme as enturmações que a matrícula já tem.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves')]);

    $this->alfa = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'max_aluno' => 2]);
    $this->beta = $this->scenario->schoolClass(['nm_turma' => 'Turma Beta', 'max_aluno' => 2]);

    $this->open = fn ($schoolClass, array $payload = []) => $this->post('/intranet/educar_matricula_turma_det.php', $payload + [
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_turma' => $schoolClass->getKey(),
        'ano_letivo' => 2030,
    ]);

    $key = $this->registration->getKey();

    $this->enrollButton = fn ($schoolClass) => "enturmar({$key}, {$schoolClass->getKey()}, \"nova\")";
    $this->transferButton = fn ($schoolClass) => "enturmar({$key}, {$schoolClass->getKey()}, \"transferir\")";
    $this->removeButton = fn ($schoolClass) => "removerEnturmacao({$key}, {$schoolClass->getKey()}";
});

test('para a matrícula sem enturmação mostra a turma, as vagas e oferece só enturmar', function () {
    $this->scenario->enrolled('Bruno Barros', $this->alfa);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSeeInOrder([
            'Nome do Aluno', 'Ana Alves',
            'Escola', 'Escola Aurora',
            'Curso', 'Ensino Fundamental',
            'Série', 'Primeiro Ano',
            'Turma selecionada', 'Turma Alfa',
            'Total de vagas', '2',
            'Alunos enturmados', '1',
            'Vagas disponíveis', '1',
            'Data da enturmação',
        ])
        ->assertDontSee('Enturmação atual')
        ->assertSee('Confirma a enturmação?', false)
        ->assertSee(($this->enrollButton)($this->alfa), false)
        ->assertDontSee(($this->transferButton)($this->alfa), false)
        ->assertDontSee(($this->removeButton)($this->alfa), false)
        ->assertSee("educar_matricula_turma_lst.php?ref_cod_matricula={$this->registration->getKey()}&ano_letivo=2030", false);
});

test('para a matrícula enturmada em outra turma oferece transferir ou enturmar também', function () {
    $this->scenario->enroll($this->registration, $this->beta);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSeeInOrder(['Enturmação atual', 'Turma Beta', 'Data da enturmação'])
        ->assertSee("<input id='ref_cod_turma_origem' type='hidden' value = '{$this->beta->getKey()}'/>", false)
        ->assertSee(($this->transferButton)($this->alfa), false)
        ->assertSee(($this->enrollButton)($this->alfa), false);
});

test('quando a instituição restringe múltiplas enturmações, só oferece transferir', function () {
    $this->user->institution->update(['restringir_multiplas_enturmacoes' => true]);
    $this->scenario->enroll($this->registration, $this->beta);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee(($this->transferButton)($this->alfa), false)
        ->assertDontSee(($this->enrollButton)($this->alfa), false);
});

test('para a matrícula enturmada na própria turma oferece só remover a enturmação', function () {
    $this->scenario->enroll($this->registration, $this->alfa);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee(($this->removeButton)($this->alfa), false)
        ->assertDontSee(($this->transferButton)($this->alfa), false)
        ->assertDontSee(($this->enrollButton)($this->alfa), false)
        ->assertDontSee('name="data_enturmacao" id="data_enturmacao"', false);
});

test('com mais de uma enturmação, pede a escolha da que será transferida', function () {
    $gama = $this->scenario->schoolClass(['nm_turma' => 'Turma Gama']);

    $this->scenario->enroll($this->registration, $this->beta);
    $this->scenario->enroll($this->registration, $gama, ['sequencial' => 2]);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee("<select id='ref_cod_turma_origem' class='obrigatorio'>", false)
        ->assertSee("<option value='{$this->beta->getKey()}'>Turma Beta</option>", false)
        ->assertSee("<option value='{$gama->getKey()}'>Turma Gama</option>", false)
        ->assertSee(($this->transferButton)($this->alfa), false);
});

test('com mais de uma enturmação, sendo uma na turma escolhida, oferece remover e transferir', function () {
    $this->scenario->enroll($this->registration, $this->alfa);
    $this->scenario->enroll($this->registration, $this->beta, ['sequencial' => 2]);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee("<option value='{$this->beta->getKey()}'>Turma Beta</option>", false)
        ->assertDontSee("<option value='{$this->alfa->getKey()}'>", false)
        ->assertSee(($this->removeButton)($this->alfa), false)
        ->assertSee(($this->transferButton)($this->alfa), false)
        ->assertDontSee(($this->enrollButton)($this->alfa), false);
});

test('na turma lotada pede confirmação, ou impede quando a série bloqueia enturmação sem vagas', function () {
    $this->scenario->enrolled('Bruno Barros', $this->alfa);
    $this->scenario->enrolled('Carla Campos', $this->alfa);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee('Atenção! Turma sem vagas! Deseja continuar com a enturmação mesmo assim?', false)
        ->assertDontSee('Enturmação não pode ser realizada', false);

    LegacySchoolGrade::query()->where('ref_cod_serie', $this->scenario->grade->getKey())->update(['bloquear_enturmacao_sem_vagas' => 1]);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee('Enturmação não pode ser realizada', false)
        ->assertDontSee('Deseja continuar com a enturmação mesmo assim?', false);
});

test('na turma lotada a matrícula de dependência já enturmada só confirma', function () {
    $this->registration->update(['dependencia' => true]);
    $this->scenario->enroll($this->registration, $this->beta);

    $this->scenario->enrolled('Bruno Barros', $this->alfa);
    $this->scenario->enrolled('Carla Campos', $this->alfa);

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee('Confirma a enturmação?', false)
        ->assertDontSee('Turma sem vagas', false);
});

test('a quem só pode visualizar matrículas mostra os dados sem as ações', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->scenario->enroll($this->registration, $this->beta);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->open)($this->alfa)
        ->assertOk()
        ->assertSee('Turma Alfa')
        ->assertDontSee(($this->enrollButton)($this->alfa), false)
        ->assertDontSee(($this->transferButton)($this->alfa), false);
});

test('volta para a listagem de matrículas sem matrícula ou por GET', function () {
    $this->post('/intranet/educar_matricula_turma_det.php')->assertRedirect('educar_matricula_lst.php');
    $this->get('/intranet/educar_matricula_turma_det.php')->assertRedirect('educar_matricula_lst.php');
});

// Defeito conhecido: a turma inexistente faz a busca devolver falso, e a tela
// o repassa a array_shift() antes de conferir; estoura em vez de voltar para
// a listagem.
test('defeito: estoura quando a turma escolhida não existe', function () {
    $this->post('/intranet/educar_matricula_turma_det.php', ['ref_cod_matricula' => $this->registration->getKey(), 'ref_cod_turma' => 999999999])
        ->assertServerError();
});
