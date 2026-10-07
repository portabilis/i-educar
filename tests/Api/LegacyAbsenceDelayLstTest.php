<?php

namespace Tests\Api;

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyAbsenceDelayLstTest extends TestCase
{
    use DatabaseTransactions;

    public const ROUTE = '/intranet/educar_falta_atraso_lst.php';

    public function test_botao_novo_envia_a_instituicao_da_listagem(): void
    {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        $servidor = EmployeeFactory::new()->create()->getKey();
        $instituicao = LegacyInstitutionFactory::new()->current()->getKey();

        $this->get(self::ROUTE . "?ref_cod_servidor={$servidor}&ref_cod_instituicao={$instituicao}")
            ->assertOk()
            ->assertSee("educar_falta_atraso_cad.php?ref_cod_servidor={$servidor}&ref_cod_instituicao={$instituicao}", false);
    }
}
