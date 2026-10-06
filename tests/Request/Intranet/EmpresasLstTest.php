<?php

use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'Empresa Alfa']);
});

test('lista as pessoas jurídicas e filtra pelo nome fantasia', function () {
    $link = 'empresas_det.php?cod_empresa=' . $this->organization->getKey();

    $this->get('/intranet/empresas_lst.php?fantasia=Empresa Alfa')->assertOk()->assertSee($link, false);
    $this->get('/intranet/empresas_lst.php?fantasia=nome-que-nao-existe&razao_social=x&id_federal=00000000000191')->assertOk()->assertDontSee($link, false);
});
