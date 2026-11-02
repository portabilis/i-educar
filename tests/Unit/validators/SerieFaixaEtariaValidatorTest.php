<?php

namespace Tests\Unit\Validators;

use App\Validators\SerieFaixaEtariaValidator;
use Tests\TestCase;

class SerieFaixaEtariaValidatorTest extends TestCase
{
    private SerieFaixaEtariaValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SerieFaixaEtariaValidator;
    }

    public function test_rejeita_idade_ideal_negativa(): void
    {
        $isValid = $this->validator->isValid(idadeIdeal: -5);

        $this->assertFalse($isValid);
        $this->assertContains(
            'A idade padrão (ideal) não pode ser negativa.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_idade_inicial_negativa(): void
    {
        $isValid = $this->validator->isValid(idadeInicial: -1, idadeFinal: 10);

        $this->assertFalse($isValid);
        $this->assertContains(
            'A idade inicial da faixa etária não pode ser negativa.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_idade_final_negativa(): void
    {
        $isValid = $this->validator->isValid(idadeInicial: 5, idadeFinal: -2);

        $this->assertFalse($isValid);
        $this->assertContains(
            'A idade final da faixa etária não pode ser negativa.',
            $this->validator->getMessages()
        );
    }

    public function test_rejeita_faixa_etaria_com_inicio_maior_que_fim(): void
    {
        $isValid = $this->validator->isValid(idadeInicial: 12, idadeFinal: 10);

        $this->assertFalse($isValid);
        $this->assertContains(
            'A idade inicial não pode ser maior que a idade final da faixa etária.',
            $this->validator->getMessages()
        );
    }

    public function test_aceita_valores_validos_e_consistentes(): void
    {
        $isValid = $this->validator->isValid(idadeInicial: 6, idadeFinal: 7, idadeIdeal: 6);

        $this->assertTrue($isValid);
        $this->assertEmpty($this->validator->getMessages());
    }

    public function test_aceita_idade_zero_para_educacao_infantil(): void
    {
        $isValid = $this->validator->isValid(idadeInicial: 0, idadeFinal: 3, idadeIdeal: 0);

        $this->assertTrue($isValid);
        $this->assertEmpty($this->validator->getMessages());
    }

    public function test_aceita_valores_nulos_para_campos_opcionais(): void
    {
        $isValid = $this->validator->isValid(idadeInicial: null, idadeFinal: null, idadeIdeal: null);

        $this->assertTrue($isValid);
        $this->assertEmpty($this->validator->getMessages());
    }
}
