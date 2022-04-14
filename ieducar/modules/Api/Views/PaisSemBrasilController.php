<?php

class PaisSemBrasilController extends ApiCoreController
{
    protected function searchOptions()
    {
        return ['namespace' => 'public', 'idAttr' => 'idpais', 'table' => 'pais'];
    }

    protected function sqlsForNumericSearch()
    {
        $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

        $namespace = $searchOptions['namespace'];
        $table = $searchOptions['table'];
        $idAttr = $searchOptions['idAttr'];
        $labelAttr = $searchOptions['labelAttr'];

        $searchOptions['selectFields'][] = "$idAttr as id, $labelAttr as name";
        $selectFields = join(', ', $searchOptions['selectFields']);

        return "select distinct $selectFields from $namespace.$table where $idAttr::varchar like $1||'%' AND nome <> 'Brasil' AND coalesce(cod_ibge, 0) <> 76 order by $idAttr limit 15";
    }

    protected function sqlsForStringSearch()
    {
        $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

        $namespace = $searchOptions['namespace'];
        $table = $searchOptions['table'];
        $idAttr = $searchOptions['idAttr'];
        $labelAttr = $searchOptions['labelAttr'];

        $searchOptions['selectFields'][] = "$idAttr as id, $labelAttr as name";
        $selectFields = join(', ', $searchOptions['selectFields']);

        return "select distinct $selectFields from $namespace.$table where lower($labelAttr) like '%'||lower($1)||'%' AND nome <> 'Brasil' AND coalesce(cod_ibge, 0) <> 76 order by $labelAttr limit 15";
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'pais-sem-brasil-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
