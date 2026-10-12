<?php

use App\Models\LegacyGradeSequence;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->sequence = LegacyGradeSequenceFactory::new()->create(['ref_usuario_cad' => $this->user->getKey()]);
});

test('abre o formulário de uma nova sequência', function () {
    $this->get('/intranet/educar_sequencia_serie_cad.php')->assertOk();
});

test('edita e exclui a sequência de enturmação', function () {
    $url = '/intranet/educar_sequencia_serie_cad.php?id=' . $this->sequence->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});

test('cadastra uma sequência nova, recusa a repetida e reativa a que estava inativa', function () {
    $origin = LegacyGradeFactory::new()->create();
    $destiny = LegacyGradeFactory::new()->create();
    $form = ['tipoacao' => 'Novo', 'ref_serie_origem' => $origin->getKey(), 'ref_serie_destino' => $destiny->getKey()];
    $sequences = fn () => LegacyGradeSequence::query()->where('ref_serie_origem', $origin->getKey())->where('ref_serie_destino', $destiny->getKey());

    $this->post('/intranet/educar_sequencia_serie_cad.php', $form)->assertRedirect('educar_sequencia_serie_lst.php');

    expect($sequences()->count())->toBe(1);

    $this->post('/intranet/educar_sequencia_serie_cad.php', $form)
        ->assertOk()
        ->assertSee('Já existe essa sequência.');

    $sequences()->update(['ativo' => 0]);
    $this->post('/intranet/educar_sequencia_serie_cad.php', $form)->assertRedirect('educar_sequencia_serie_lst.php');

    expect($sequences()->count())->toBe(1)
        ->and($sequences()->value('ativo'))->toBe(1);
});

test('não edita sequência que não existe nem para uma que já existe, e não exclui a inexistente', function () {
    $other = LegacyGradeSequenceFactory::new()->create();

    $this->post('/intranet/educar_sequencia_serie_cad.php?id=0', ['tipoacao' => 'Editar', 'ref_serie_origem' => 1, 'ref_serie_destino' => 2])
        ->assertOk()
        ->assertSee('Edição não realizada.');

    $this->post('/intranet/educar_sequencia_serie_cad.php?id=' . $this->sequence->getKey(), ['tipoacao' => 'Editar', 'ref_serie_origem' => $other->ref_serie_origem, 'ref_serie_destino' => $other->ref_serie_destino])
        ->assertOk()
        ->assertSee('Já existe essa sequência.');

    $this->post('/intranet/educar_sequencia_serie_cad.php?id=0', ['tipoacao' => 'Excluir'])
        ->assertOk()
        ->assertSee('Exclusão não realizada.');
});

test('usuário institucional usa a própria instituição no formulário', function () {
    $user = LegacyUserFactory::new()->institutional()->withAccess(587)->create();
    // Usuários que não são administradores só entram com o sistema ativo na instituição.
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $this->actingAs($user);

    $fields = LegacyFields::from($this->get('/intranet/educar_sequencia_serie_cad.php')->assertOk()->getContent());

    expect($fields['ref_cod_instituicao'])->toBe((string) $user->ref_cod_instituicao);
});
