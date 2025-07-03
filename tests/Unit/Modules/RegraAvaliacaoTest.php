<?php
class RegraAvaliacaoTest extends UnitBaseTest
{
    protected function makeRegra($attrs = [])
    {
        $regra = new RegraAvaliacao_Model_Regra();
        foreach ($attrs as $k => $v) {
            $regra->$k = $v;
        }
        return $regra;
    }

    public function testNotaMaximaMenorQueMinima()
    {
        $regra = $this->makeRegra([
            'notaMinimaGeral' => 6,
            'notaMaximaGeral' => 5
        ]);

        $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
        $this->assertFalse($validator->isValid($regra));
        $this->assertContains(
            'A nota máxima deve ser maior que a nota mínima.',
            $validator->getMessages()
        );
    }
       public function testPorcentagemPresencaInvalida()
   {
       $regra = $this->makeRegra([
           'porcentagemPresenca' => 150
       ]);
       $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
       $this->assertFalse($validator->isValid($regra));
       $this->assertContains(
           'A porcentagem de presença deve estar entre 0 e 100.',
           $validator->getMessages()
       );
   }


}
