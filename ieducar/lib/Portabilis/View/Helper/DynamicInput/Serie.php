<?php

class Portabilis_View_Helper_DynamicInput_Serie extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_serie';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $cursoId = $this->getCursoId($options['cursoId'] ?? null);
        $userId = $this->getCurrentUserId();
        $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);
        $ano = $options['options']['ano'] ?? null;

        if ($isOnlyProfessor && Portabilis_Business_Professor::canLoadSeriesAlocado($instituicaoId)) {
            $resources = Portabilis_Business_Professor::seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId);
        } elseif ($escolaId && $cursoId && empty($resources)) {
            $resources = App_Model_IedFinder::getSeries($instituicaoId, $escolaId, $cursoId, $ano);
        }

        return $this->insertOption(null, 'Selecione uma série', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Série']];
    }

    public function serie($options = [])
    {
        parent::select($options);
    }
}
