<?php

use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->school = LegacySchoolFactory::new()->create();
});

test('abre o formulário e define o ano letivo da escola', function () {
    $url = '/intranet/educar_escola_ano_letivo_cad.php?cod_escola=' . $this->school->getKey() . '&ano=' . now()->year;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    $this->post($url, $payload)->assertRedirect();
});
