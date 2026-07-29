<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\IdadePermitidaValidator;
use Tests\TestCase;

class IdadePermitidaValidatorTest extends TestCase
{
    public function test_idade_nula_nao_bloqueia()
    {
        $validator = new IdadePermitidaValidator(null, [7, 50], 'a etapa de ensino 5º Ano');

        $this->assertTrue($validator->isValid());
    }

    public function test_faixa_nula_nao_bloqueia()
    {
        $validator = new IdadePermitidaValidator(6, null, 'a etapa de ensino 5º Ano');

        $this->assertTrue($validator->isValid());
    }

    public function test_idade_dentro_da_faixa_valida()
    {
        $validator = new IdadePermitidaValidator(10, [7, 50], 'a etapa de ensino 5º Ano');

        $this->assertTrue($validator->isValid());
    }

    public function test_idade_igual_ao_minimo_valida()
    {
        $validator = new IdadePermitidaValidator(7, [7, 50], 'a etapa de ensino 5º Ano');

        $this->assertTrue($validator->isValid());
    }

    public function test_idade_igual_ao_maximo_valida()
    {
        $validator = new IdadePermitidaValidator(50, [7, 50], 'a etapa de ensino 5º Ano');

        $this->assertTrue($validator->isValid());
    }

    public function test_idade_abaixo_do_minimo_invalida()
    {
        // caso do aluno Rhyan: 6 anos numa etapa que exige no mínimo 7
        $validator = new IdadePermitidaValidator(6, [7, 50], 'a etapa de ensino 5º Ano');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('6 anos', $validator->getMessage());
        $this->assertStringContainsString('7 a 50', $validator->getMessage());
    }

    public function test_idade_acima_do_maximo_invalida()
    {
        $validator = new IdadePermitidaValidator(51, [7, 50], 'a etapa de ensino 5º Ano');

        $this->assertFalse($validator->isValid());
    }

    public function test_mensagem_vazia_quando_valido()
    {
        $validator = new IdadePermitidaValidator(10, [7, 50], 'a etapa de ensino 5º Ano');
        $validator->isValid();

        $this->assertSame('', $validator->getMessage());
    }

    public function test_gestor_abaixo_do_minimo_invalido()
    {
        // gestor de 17 anos, faixa da função de 18 a 95
        $validator = new IdadePermitidaValidator(17, [18, 95], 'a função de gestor(a) escolar (registro 40)');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('18 a 95', $validator->getMessage());
    }

    public function test_profissional_abaixo_do_minimo_invalido()
    {
        // profissional de 13 anos, faixa da função de 14 a 95
        $validator = new IdadePermitidaValidator(13, [14, 95], 'a função de profissional escolar em sala de aula (registro 50)');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('14 a 95', $validator->getMessage());
    }

    public function test_aluno_em_turma_prisional_abaixo_do_minimo_invalido()
    {
        // aluno de 15 anos numa turma em unidade prisional, faixa de 18 a 94
        $validator = new IdadePermitidaValidator(15, [18, 94], 'turmas em unidade prisional');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('18 a 94', $validator->getMessage());
    }
}
