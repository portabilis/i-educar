<?php

class Portabilis_View_Helper_DynamicInput_Frequencia extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'frequencia';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        // $turmaId = $this->getTurmaId($options['turmaId'] ?? null);

        $userId = $this->getCurrentUserId();

        if (/*$turmaId and */empty($resources)) {
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

            $db = new clsBanco();
            $db->Consulta($sql);

            while ($db->ProximoRegistro()) {
                if ($db->Campo('nome') != null)
                    $resources[$db->Campo('id')] = dataToBrasil($db->Campo('data')) . ' - ' . $db->Campo('nm_turma') . ' (' . $db->Campo('nome') . ')';
                else
                    $resources[$db->Campo('id')] = dataToBrasil($db->Campo('data')) . ' - ' . $db->Campo('nm_turma');
            }
        }

        return $this->insertOption(null, 'Selecione a frequência', $resources);
    }

    protected function defaultOptions()
    {
        return [
            'id' => null,
            'options' => ['required' => true, 'label' => 'Frequência'],
            'resources' => []
        ];
    }

    public function frequencia($options = [])
    {
        parent::select($options);
    }
}
