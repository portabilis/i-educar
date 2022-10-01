<?php

class ValidaPlanoRegistroAulaController extends ApiCoreController
{

    public function validaPlanejamentoAula ()
    {
        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;
        $result = false;

        if (is_numeric($planejamento_aula_id)) {
            $obj = new clsModulesPlanejamentoAula($planejamento_aula_id);

            $date = date('Y-m-d H:i:s');
            $result = $obj->updateValidacao(true, Auth::id(), $date);
        }

        return ['result' => $result];
    }

    public function removerValidacaoPlanoAula ()
    {
        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;
        $result = false;

        if (is_numeric($planejamento_aula_id)) {
            $obj = new clsModulesPlanejamentoAula($planejamento_aula_id);
            $result = $obj->updateValidacao(false);
        }

        return ['result' => $result];
    }

    public function validaRegistroAula ()
    {
        $frequencia_id = $this->getRequest()->frequencia_id;
        $result = false;

        if (is_numeric($frequencia_id)) {
            $obj = new clsModulesFrequencia($frequencia_id);
            $date = date('Y-m-d H:i:s');
            $result = $obj->updateValidacao(true, Auth::id(), $date);
        }

        return ['result' => $result];
    }

    public function removerValidacaoRegistroAula ()
    {
        $frequencia_id = $this->getRequest()->frequencia_id;
        $result = false;

        if (is_numeric($frequencia_id)) {
            $obj = new clsModulesFrequencia($frequencia_id);
            $result = $obj->updateValidacao(false);
        }

        return ['result' => $result];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'validar-planejamento-aula')) {
            $this->appendResponse($this->validaPlanejamentoAula());
        } if ($this->isRequestFor('post', 'remover-validacao-planejamento-aula')) {
            $this->appendResponse($this->removerValidacaoPlanoAula());
        } else if ($this->isRequestFor('post', 'validar-registro-aula')) {
            $this->appendResponse($this->validaRegistroAula());
        } else if ($this->isRequestFor('post', 'remover-validacao-registro-aula')) {
            $this->appendResponse($this->removerValidacaoRegistroAula());
        }
    }

}
