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
                    m.nm_tipo, m.num_etapas
                FROM
                    pmieducar.modulo m 
                JOIN pmieducar.turma_modulo t
                    ON (t.ref_cod_modulo = m.cod_modulo)
                WHERE t.ref_cod_turma = {$turmaId}
            ";

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            $nm_tipo = $db->Campo('nm_tipo');
            $num_etapas = $db->Campo('num_etapas');
            
            for ($i=1; $i < $num_etapas + 1; $i++) { 
                $resources[$i] = $i . 'º ' . $nm_tipo;
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
