<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';

class RotinasAuditoriaController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return [
            'namespace' => 'modules',
            'table' => 'auditoria_geral',
            'idAttr' => 'rotina'
        ];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function sqlsForNumericSearch()
    {
        return 'SELECT DISTINCT rotina AS id,
                       rotina AS name
                  FROM modules.auditoria_geral
                 WHERE rotina::varchar like \'%\'||$1||\'%\'
                 ORDER BY rotina
                 LIMIT 15';
    }

    protected function sqlsForStringSearch()
    {
        return 'SELECT DISTINCT rotina AS id,
                       rotina AS name
                  FROM modules.auditoria_geral
                 WHERE rotina::varchar like \'%\'||$1||\'%\'
                 ORDER BY rotina
                 LIMIT 15';
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'rotinas-auditoria-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
