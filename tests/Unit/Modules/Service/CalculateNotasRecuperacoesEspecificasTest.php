<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

require_once dirname(__DIR__, 4) . '/ieducar/modules/Avaliacao/Service/Boletim.php';

class CalculateNotasRecuperacoesEspecificasTest extends TestCase
{
    private function criarInstanciaParaTeste($regras, $notaObj)
    {
        return new class($regras, $notaObj) extends Avaliacao_Service_Boletim {
            private $regras;
            private $notaObj;

            public function __construct($regras, $notaObj) {
                $this->regras = $regras;
                $this->notaObj = $notaObj;
            }

            public function executarMetodoAlvo($id, $data = []) {
                return $this->_calculateNotasRecuperacoesEspecificas($id, $data);
            }

            public function getRegrasRecuperacao() {
                return $this->regras;
            }

            public function getNotaComponente($id, $etapa = 1) {
                return $this->notaObj;
            }
        };
    }

    public function test_deve_ignorar_o_calculo_se_nao_houver_regras_cadastradas()
    {
        $boletim = $this->criarInstanciaParaTeste([], null);
        $dataInicial = ['Se' => 10.0];
        
        $resultado = $boletim->executarMetodoAlvo(1, $dataInicial);
        
        $this->assertEquals($dataInicial, $resultado);
    }

    public function test_deve_calcular_e_substituir_a_menor_nota_quando_a_recuperacao_aumentar_a_media()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 8.0];
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 5.0, 'E2' => 7.0, 'Se' => 12.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertEquals(8.0, $resultado['RSP1']);
        $this->assertEquals(7.0, $resultado['RSPM1']);
    }

    public function test_deve_ignorar_o_calculo_se_a_nota_da_recuperacao_for_um_texto_invalido()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; } 
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 'falta'];
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 5.0, 'E2' => 5.0, 'Se' => 10.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertArrayNotHasKey('RSP1', $resultado);
    }

    public function test_deve_ignorar_o_calculo_se_o_aluno_nao_tiver_nota_de_recuperacao()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; } 
        };
        $boletim = $this->criarInstanciaParaTeste([$regra], null);
        
        $dataInput = ['E1' => 5.0, 'E2' => 5.0, 'Se' => 10.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertArrayNotHasKey('RSP1', $resultado);
    }

    public function test_deve_proteger_o_aluno_e_manter_a_media_antiga_se_a_recuperacao_for_menor()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 2.0];
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 6.0, 'E2' => 8.0, 'Se' => 14.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertEquals(2.0, $resultado['RSP1']);
        $this->assertEquals(7.0, $resultado['RSPM1']); 
    }

    public function test_deve_somar_a_nota_sem_substituir_quando_a_regra_da_escola_assim_exigir()
    {
        $regra = new class {
            public $substituiMenorNota = false;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 4.0];
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 5.0, 'E2' => 5.0, 'Se' => 10.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertEquals(4.0, $resultado['RSP1']);
        $this->assertEquals(4.5, $resultado['RSPM1']);
        $this->assertEquals(4.0, $resultado['RSPS1']);
    }


    public function test_valor_limite_minimo_nota_zero()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 0.00]; 
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 5.0, 'E2' => 5.0, 'Se' => 10.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertEquals(0.00, $resultado['RSP1']);
    }

    public function test_valor_limite_maximo_nota_dez()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 10.00]; 
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 5.0, 'E2' => 5.0, 'Se' => 10.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertEquals(10.00, $resultado['RSP1']);
    }

    public function test_particao_invalida_negativa()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => -2.50];
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 6.0, 'E2' => 6.0, 'Se' => 12.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
        $this->assertEquals(-2.50, $resultado['RSP1']);
        //$this->assertArrayNotHasKey('RSP1', $resultado);
    }

    public function test_particao_invalida_absurda()
    {
        $regra = new class {
            public $substituiMenorNota = true;
            public function getLastEtapa() { return 2; }
            public function getEtapas() { return [1, 2]; }
        };
        $notaObj = (object)['notaRecuperacaoEspecifica' => 999.00]; 
        $boletim = $this->criarInstanciaParaTeste([$regra], $notaObj);
        
        $dataInput = ['E1' => 6.0, 'E2' => 6.0, 'Se' => 12.0];
        $resultado = $boletim->executarMetodoAlvo(1, $dataInput);
        
       
        $this->assertEquals(999.00, $resultado['RSP1']);
        //$this->assertArrayNotHasKey('RSP1', $resultado);
    }

}