<?php

namespace Tests\Educacenso;

use App\Models\Educacenso\Registro30;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use Tests\TestCase;

class Registro30Test extends TestCase
{
    public function test_duas_deficiencias_da_lista_formam_deficiencia_multipla(): void
    {
        $pessoa = new Registro30;
        $pessoa->arrayDeficiencias = '{' . Deficiencias::CEGUEIRA . ',' . Deficiencias::SURDEZ . '}';
        $pessoa->deficienciaCegueira = 1;
        $pessoa->deficienciaSurdez = 1;

        $this->assertSame(1, $pessoa->deficienciaMultipla());

        $pessoa->deficienciaSurdez = 0;

        $this->assertSame(0, $pessoa->deficienciaMultipla());
    }
}
