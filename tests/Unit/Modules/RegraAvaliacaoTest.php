<?php

class RegraAvaliacaoTest extends UnitBaseTest
{
    protected function makeRegra($attrs = [])
    {
        $regra = new RegraAvaliacao_Model_Regra;
        foreach ($attrs as $k => $v) {
            $regra->$k = $v;
        }

        return $regra;
    }

    public function test_nota_maxima_menor_que_minima()
    {
        $regra = $this->makeRegra([
            'notaMinimaGeral' => 6,
            'notaMaximaGeral' => 5,
        ]);

        $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
        $this->assertFalse($validator->isValid($regra));
        $this->assertContains(
            'A nota máxima deve ser maior que a nota mínima.',
            $validator->getMessages()
        );
    }

    public function test_porcentagem_presenca_invalida()
    {
        $regra = $this->makeRegra([
            'porcentagemPresenca' => 150,
        ]);
        $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
        $this->assertFalse($validator->isValid($regra));
        $this->assertContains(
            'A porcentagem de presença deve estar entre 0 e 100.',
            $validator->getMessages()
        );
    }

    public function test_falta_maxima_menor_ou_igual_falta_minima()
    {
        $regra = $this->makeRegra([
            'faltaMinimaGeral' => 5,
            'faltaMaximaGeral' => 5,
        ]);

        $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator;
        $this->assertFalse($validator->isValid($regra));
        $this->assertContains(
            'A falta máxima deve ser maior que a falta mínima.',
            $validator->getMessages()
        );
    }
}
