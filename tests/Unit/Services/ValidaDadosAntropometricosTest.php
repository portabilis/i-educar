<?php

namespace Tests\Unit\Services;
use App\Services\AntropometriaService;

class ValidaDadosAntropometricosTest extends PHPUnit\Framework\TestCase
{

    public function test_calcula_o_imc_corretamente()
    {
        $imc = AntropometriaService::calculaIMC(70, 175);
        $this->assertEqualsWithDelta(22.86, $imc, 0.01);
    }

    public function test_classifica_imc_magreza_acentuada()
    {
        $classificacao = AntropometriaService::classificaIMC(-3.5);
        $this->assertEquals('Magreza acentuada', $classificacao);
    }

    public function test_classifica_imc_magreza()
    {
        $classificacao = AntropometriaService::classificaIMC(-2.5);
        $this->assertEquals('Magreza', $classificacao);
    }

    public function test_classifica_imc_eutrofia()
    {
        $classificacao = AntropometriaService::classificaIMC(0.5);
        $this->assertEquals('Eutrofia (peso adequado)', $classificacao);
    }

    public function test_classifica_imc_sobrepeso()
    {
        $classificacao = AntropometriaService::classificaIMC(1.5);
        $this->assertEquals('Sobrepeso', $classificacao);
    }

    public function test_classifica_imc_obesidade()
    {
        $classificacao = AntropometriaService::classificaIMC(2.5);
        $this->assertEquals('Obesidade', $classificacao);
    }

    public function test_classifica_imc_obesidade_grave()
    {
        $classificacao = AntropometriaService::classificaIMC(3.5);
        $this->assertEquals('Obesidade grave', $classificacao);
    }

    public function test_classifica_cintura_masculino_risco_aumentado()
    {
        $classificacao = AntropometriaService::classificaCintura('M', 95);
        $this->assertEquals('Risco aumentado', $classificacao);
    }

    public function test_classifica_cintura_masculino_risco_muito_aumentado()
    {
        $classificacao = AntropometriaService::classificaCintura('M', 105);
        $this->assertEquals('Risco muito aumentado', $classificacao);
    }

    public function test_classifica_cintura_feminino_risco_aumentado()
    {
        $classificacao = AntropometriaService::classificaCintura('F', 82);
        $this->assertEquals('Risco aumentado', $classificacao);
    }

    public function test_classifica_cintura_feminino_risco_muito_aumentado()
    {
        $classificacao = AntropometriaService::classificaCintura('F', 90);
        $this->assertEquals('Risco muito aumentado', $classificacao);
    }

    public function test_classifica_cintura_sem_risco()
    {
        $classificacao = AntropometriaService::classificaCintura('F', 70);
        $this->assertEquals('Sem risco identificado', $classificacao);
    }
}
