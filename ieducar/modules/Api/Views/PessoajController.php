<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class PessoajController extends ApiCoreController
{

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'select distinct idpes as id, nome as name from
            cadastro.pessoa where tipo=\'J\' and idpes::varchar like $1||\'%\'';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'select distinct idpes as id, nome as name from
            cadastro.pessoa where tipo=\'J\' and lower((nome)) like \'%\'||lower(($1))||\'%\'';

        return $sqls;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'pessoaj-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
