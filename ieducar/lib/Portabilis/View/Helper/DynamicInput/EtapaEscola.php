<?php

class Portabilis_View_Helper_DynamicInput_EtapaEscola extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'etapa';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $ano = $this->viewInstance->ano;
        $userId = $this->getCurrentUserId();

        if ($escolaId && empty($resources)) {
            $resources = App_Model_IedFinder::getEtapasEscola($ano, $escolaId);
        }

        return $this->insertOption(null, 'Selecione uma etapa', $resources);
    }

    public function etapaEscola($options = [])
    {
        parent::select($options);
    }
}
