<?php

namespace Tests\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use iEducar\Modules\Educacenso\ExportRule\PoderPublicoResponsavelTransporte;
use Tests\TestCase;

class PoderPublicoResponsavelTransporteTest extends TestCase
{
    public function test_limpa_o_poder_publico_quando_nao_usa_transporte_publico(): void
    {
        $registro = new Registro60;
        $registro->transportePublico = 0;
        $registro->poderPublicoResponsavelTransporte = 1;

        $this->assertNull(PoderPublicoResponsavelTransporte::handle($registro)->poderPublicoResponsavelTransporte);
    }

    public function test_mantem_o_poder_publico_quando_usa_transporte_publico(): void
    {
        $registro = new Registro60;
        $registro->transportePublico = 1;
        $registro->poderPublicoResponsavelTransporte = 2;

        $this->assertSame(2, PoderPublicoResponsavelTransporte::handle($registro)->poderPublicoResponsavelTransporte);
    }
}
