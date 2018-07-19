<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';

class CursoSuperiorController extends ApiCoreController
{

    // search options
    protected function sqlsForStringSearch()
    {
        $sqls[] = 'SELECT id,
                      curso_id,
                      nome || \' / \' || coalesce((CASE grau_academico
                                                      WHEN 1 THEN \'Tecnológico\'
                                                      WHEN 2 THEN \'Licenciatura\'
                                                      WHEN 3 THEN \'Bacharelado\' END), \'\') AS name
                 FROM modules.educacenso_curso_superior
                WHERE unaccent(nome) ILIKE \'%\'|| unaccent($1) ||\'%\'
                   OR curso_id ILIKE \'%\'|| $1 ||\'%\'
                LIMIT 15';

        return $sqls;
    }

    protected function formatResourceValue($resource)
    {
        return $resource['curso_id'] . ' - ' . $this->toUtf8($resource['name'], ['transform' => true]);
    }

    // sobrescrito para pesquisar apenas string, pois o codigo do curso possui letras
    protected function loadResourcesBySearchQuery($query)
    {
        $results = [];
        $sqls = $this->sqlsForStringSearch();
        $params = $this->sqlParams($query);

        if (! is_array($sqls)) {
            $sqls = [$sqls];
        }

        foreach ($sqls as $sql) {
            $_results = $this->fetchPreparedQuery($sql, $params, false);

            foreach ($_results as $result) {
                if (!isset($results[$result['id']])) {
                    $results[$result['id']] = $this->formatResourceValue($result);
                }
            }
        }

        return $results;
    }

    protected function searchOptions()
    {
        return ['namespace' => 'modules', 'table' => 'educacenso_curso_superior', 'idAttr' => 'id'];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'cursosuperior-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
