<?php

namespace Tests\Educacenso\Model;

use App\Models\Educacenso\Registro50;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Tests\TestCase;

class Registro50GetPropertyTest extends TestCase
{
    public function test_retorna_propriedades_diretas_das_colunas_1_a_8()
    {
        $registro = new Registro50;
        $registro->registro = '50';
        $registro->inepEscola = '12345';
        $registro->codigoPessoa = '67890';
        $registro->inepDocente = '11111';
        $registro->codigoTurma = '22222';
        $registro->inepTurma = '33333';
        $registro->funcaoDocente = '1';
        $registro->tipoVinculo = '2';

        $this->assertSame('50', $registro->getProperty(1));
        $this->assertSame('12345', $registro->getProperty(2));
        $this->assertSame('67890', $registro->getProperty(3));
        $this->assertSame('11111', $registro->getProperty(4));
        $this->assertSame('22222', $registro->getProperty(5));
        $this->assertSame('33333', $registro->getProperty(6));
        $this->assertSame('1', $registro->getProperty(7));
        $this->assertSame('2', $registro->getProperty(8));
    }

    public function test_retorna_componentes_das_colunas_9_a_33()
    {
        $registro = new Registro50;
        $registro->componentes = range(0, 24);

        $this->assertSame(0, $registro->getProperty(9));
        $this->assertSame(12, $registro->getProperty(21));
        $this->assertSame(24, $registro->getProperty(33));
    }

    public function test_retorna_null_para_componente_inexistente()
    {
        $registro = new Registro50;
        $registro->componentes = [];

        $this->assertNull($registro->getProperty(9));
    }

    public function test_retorna_area_itinerario_colunas_34_a_37()
    {
        $registro = new Registro50;
        $registro->areaItinerario = [
            TipoItinerarioFormativo::LINGUANGENS,
            TipoItinerarioFormativo::CIENCIAS_HUMANAS,
        ];

        $this->assertSame(1, $registro->getProperty(34));
        $this->assertSame(0, $registro->getProperty(35));
        $this->assertSame(0, $registro->getProperty(36));
        $this->assertSame(1, $registro->getProperty(37));
    }

    public function test_retorna_null_quando_area_itinerario_vazia()
    {
        $registro = new Registro50;
        $registro->areaItinerario = null;

        $this->assertNull($registro->getProperty(34));
        $this->assertNull($registro->getProperty(35));
        $this->assertNull($registro->getProperty(36));
        $this->assertNull($registro->getProperty(37));
    }

    public function test_retorna_leciona_itinerario_tecnico_coluna_38()
    {
        $registro = new Registro50;
        $registro->lecionaItinerarioTecnicoProfissional = 1;

        $this->assertSame(1, $registro->getProperty(38));
    }

    public function test_retorna_null_para_coluna_nao_mapeada()
    {
        $registro = new Registro50;

        $this->assertNull($registro->getProperty(0));
        $this->assertNull($registro->getProperty(39));
        $this->assertNull($registro->getProperty(999));
    }
}
