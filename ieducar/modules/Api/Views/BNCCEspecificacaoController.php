<?php

class BNCCEspecificacaoController extends ApiCoreController
{
    public function retrieve()
    {
        $bncc_id = $this->getRequest()->bncc_id;

        $obj = new clsModulesBNCCEspecificacao();
        $result = $obj->lista($bncc_id);

        return [ "result" => $result ];
    }

    public function list()
    {
        $bnccArray = $this->getRequest()->bnccArray;

        $obj = new clsModulesBNCCEspecificacao();
        $result = $obj->lista2($bnccArray);

        return [ "result" => $result ];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'retrieve')) {
            $this->appendResponse($this->retrieve());
        } else if ($this->isRequestFor('get', 'list')) {
            $this->appendResponse($this->list());
        }
    }
}
