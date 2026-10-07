<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('mostra o detalhe do motivo de abandono', function () {
    $type = LegacyAbandonmentTypeFactory::new()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nome' => 'Motivo Alfa',
    ]);

    $this->get('/intranet/educar_abandono_tipo_det.php?cod_abandono_tipo=' . $type->getKey())
        ->assertOk()
        ->assertSee('Motivo Alfa');
});

test('volta para a listagem quando o motivo não existe', function () {
    $this->get('/intranet/educar_abandono_tipo_det.php?cod_abandono_tipo=0')
        ->assertRedirect('educar_abandono_tipo_lst.php');
});
