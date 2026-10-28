<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('o envio sem o arquivo gerado não cadastra nada e, com a escola escolhida, carrega o script da exportação', function () {
    $scenario = EnrollmentScenario::for($this->user, 2030);
    $url = '/intranet/educar_exportacao_educacenso.php';

    $this->post($url, LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Novo', 'ref_cod_escola' => $scenario->school->getKey()]))
        ->assertOk()
        ->assertSee('Não foi possível inserir a informação. [CAD01]')
        ->assertSee('Educacenso/Assets/Javascripts/Educacenso.js', false);
});
