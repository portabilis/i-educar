<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class DistritoController extends ApiCoreController
{

    protected function searchOptions()
    {
        $municipioId = $this->getRequest()->municipio_id ? $this->getRequest()->municipio_id : 0;

        return ['sqlParams' => [$municipioId]];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'select iddis as id, nome as name from
                 public.distrito where iddis like $1||\'%\' and idmun = $2 ';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'select iddis as id, nome as name from
                 public.distrito where lower((nome)) like \'%\'||lower(($1))||\'%\' and idmun = $2 ';

        return $sqls;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'distrito-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
