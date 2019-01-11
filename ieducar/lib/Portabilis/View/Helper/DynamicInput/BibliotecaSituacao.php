<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

class Portabilis_View_Helper_DynamicInput_BibliotecaSituacao extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_situacao';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $bibliotecaId = $this->getBibliotecaId();

        if ($bibliotecaId and empty($resources)) {
            $resources = App_Model_IedFinder::getBibliotecaSituacoes($bibliotecaId);
        }

        return $this->insertOption(null, 'Selecione uma situa&ccedil;&atilde;o', $resources);
    }

    public function bibliotecaSituacao($options = [])
    {
        parent::select($options);
    }
}
