<?php

class LogradouroController extends ApiCoreController
{
    protected function searchOptions()
    {
        $municipioId = $this->getRequest()->municipio_id ? $this->getRequest()->municipio_id : 0;

        return ['sqlParams' => [$municipioId], 'selectFields' => ['tipo_logradouro']];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'SELECT distinct l.id as id, l.address as name, \'\'::character varying tipo_logradouro, c.name as municipio from
                 public.places l
                 INNER JOIN public.cities c ON c.id = l.city_id
                 where l.id::varchar like $1||\'%\' and (c.id = $2 OR $2 = 0)';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'SELECT distinct l.id as id, l.address as name, \'\'::character varying tipo_logradouro, c.name as municipio FROM
                 public.places l
                 INNER JOIN public.cities c ON c.id = l.city_id
                 where lower((l.address)) like \'%\'||lower(($1))||\'%\'
                 and (c.id = $2 OR $2 = 0)';

        return $sqls;
    }

    protected function formatResourceValue($resource)
    {
        $id = $resource['id'];
        $tipo = $resource['tipo_logradouro'];
        $nome = $this->toUtf8($resource['name'], ['transform' => true]);
        $municipio = $this->toUtf8($resource['municipio'], ['transform' => true]);

        return $this->getRequest()->exibir_municipio ? "$id - $tipo $nome - $municipio" : "$tipo $nome";
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'logradouro-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
