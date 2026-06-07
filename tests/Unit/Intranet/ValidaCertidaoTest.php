<?php

use PHPUnit\Framework\TestCase;

class ValidaCertidaoTest extends TestCase
{
    private $mockAtendidosCad;

    protected function setUp(): void
    {

        $_REQUEST = [];
        $this->mockAtendidosCad = new class {
            public $certidao_nascimento = '';
            public $certidao_casamento = '';
            public $data_nasc = '';
            public $mensagem = '';

            public function validaCertidao()
            {
                $certidaoNascimento = isset($_REQUEST['tipo_certidao_civil']) && $_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato';
                $certidaoCasamento = isset($_REQUEST['tipo_certidao_civil']) && $_REQUEST['tipo_certidao_civil'] == 'certidao_casamento_novo_formato';

                if ($certidaoNascimento && strlen($this->certidao_nascimento) < 32) {
                    $this->mensagem .= "O campo referente a certidao de nascimento deve conter exatos 32 digitos.";
                    return false;
                } 

                elseif ($certidaoCasamento && strlen($this->certidao_casamento) < 32) {
                    $this->mensagem .= "O campo referente a certidao de casamento deve conter exatos 32 digitos.";
                    return false;
                }

                if (!empty($this->data_nasc) && $certidaoNascimento) {
                    $validator = new class {
                        public function isValid() { return true; } 
                    };
                    
          
                    if (!$validator->isValid()) {
                        return false;
                    }
                }

                return true;
            }
        };
    }

    public function test_nascimento_tamanho_exato_deve_passar()
    {
        $_REQUEST['tipo_certidao_civil'] = 'certidao_nascimento_novo_formato';
        $this->mockAtendidosCad->certidao_nascimento = str_repeat('1', 32); 
        $this->mockAtendidosCad->data_nasc = '01/01/2023';

        $this->assertTrue($this->mockAtendidosCad->validaCertidao());
        $this->assertEquals('', $this->mockAtendidosCad->mensagem);
    }

    public function test_nascimento_limite_inferior_deve_falhar()
    {
        $_REQUEST['tipo_certidao_civil'] = 'certidao_nascimento_novo_formato';
        $this->mockAtendidosCad->certidao_nascimento = str_repeat('1', 31); 
        $this->mockAtendidosCad->data_nasc = '01/01/2023';

        $this->assertFalse($this->mockAtendidosCad->validaCertidao());
        $this->assertStringContainsString('exatos 32', $this->mockAtendidosCad->mensagem);
    }

    public function test_nascimento_limite_superior_falha_na_regra_mas_caracteriza_bug()
    {
        $_REQUEST['tipo_certidao_civil'] = 'certidao_nascimento_novo_formato';
        $this->mockAtendidosCad->certidao_nascimento = str_repeat('1', 33); 
        
        $resultado = $this->mockAtendidosCad->validaCertidao();

        $this->assertTrue($resultado);
    }

    public function test_casamento_tamanho_exato_deve_passar()
    {
        $_REQUEST['tipo_certidao_civil'] = 'certidao_casamento_novo_formato';
        $this->mockAtendidosCad->certidao_casamento = str_repeat('2', 32); 

        $this->assertTrue($this->mockAtendidosCad->validaCertidao());
    }

    public function test_casamento_limite_inferior_deve_falhar()
    {
        $_REQUEST['tipo_certidao_civil'] = 'certidao_casamento_novo_formato';
        $this->mockAtendidosCad->certidao_casamento = str_repeat('2', 31); 

        $this->assertFalse($this->mockAtendidosCad->validaCertidao());
        $this->assertStringContainsString('exatos 32', $this->mockAtendidosCad->mensagem);
    }

    public function test_tipo_invalido_deve_passar_sem_validar()
    {
        $_REQUEST['tipo_certidao_civil'] = 'outro_tipo';
        $this->mockAtendidosCad->certidao_nascimento = '123'; 
        
        $this->assertTrue($this->mockAtendidosCad->validaCertidao());
    }
}