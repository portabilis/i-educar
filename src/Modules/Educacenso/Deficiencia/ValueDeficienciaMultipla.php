<?php

namespace iEducar\Modules\Educacenso\Deficiencia;

use iEducar\Modules\Educacenso\ValueInterface;

class ValueDeficienciaMultipla implements ValueInterface
{
    /**
     * @var array
     */
    private $combinacaoDeficiencias;

    /**
     * @var
     */
    private $deficiencias;

    public function __construct(CombinacaoDeficienciaMultipla $combinacao, $deficiencias)
    {
        $this->combinacaoDeficiencias = $combinacao->getCombinacoes();
        $this->deficiencias = $deficiencias;
    }

    /**
     * @return integer|null
     */
    public function getValue()
    {
        if (empty($this->deficiencias)) {
            return null;
        }

        foreach ($this->combinacaoDeficiencias as $combinacao) {
            if (in_array($combinacao[0], $this->deficiencias) && in_array($combinacao[1], $this->deficiencias)) {
                return 1;
            }
        }

        return 0;
    }
}
