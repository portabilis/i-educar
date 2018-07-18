<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'intranet/include/pmieducar/clsPmieducarAcervoAssunto.inc.php';

class AssuntoController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'pmieducar', 'labelAttr' => 'nm_assunto', 'idAttr' => 'cod_acervo_assunto'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getAssunto()
    {
        $obj = new clsPmieducarAcervoAssunto();
        $arrayAssuntos;

        foreach ($obj->listaAssuntosPorObra($this->getRequest()->id) as $reg) {
            $arrayAssuntos[] = $reg['ref_cod_acervo_assunto'];
        }

        return ['assuntos' => $arrayAssuntos];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'assunto-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'assunto')) {
            $this->appendResponse($this->getAssunto());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
