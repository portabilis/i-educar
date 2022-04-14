<?php

class IesController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'modules', 'table' => 'educacenso_ies', 'idAttr' => 'id'];
    }

    protected function formatResourceValue($resource)
    {
        return $resource['ies_id'] . ' - ' . $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function sqlsForNumericSearch()
    {
        return 'select id, ies_id, nome as name from modules.educacenso_ies
            where ies_id::varchar like $1||\'%\' order by ies_id limit 15';
    }

    protected function sqlsForStringSearch()
    {
        return 'select id, ies_id, nome as name from modules.educacenso_ies
            where f_unaccent(nome) ilike f_unaccent(\'%\'||$1||\'%\') order by name limit 15';
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'ies-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
