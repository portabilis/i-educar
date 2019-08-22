<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';
require_once 'Portabilis/Business/Professor.php';

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

        if ($isOnlyProfessor && Portabilis_Business_Professor::canLoadSeriesAlocado($instituicaoId)) {
            $resources = Portabilis_Business_Professor::seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId);
        } elseif ($escolaId && $cursoId && empty($resources)) {
            $resources = App_Model_IedFinder::getSeries($instituicaoId = null, $escolaId, $cursoId);
        }

        return $this->insertOption(null, 'Selecione uma s&eacute;rie', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'S&eacute;rie']];
    }

    public function serie($options = [])
    {
        parent::select($options);
    }
}
