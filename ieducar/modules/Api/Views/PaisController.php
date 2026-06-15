<?php

class PaisController extends ApiCoreController
{
    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'public', 'table' => 'countries', 'idAttr' => 'id', 'labelAttr' => 'name'];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'pais-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
