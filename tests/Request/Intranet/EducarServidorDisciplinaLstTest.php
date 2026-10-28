<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

/**
 * Popup dos componentes que o servidor leciona, por função. A gravação termina
 * em `exit`, que encerraria a suíte; os testes cobrem a abertura.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->url = fn (string $role = '55') => "/intranet/educar_servidor_disciplina_lst.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}&cod_funcao={$role}";
});

test('abre com uma linha por componente guardado na sessão para a função, e os componentes do curso como opções', function () {
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');
    $course = $this->school->course->getKey();

    $html = $this->withSession(["servant:{$this->id}" => [
        55 => [$course => [$arts->getKey(), $botany->getKey()]],
        77 => [$course => [$botany->getKey()]],
    ]])->get(($this->url)())->assertOk()->getContent();

    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['ref_cod_curso[0]'] ?? $fields['ref_cod_curso'][0] ?? null)->toBe((string) $course)
        ->and($form->options('ref_cod_disciplina[0]'))->toBe([
            'todas_disciplinas' => 'Todas as disciplinas',
            (string) $arts->getKey() => 'Artes',
            (string) $botany->getKey() => 'Botânica',
        ])
        ->and($html)->toContain("value=\"{$arts->getKey()}\" selected", "value=\"{$botany->getKey()}\" selected");
});

test('sem nada na sessão abre a tabela vazia', function () {
    $html = $this->get(($this->url)())->assertOk()->getContent();

    expect(LegacyForm::from($html)->options('ref_cod_disciplina[0]'))->toBe(['' => 'Selecione']);
});

test('para um servidor que não existe abre como cadastro', function () {
    $fields = LegacyFields::from($this->get("/intranet/educar_servidor_disciplina_lst.php?ref_cod_servidor=999999999&ref_cod_instituicao={$this->institution}")->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});

test('a ação de excluir não faz nada', function () {
    $this->post(($this->url)(), ['tipoacao' => 'Excluir'])->assertOk()->assertSessionMissing("servant:{$this->id}");
});

test('volta para a listagem de servidores quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)())->assertRedirect('educar_servidor_lst.php');
});
