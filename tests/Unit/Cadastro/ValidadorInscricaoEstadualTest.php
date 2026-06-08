<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../ieducar/intranet/ValidadorInscricaoEstadual.php';

class ValidadorInscricaoEstadualTest extends TestCase
{
    public function test_deve_aceitar_inscricao_apenas_numeros()
    {
        $validador = new ValidadorInscricaoEstadual();
        $this->assertTrue($validador->validar("123456789"));
    }


    public function test_deve_rejeitar_inscricao_com_letras()
    {
        $validador = new ValidadorInscricaoEstadual();
        $this->assertFalse($validador->validar("ABCDE"));
        $this->assertFalse($validador->validar("1234ABC"));
    }

    public function test_deve_aceitar_a_palavra_isento()
    {
        $validador = new ValidadorInscricaoEstadual();
        $this->assertTrue($validador->validar("ISENTO"));
        $this->assertTrue($validador->validar("isento"));
    }
}
