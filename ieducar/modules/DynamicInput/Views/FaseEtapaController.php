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
                    m.nm_tipo, m.num_etapas,
                    t.data_inicio, t.data_fim
                FROM
                    pmieducar.modulo m
                JOIN pmieducar.turma_modulo t
                    ON (t.ref_cod_modulo = m.cod_modulo)
                WHERE t.ref_cod_turma = $1
                ORDER BY T.data_inicio ASC
            ";

            $data = $this->fetchPreparedQuery($sql, $this->getRequest()->turma_id);

            $num_etapas = $data[0]['num_etapas'];

            $options = [];

            $unidade = 1;
            for ($i=0; $i < $num_etapas; $i++) {
                $options['__' . $unidade] = $unidade . 'º ' . $data[$i]['nm_tipo']. ' (' . date('d/m/Y', strtotime($data[$i]['data_inicio'])) . ' à ' . date('d/m/Y', strtotime($data[$i]['data_fim'])) . ')';
                $unidade++;
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
