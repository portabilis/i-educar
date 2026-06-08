<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../ieducar/intranet/ValidadorCargaHoraria.php';

class ValidadorCargaHorariaTest extends TestCase
{
    public function test_deve_aceitar_carga_horaria_valida()
    {
        $validador = new ValidadorCargaHoraria();
        $this->assertTrue($validador->validar("20:00"));
        $this->assertTrue($validador->validar("40:00"));
    }

    public function test_deve_rejeitar_carga_horaria_zerada()
    {
        $validador = new ValidadorCargaHoraria();
        $this->assertFalse($validador->validar("00:00"));
    }

    public function test_deve_rejeitar_formatos_invalidos_e_minutos_impossiveis()
    {
        $validador = new ValidadorCargaHoraria();
        $this->assertFalse($validador->validar(""));
        $this->assertFalse($validador->validar("ABC"));
        $this->assertFalse($validador->validar("08:85"));
        $this->assertTrue($validador->validar("120:30")); 
    }
}
