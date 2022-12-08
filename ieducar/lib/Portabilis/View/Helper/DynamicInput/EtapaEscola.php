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
        $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $ano = $this->viewInstance->ano;
        $this->getCurrentUserId();

        if ($escolaId && empty($resources)) {
            $resources = App_Model_IedFinder::getEtapasEscola($ano, $escolaId);
        }

        return self::insertOption(null, 'Selecione uma etapa', $resources);
    }

    public function etapaEscola($options = [])
    {
        parent::select($options);
    }
}
