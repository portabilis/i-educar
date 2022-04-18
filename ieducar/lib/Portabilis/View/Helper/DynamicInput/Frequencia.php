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

        $instituicaoId = /*$this->getInstituicaoId($options['instituicaoId'] ?? null)*/1;
        $userId = $this->getCurrentUserId();
        $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

        if (empty($resources)) {
            $sql = "
                SELECT
                    f.id, data, cc.nome, t.nm_turma
                FROM
                    modules.frequencia f
                LEFT JOIN modules.componente_curricular as cc
                    ON	(f.ref_componente_curricular = cc.id)
                JOIN pmieducar.turma as t
                    ON (f.ref_cod_turma = t.cod_turma)
                JOIN modules.professor_turma as pt
                    ON (pt.turma_id = t.cod_turma)
                JOIN modules.professor_turma_disciplina as ptd
                    ON (pt.id = ptd.professor_turma_id AND ptd.componente_curricular_id = cc.id)
            ";

            if ($isOnlyProfessor && $userId) {
                $sql .= " WHERE pt.servidor_id = '{$userId}'";
            }

            $sql .= " ORDER BY data DESC";

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
