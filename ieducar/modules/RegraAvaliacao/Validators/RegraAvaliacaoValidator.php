<?php

class RegraAvaliacao_Validators_RegraAvaliacaoValidator
{
    protected $messages = [];

    public function isValid(RegraAvaliacao_Model_Regra $regra)
    {
        $this->messages = [];

        // Validação 1: nota máxima >= nota mínima
        if (isset($regra->notaMinimaGeral) && isset($regra->notaMaximaGeral)) {
            if ($regra->notaMaximaGeral <= $regra->notaMinimaGeral) {
                $this->messages[] = 'A nota máxima deve ser maior que a nota mínima.';
            }
        }

        // Validação 2: porcentagem de presença entre 0 e 100
        if (isset($regra->porcentagemPresenca)) {
            $p = (float) $regra->porcentagemPresenca;
            if ($p < 0 || $p > 100) {
                $this->messages[] = 'A porcentagem de presença deve estar entre 0 e 100.';
            }
        }

        // Validação 3: falta máxima > falta mínima
        if (isset($regra->faltaMinimaGeral) && isset($regra->faltaMaximaGeral)) {
            if ($regra->faltaMaximaGeral <= $regra->faltaMinimaGeral) {
                $this->messages[] = 'A falta máxima deve ser maior que a falta mínima.';
            }
        }

        // Validação 4: Quantidade máxima de casas decimais entre 0 e 4
        if (isset($regra->qtdCasasDecimais) && $regra->qtdCasasDecimais !== '') {
            $q = (int) $regra->qtdCasasDecimais;
            if ($q < 0 || $q > 4) {
                $this->messages[] = 'A quantidade máxima de casas decimais deve estar entre 0 e 4.';
            }
        }

        return empty($this->messages);
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
