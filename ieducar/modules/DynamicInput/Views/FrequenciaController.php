<?php

class FaseEtapaController extends ApiCoreController
{
    protected function canGetFrequencias()
    {
        return true; /*$this->validatesId('turma');*/
    }

    protected function getFrequencias()
    {
        $instituicaoId = /*$this->getInstituicaoId($options['instituicaoId'] ?? null)*/1;
        $userId = $this->getCurrentUserId();
        $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

        if (/*$isOnlyProfessor && */$this->canGetFrequencias()) {
    
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
                ORDER BY data DESC
            ";

            if ($isOnlyProfessor && $servidor_id) {
                $sql .= " WHERE pt.servidor_id = '{$servidor_id}'";
            }

            $sql .= " ORDER BY data DESC";

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
