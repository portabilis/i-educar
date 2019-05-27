<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class DeficienciaController extends ApiCoreController
{
    protected function searchOptions()
    {
        return ['namespace' => 'cadastro', 'labelAttr' => 'nm_deficiencia', 'idAttr' => 'cod_deficiencia'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getDeficiencias()
    {
        $params = [];
        $modified = $this->getRequest()->modified ?: null;

        if ($modified) {
            $params[] = $modified;
            $modified = 'AND updated_at >= $1';
        }

        $sql = "
            (
                SELECT cod_deficiencia, nm_deficiencia, desconsidera_regra_diferenciada, updated_at, null as deleted_at
                FROM cadastro.deficiencia
                {$modified}
            )
            
            UNION ALL
            
            (
                SELECT cod_deficiencia, nm_deficiencia, desconsidera_regra_diferenciada, updated_at, deleted_at
                FROM cadastro.deficiencia_excluidos
                {$modified} 
            )
        ";

        $deficiencias = $this->fetchPreparedQuery($sql, $params);

        $attrs = [
            'cod_deficiencia' => 'id',
            'nm_deficiencia' => 'nome',
            'desconsidera_regra_diferenciada' => 'desconsidera_regra_diferenciada',
            'updated_at' => 'updated_at',
            'deleted_at' => 'deleted_at',
        ];

        $deficiencias = Portabilis_Array_Utils::filterSet($deficiencias, $attrs);

        $deficiencias = array_map(function ($deficiencia) {
            $deficiencia['desconsidera_regra_diferenciada'] = dbBool($deficiencia['desconsidera_regra_diferenciada']);

            return $deficiencia;
        }, $deficiencias);

        return [
            'deficiencias' => $deficiencias
        ];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'deficiencia-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'deficiencias')) {
            $this->appendResponse($this->getDeficiencias());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
