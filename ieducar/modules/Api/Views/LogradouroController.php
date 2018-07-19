<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class LogradouroController extends ApiCoreController
{

    protected function searchOptions()
    {
        $municipioId = $this->getRequest()->municipio_id ? $this->getRequest()->municipio_id : 0;

        return ['sqlParams' => [$municipioId], 'selectFields' => ['tipo_logradouro']];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'SELECT distinct l.idlog as id, l.nome as name, tl.descricao as tipo_logradouro, m.nome as municipio from
                 public.logradouro l left join urbano.tipo_logradouro tl on (l.idtlog = tl.idtlog)
                 INNER JOIN public.municipio m ON m.idmun = l.idmun
                 where l.idlog::varchar like $1||\'%\' and (m.idmun = $2 OR $2 = 0)';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'SELECT distinct l.idlog as id, l.nome as name, tl.descricao as tipo_logradouro, m.nome as municipio FROM
                 public.logradouro l left join urbano.tipo_logradouro tl on (l.idtlog = tl.idtlog)
                 INNER JOIN public.municipio m ON m.idmun = l.idmun
                 where (lower((l.nome)) like \'%\'||lower(($1))||\'%\'
                 OR lower((tl.descricao))|| \' \' ||lower((l.nome)) like \'%\'||lower(($1))||\'%\')
                 and (m.idmun = $2 OR $2 = 0)';

        return $sqls;
    }

    protected function formatResourceValue($resource)
    {
        $id = $resource['id'];
        $tipo = $resource['tipo_logradouro'];
        $nome = $this->toUtf8($resource['name'], ['transform' => true]);
        $municipio = $this->toUtf8($resource['municipio'], ['transform' => true]);

        return $this->getRequest()->exibir_municipio ? "$id - $tipo $nome - $municipio": "$tipo $nome";
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
