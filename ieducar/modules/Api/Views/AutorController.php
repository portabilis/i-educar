<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'intranet/include/pmieducar/clsPmieducarAcervoAcervoAutor.inc.php';

class AutorController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'pmieducar', 'table' => 'acervo_autor', 'labelAttr' => 'nm_autor', 'idAttr' => 'cod_acervo_autor'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getAutor()
    {
        $obj = new clsPmieducarAcervoAcervoAutor();
        $arrayAutores;

        foreach ($obj->listaAutoresPorObra($this->getRequest()->id) as $reg) {
            $arrayAutores[] = [
                'id' => $reg['id'],
                'text' => $reg['nome'],
            ];
        }

        return ['autores' => $arrayAutores];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'autor-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'autor')) {
            $this->appendResponse($this->getAutor());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
