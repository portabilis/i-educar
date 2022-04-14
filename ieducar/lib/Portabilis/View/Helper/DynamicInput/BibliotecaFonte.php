<?php

class Portabilis_View_Helper_DynamicInput_BibliotecaFonte extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_fonte';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $bibliotecaId = $this->getBibliotecaId();

        if ($bibliotecaId and empty($resources)) {
            $resources = App_Model_IedFinder::getBibliotecaFontes($bibliotecaId);
        }

        return $this->insertOption(null, 'Selecione uma fonte', $resources);
    }

    public function bibliotecaFonte($options = [])
    {
        parent::select($options);
    }
}
