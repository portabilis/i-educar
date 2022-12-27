<?php

class Portabilis_View_Helper_DynamicInput_ComponenteCurricularTurma extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_componente_curricular_turma';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $componenteId = $this->getComponenteCurricularId($options['componenteCurricularId'] ?? null);

        $userId = $this->getCurrentUserId();

        if ($resources and empty($resources)) {
            $sql = "
                SELECT
                cc.carga_horaria
                FROM
                    modules.componente_curricular_turma cc
                WHERE cc.componente_curricular_id = {$componenteId}
                ORDER BY T.componente_curricular_id ASC
            ";

            $db = new clsBanco();
            $db->Consulta($sql);

            $data = [];

            while($db->ProximoRegistro()) {
                $data[0] = [
                    'carga_horaria' => $db->Campo('carga_horaria'),
                    
                ];
            }

            
                $resources[1] = $data[0]['carga_horaria'];
                $unidade++;
            
        }

        return $this->insertOption(null, $data[0]['carga_horaria'], $resources);
    }

    protected function defaultOptions()
    {
        return [
            'id' => null,
            'options' => ['label' => 'Carga horaria'],
            'resources' => []
        ];
    }

    public function componenteCurricularTurma($options = [])
    {
        parent::select($options);
    }
}
