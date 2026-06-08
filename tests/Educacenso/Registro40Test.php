<?php

namespace Tests\Educacenso;

use App\Models\Educacenso\Registro40;
use Tests\TestCase;

class Registro40Test extends TestCase
{
    public function test_get_property_returns_registro_for_column_1()
    {
        $registro = new Registro40();
        $registro->registro = '40';

        $this->assertEquals('40', $registro->getProperty(1));
    }

   public function test_get_property_returns_inep_escola_for_column_2()
    {
        $registro = new Registro40();
        $registro->inepEscola = '12345678';

        $this->assertEquals('12345678', $registro->getProperty(2));
    }

    public function test_get_property_returns_correct_values_for_columns_3_to_7()
    {
        $registro = new Registro40();
        $registro->codigoPessoa = '999';
        $registro->inepGestor = '88888888';
        $registro->cargo = '1';
        $registro->criterioAcesso = '2';
        $registro->tipoVinculo = '3';

        $this->assertEquals('999', $registro->getProperty(3));
        $this->assertEquals('88888888', $registro->getProperty(4));
        $this->assertEquals('1', $registro->getProperty(5));
        $this->assertEquals('2', $registro->getProperty(6));
        $this->assertEquals('3', $registro->getProperty(7));
    }

     public function test_get_property_returns_null_for_invalid_columns()
    {
        $registro = new Registro40();

        $this->assertNull($registro->getProperty(0));
        $this->assertNull($registro->getProperty(8));
        $this->assertNull($registro->getProperty(99));
    }
}
