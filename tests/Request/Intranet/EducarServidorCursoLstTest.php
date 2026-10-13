<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

/**
 * Popup dos cursos que o servidor ministra, por função. A gravação termina em
 * `exit`, que encerraria a suíte; os testes cobrem a abertura.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->url = fn (string $role = '') => "/intranet/educar_servidor_curso_lst.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}&ref_cod_servidor_funcao={$role}";
});

test('abre com os cursos ativos da instituição e os que a sessão guarda para a função', function () {
    $other = LegacyCourseFactory::new()->create(['nm_curso' => 'Educação Infantil', 'ref_cod_instituicao' => $this->institution]);
    LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Extinto', 'ref_cod_instituicao' => $this->institution, 'ativo' => 0]);

    $course = $this->school->course->getKey();

    $html = $this->withSession(["servant:{$this->id}" => [55 => [$course => [1, 2], $other->getKey() => [3]]]])
        ->get(($this->url)('55'))
        ->assertOk()
        ->getContent();

    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['ref_cod_servidor_funcao'])->toBe('55')
        ->and(LegacyForm::from($html)->options('ref_cod_curso[0]'))->toBe(['' => 'Selecione', (string) $other->getKey() => 'Educação Infantil', (string) $course => 'Ensino Fundamental'])
        ->and($html)->toContain("value=\"{$course}\" selected", "value=\"{$other->getKey()}\" selected");
});

test('sem cursos da função na sessão do servidor, usa os escolhidos nesta mesma tela', function () {
    $course = $this->school->course->getKey();

    $html = $this->withSession(['cursos_por_funcao' => [55 => ['cursos_servidor' => [$course => $course]]]])
        ->get(($this->url)('55'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain("value=\"{$course}\" selected");
});

test('sem a função na URL usa a função zero, e para um servidor que não existe abre como cadastro', function () {
    $fields = LegacyFields::from($this->get(($this->url)())->assertOk()->getContent());

    expect($fields['ref_cod_servidor_funcao'])->toBe('0')
        ->and($fields['tipoacao'])->toBe('Editar');

    $missing = LegacyFields::from($this->get("/intranet/educar_servidor_curso_lst.php?ref_cod_servidor=999999999&ref_cod_instituicao={$this->institution}")->assertOk()->getContent());

    expect($missing['tipoacao'])->toBe('Novo');
});

test('a ação de excluir não faz nada', function () {
    $this->post(($this->url)('55'), ['tipoacao' => 'Excluir'])->assertOk()->assertSessionMissing('cursos_por_funcao');
});

test('volta para a listagem de servidores quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)('55'))->assertRedirect('educar_servidor_lst.php');
});
