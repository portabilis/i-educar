<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('botao novo envia a instituicao da listagem', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $servidor = EmployeeFactory::new()->create()->getKey();
    $instituicao = LegacyInstitutionFactory::new()->current()->getKey();

    $this->get('/intranet/educar_falta_atraso_lst.php' . "?ref_cod_servidor={$servidor}&ref_cod_instituicao={$instituicao}")
        ->assertOk()
        ->assertSee("educar_falta_atraso_cad.php?ref_cod_servidor={$servidor}&ref_cod_instituicao={$instituicao}", false);
});
