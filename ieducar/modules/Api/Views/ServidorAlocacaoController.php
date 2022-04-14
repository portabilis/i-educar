<?php

class ServidorAlocacaoController extends ApiCoreController
{
    public function getDadosServidorAlocacao ()
    {
        $cod_servidor = $this->getRequest()->cod_servidor;

        if (is_numeric($cod_servidor)) {
            $obj = new clsPmieducarServidorAlocacao(null, 1, null, null, null, $cod_servidor, null, null, null, null, null, null, null, date("Y"));
            $servidor_alocacoes = $obj->detalhePorServidor();

            return ['result' => $servidor_alocacoes];
        } else {
            return ['result' => null];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'dados-servidor-alocacao')) {
            $this->appendResponse($this->getDadosServidorAlocacao());
        }
    }
}
