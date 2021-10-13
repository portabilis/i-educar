<?php

class FaseEtapaController extends ApiCoreController
{
    protected function canGetEtapas()
    {
        return $this->validatesId('turma');
    }

    protected function getEtapas()
    {
        if ($this->canGetEtapas()) {
    
            $sql = "
                SELECT
                    m.nm_tipo, m.num_etapas
                FROM
                    pmieducar.modulo m 
                JOIN pmieducar.turma_modulo t
                    ON (t.ref_cod_modulo = m.cod_modulo)
                WHERE t.ref_cod_turma = $1
            ";

            $data = $this->fetchPreparedQuery($sql, $this->getRequest()->turma_id)[0];

            $options = [];

            for ($i=1; $i < $data['num_etapas'] + 1; $i++) { 
                $options['__' . $i] = $i . 'º ' . $data['nm_tipo'];
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'etapas')) {
            $this->appendResponse($this->getEtapas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
