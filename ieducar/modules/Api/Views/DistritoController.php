<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class DistritoController extends ApiCoreController
{
    protected function searchOptions()
    {
        $municipioId = $this->getRequest()->city_id ? $this->getRequest()->city_id : 0;

        return ['sqlParams' => [$municipioId]];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'select id, name from public.districts where id = $1 and city_id = $2';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'select id, name from public.districts where lower(name) like \'%\'||lower($1)||\'%\' and city_id = $2';

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
