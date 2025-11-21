<?php

namespace Tests\Unit;

use App\Support\GradeAgeGroupValidator;
use PHPUnit\Framework\TestCase;

class GradeAgeGroupTest extends TestCase
{
    public function test_nao_deve_permitir_idade_inicial_negativa(): void
    {
        // idade_inicial = -1 (inválida)
        $valido = GradeAgeGroupValidator::faixaEtariaEhValida(-1,5,10);

        $this->assertFalse($valido);
    }

     public function test_nao_deve_permitir_idade_final_negativa(): void
    {
        $valido = GradeAgeGroupValidator::faixaEtariaEhValida(6, 7, -5);
        $this->assertFalse($valido);
    }

    public function test_nao_deve_permitir_idade_ideal_negativa(): void
{
    // idade_ideal = -7 (inválida)
    $valido = GradeAgeGroupValidator::faixaEtariaEhValida(6, -7, 10);

    $this->assertFalse($valido);
}
   
}