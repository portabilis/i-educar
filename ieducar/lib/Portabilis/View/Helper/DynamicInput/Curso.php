<?php

class Portabilis_View_Helper_DynamicInput_Curso extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_curso';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $userId = $this->getCurrentUserId();
        $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

        if ($instituicaoId && $escolaId && empty($resources) && $isOnlyProfessor) {
            $cursos = Portabilis_Business_Professor::cursosAlocado($instituicaoId, $escolaId, $userId);
            $resources = Portabilis_Array_Utils::setAsIdValue($cursos, 'id', 'nome');
        } elseif ($escolaId && empty($resources)) {
            $resources = App_Model_IedFinder::getCursos($escolaId);
        }

        return $this->insertOption(null, 'Selecione um curso', $resources);
    }

    public function curso($options = [])
    {
        parent::select($options);
    }
}
