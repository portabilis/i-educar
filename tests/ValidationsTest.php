<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../ieducar/lib/validations.php';

final class ValidationsTest extends TestCase
{
    public function testValidCpf()
    {
        $this->assertTrue(validar_cpf('529.982.247-25'));
        $this->assertTrue(validar_cpf('52998224725'));
    }

    public function testInvalidCpf()
    {
        $this->assertFalse(validar_cpf('111.111.111-11'));
        $this->assertFalse(validar_cpf('123.456.789-00'));
        $this->assertFalse(validar_cpf('00000000000'));
        $this->assertFalse(validar_cpf(''));
        $this->assertFalse(validar_cpf('123'));
    }
}
