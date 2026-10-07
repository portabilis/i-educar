<?php

use App\Models\LegacyAbandonmentType;
use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('cadastra um motivo de abandono', function () {
    $form = $this->get('/intranet/educar_abandono_tipo_cad.php')->assertOk()->getContent();

    $payload = LegacyFields::from($form, [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nome' => 'Mudança de cidade',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_abandono_tipo_cad.php', $payload)
        ->assertRedirect('educar_abandono_tipo_lst.php');

    expect(LegacyAbandonmentType::query()->where('nome', 'Mudança de cidade')->value('ativo'))->toBe(1);
});

test('edita um motivo de abandono', function () {
    $type = LegacyAbandonmentTypeFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $url = '/intranet/educar_abandono_tipo_cad.php?cod_abandono_tipo=' . $type->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nome' => 'Motivo renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect('educar_abandono_tipo_lst.php');

    expect($type->refresh()->nome)->toBe('Motivo renomeado');
});

test('exclui um motivo de abandono', function () {
    $type = LegacyAbandonmentTypeFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $url = '/intranet/educar_abandono_tipo_cad.php?cod_abandono_tipo=' . $type->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect('educar_abandono_tipo_lst.php');

    expect($type->refresh()->ativo)->toBe(0);
});
