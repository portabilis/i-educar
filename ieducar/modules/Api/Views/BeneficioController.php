<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'intranet/include/pmieducar/clsPmieducarAlunoBeneficio.inc.php';

class BeneficioController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'pmieducar', 'table' => 'beneficio_aluno','labelAttr' => 'nm_beneficio', 'idAttr' => 'cod_beneficio_aluno'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getBeneficios()
    {
        $obj = new clsPmieducarAlunoBeneficio();
        $arrayBeneficios;

        foreach ($obj->listaBeneficiosPorAluno($this->getRequest()->id) as $reg) {
            $arrayBeneficios[] = $reg['aluno_beneficio_id'];
        }

        return ['beneficios' => $arrayBeneficios];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'assunto-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'assunto')) {
            $this->appendResponse($this->getBeneficios());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
