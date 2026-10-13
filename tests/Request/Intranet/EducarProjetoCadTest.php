<?php

use App\Models\LegacyProject;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyStudentProjectFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->new = '/intranet/educar_projeto_cad.php';
    $this->project = fn () => LegacyProjectFactory::new()->create(['nome' => 'Registro Original', 'observacao' => 'Observação original']);
    $this->edit = fn ($project) => $this->new . '?cod_projeto=' . $project->getKey();
});

test('cadastra um projeto', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'nome' => 'Registro Alfa',
        'observacao' => 'Contraturno de esporte',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_projeto_lst.php');

    expect(LegacyProject::query()->where('nome', 'Registro Alfa')->sole()->observacao)->toBe('Contraturno de esporte');
});

test('abre a edição com os dados gravados e edita o projeto', function () {
    $project = ($this->project)();

    $fields = LegacyFields::from($this->get(($this->edit)($project))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nome'])->toBe('Registro Original')
        ->and($fields['observacao'])->toBe('Observação original')
        ->and($fields['cod_projeto'])->toBe((string) $project->getKey());

    $this->post(($this->edit)($project), ['nome' => 'Registro Renomeado', 'observacao' => 'Outra observação'] + $fields)
        ->assertRedirect('educar_projeto_lst.php');

    expect($project->refresh())
        ->nome->toBe('Registro Renomeado')
        ->observacao->toBe('Outra observação');
});

test('exclui o projeto', function () {
    $project = ($this->project)();

    $payload = LegacyFields::from($this->get(($this->edit)($project))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($project), $payload)->assertRedirect('educar_projeto_lst.php');

    $this->assertModelMissing($project);
});

test('recusa excluir o projeto com aluno vinculado', function () {
    $project = ($this->project)();
    LegacyStudentProjectFactory::new()->create(['ref_cod_projeto' => $project->getKey()]);

    $payload = LegacyFields::from($this->get(($this->edit)($project))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($project), $payload)
        ->assertOk()
        ->assertSee('Você não pode excluir esse projeto, pois ele possui alunos vinculados.');

    $this->assertModelExists($project);
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(21250, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_projeto_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_projeto=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
