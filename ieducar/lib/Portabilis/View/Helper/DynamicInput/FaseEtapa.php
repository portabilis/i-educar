<?php

class Portabilis_View_Helper_DynamicInput_FaseEtapa extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'fase_etapa';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $turmaId = $this->getTurmaId($options['turmaId'] ?? null);

        $userId = $this->getCurrentUserId();

        if ($turmaId and empty($resources)) {
            $sql = "
                SELECT
                    m.nm_tipo, m.num_etapas,
                    t.data_inicio, t.data_fim
                FROM
                    pmieducar.modulo m
                JOIN pmieducar.turma_modulo t
                    ON (t.ref_cod_modulo = m.cod_modulo)
                WHERE t.ref_cod_turma = {$turmaId}
                ORDER BY T.data_inicio ASC
            ";

            $db = new clsBanco();
            $db->Consulta($sql);

            $data = [];

            while($db->ProximoRegistro()) {
                $data[] = [
                    'nm_tipo' => $db->Campo('nm_tipo'),
                    'num_etapas' => $db->Campo('num_etapas'),
                    'data_inicio' => $db->Campo('data_inicio'),
                    'data_fim' => $db->Campo('data_fim'),
                ];
            }

            $nm_tipo = $data[0]['nm_tipo'];
            $num_etapas = $data[0]['num_etapas'];
            $unidade = 1;

            for ($i=0; $i < $num_etapas; $i++) {
                $resources[$unidade] = $unidade . 'º ' . $nm_tipo. ' (' . date('d/m/Y', strtotime($data[$i]['data_inicio'])) . ' à ' . date('d/m/Y', strtotime($data[$i]['data_fim'])) . ')';
                $unidade++;
            }
        }

        return $this->insertOption(null, 'Selecione a fase da etapa', $resources);
    }

    protected function defaultOptions()
    {
        return [
            'id' => null,
            'options' => ['label' => 'Fase da etapa'],
            'resources' => []
        ];
    }

    public function faseEtapa($options = [])
    {
        parent::select($options);
    }
}
