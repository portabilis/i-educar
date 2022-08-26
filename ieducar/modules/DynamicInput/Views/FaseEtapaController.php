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
            $turma_id = $this->getRequest()->turma_id;
            $data_inicial = $this->getRequest()->data_inicial;
            $selected = '';

            $sql = "
                SELECT
                    m.nm_tipo, m.num_etapas,
                    t.data_inicio, t.data_fim,
                    t.sequencial
                FROM
                    pmieducar.modulo m
                JOIN pmieducar.turma_modulo t
                    ON (t.ref_cod_modulo = m.cod_modulo)
                WHERE t.ref_cod_turma = $1
                ORDER BY t.sequencial ASC
            ";

            $data = $this->fetchPreparedQuery($sql, $turma_id);

            $num_etapas = $data[0]['num_etapas'];

            $options = [];

            $unidade = 1;
            for ($i=0; $i < $num_etapas; $i++) {
                $options['__' . $unidade] = $unidade . 'º ' . $data[$i]['nm_tipo']. ' (' . date('d/m/Y', strtotime($data[$i]['data_inicio'])) . ' à ' . date('d/m/Y', strtotime($data[$i]['data_fim'])) . ')';
                $unidade++;
            }

            if (!empty($data_inicial)) {
                $sql = "
                SELECT
                    t.sequencial
                FROM
                    pmieducar.modulo m
                JOIN pmieducar.turma_modulo t
                    ON (t.ref_cod_modulo = m.cod_modulo)
                WHERE t.ref_cod_turma = $1 AND
                      $2 BETWEEN t.data_inicio AND t.data_fim
                ORDER BY t.sequencial ASC
                LIMIT 1
                ";

                $result = $this->fetchPreparedQuery($sql, [$turma_id, dataToBanco($data_inicial)])[0];

                if ($result) {
                    $selected = $result['sequencial'];
                }
            }

            return ['options' => $options,
                    'selected' => $selected];
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
