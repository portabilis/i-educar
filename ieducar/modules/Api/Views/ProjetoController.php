<?php

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
