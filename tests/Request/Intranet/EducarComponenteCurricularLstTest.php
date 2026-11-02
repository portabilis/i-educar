<?php

use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('lista os componentes curriculares e filtra pelo nome', function () {
    $discipline = LegacyDisciplineFactory::new()->create(['nome' => 'Componente Alfa']);
    $link = 'ComponenteCurricular/view?id=' . $discipline->getKey();

    $this->get('/intranet/educar_componente_curricular_lst.php?nome=Componente Alfa&abreviatura=' . urlencode($discipline->abreviatura) . '&tipo_base=' . $discipline->tipo_base . '&area_conhecimento_id=' . $discipline->area_conhecimento_id)
        ->assertOk()
        ->assertSee('Componente Alfa');
});
