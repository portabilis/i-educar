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

        return empty($this->messages);
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
