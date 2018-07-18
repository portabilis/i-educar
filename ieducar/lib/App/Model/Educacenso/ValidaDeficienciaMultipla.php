<?php


namespace iEducar\App\Model\Educacenso;


class ValidaDeficienciaMultipla
{
    /**
     * @var array
     */
    private $mapeamentoDeficiencias;

    /**
     * @var array
     */
    private $combinacaoDeficiencias;

    public function __construct(MapeamentoDeficiencias $mapeamentoDeficiencias, CombinacaoDeficienciaMultipla $combinacao)
    {
        $this->mapeamentoDeficiencias = MapeamentoDeficiencias::getArrayMapeamentoDeficiencias();
        $this->combinacaoDeficiencias = $combinacao->getCombinacoes();
    }

    /**
     * @param array $deficiencias
     * @return bool
     */
    public function possuiDeficienciaMultipla($deficiencias)
    {
        if (empty($deficiencias)) {
            return false;
        }

        $deficienciasAluno = [];
        foreach ($deficiencias as $deficiencia) {
            $deficienciasAluno[$this->mapeamentoDeficiencias[$deficiencia['id']]] = true;
        }

        foreach ($this->combinacaoDeficiencias as $combinacao) {
            if (isset($deficienciasAluno[$combinacao[0]]) && isset($deficienciasAluno[$combinacao[1]])) {
                return true;
            }
        }

        return false;
    }
}