<?php

class FaseEtapaController extends ApiCoreController
{
    protected function canGetFrequencias()
    {
        return true; /*$this->validatesId('turma');*/
    }

    protected function getFrequencias()
    {
        if ($this->canGetFrequencias()) {
    
            $sql = "
                SELECT
                    f.id, data, cc.nome, t.nm_turma
                FROM
                    modules.frequencia f
                LEFT JOIN modules.componente_curricular as cc
                    ON	(f.ref_componente_curricular = cc.id)
                JOIN pmieducar.turma as t
                    ON (f.ref_cod_turma = t.cod_turma)
            ";

            $data = $this->fetchPreparedQuery($sql)[0];

            $options = [];

            for ($i=1; $i < $data['data'] + 1; $i++) {
                if ($data['nome'] != null)
                    $options[$data['id']] = $data['data'] . ' - ' . $data['nm_turma'] . ' (' . $data['nome'] . ')';
                else
                    $options[$data['id']] = $data['data'] . ' - ' . $data['nm_turma'];
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'frequencias')) {
            $this->appendResponse($this->getFrequencias());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
