<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

class SetorController extends ApiCoreController
{
    protected function getSetor()
    {
        return ['options' => []];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'setor')) {
            $this->appendResponse($this->getSetor());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
