<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class BairroController extends ApiCoreController
{

    protected function searchOptions()
    {
        $distritoId = $this->getRequest()->distrito_id ? $this->getRequest()->distrito_id : 0;

        return ['sqlParams' => [$distritoId], 'selectFields' => ['zona_localizacao']];
    }

    protected function formatResourceValue($resource)
    {
        $id = $resource['id'];
        $zona = $resource['zona_localizacao'] == 1 ? 'Urbana' : 'Rural';
        $nome = $this->toUtf8($resource['name'], ['transform' => true]);
        $municipio = $this->toUtf8($resource['municipio'], ['transform' => true]);

        return $this->getRequest()->exibir_municipio ? "$id - $nome - $municipio" : "$nome / Zona $zona ";
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'SELECT b.idbai as id, b.nome as name, zona_localizacao, m.nome as municipio from
            public.bairro b
            INNER JOIN public.municipio m ON m.idmun = b.idmun
            where idbai::varchar like $1||\'%\' and (iddis = $2 or $2 = 0 )';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'SELECT b.idbai as id, b.nome as name, zona_localizacao, m.nome as municipio from
                 public.bairro b
                 INNER JOIN public.municipio m ON m.idmun = b.idmun
                 where lower((b.nome)) like \'%\'||lower(($1))||\'%\' and (b.iddis = $2 or $2 = 0) ';

        return $sqls;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'bairro-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
