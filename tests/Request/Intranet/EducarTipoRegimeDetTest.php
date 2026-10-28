<?php

use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRegimeTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de regime', function () {
    $this->get('/intranet/educar_tipo_regime_det.php?cod_tipo_regime=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem avisando quando tipo de regime não existe', function () {
    $this->get('/intranet/educar_tipo_regime_det.php?cod_tipo_regime=0')
        ->assertRedirect('educar_tipo_regime_lst.php')
        ->assertSessionHas('error', 'Tipo de regime não encontrado. O registro não existe ou foi excluído. Use a busca abaixo para localizá-lo.');
});
