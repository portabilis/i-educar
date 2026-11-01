<?php

use App\Models\LegacyBondType;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('mostra o nome, a abreviatura e os links de novo e de edição', function () {
    $bond = LegacyBondType::query()->create(['nm_vinculo' => 'Registro Alfa', 'abreviatura' => 'RA']);

    $this->get('/intranet/funcionario_vinculo_det.php?cod_func=' . $bond->getKey())
        ->assertOk()
        ->assertSeeInOrder(['Nome', 'Registro Alfa', 'Abreviatura', 'RA'])
        ->assertSee('funcionario_vinculo_cad.php?cod_funcionario_vinculo=' . $bond->getKey(), false)
        ->assertSee('funcionario_vinculo_lst.php', false);
});

test('responde 404 quando o vínculo não existe', function () {
    $this->get('/intranet/funcionario_vinculo_det.php?cod_func=0')->assertNotFound();
});
