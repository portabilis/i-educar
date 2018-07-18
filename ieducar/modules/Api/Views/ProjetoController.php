<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class ProjetoController extends ApiCoreController
{
    protected function searchOptions()
    {
        return ['namespace' => 'pmieducar', 'idAttr' => 'cod_projeto'];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'projeto-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
