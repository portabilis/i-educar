<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class DeficienciaController extends ApiCoreController
{

    // search options
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
        $sql = " SELECT cod_deficiencia, nm_deficiencia
                   FROM cadastro.deficiencia
                  WHERE desconsidera_regra_diferenciada = 'FALSE'";

        $deficiencias = $this->fetchPreparedQuery($sql);

        foreach ($deficiencias as &$deficiencia) {
            $deficiencia['nm_deficiencia'] = Portabilis_String_Utils::toUtf8($deficiencia['nm_deficiencia']);
        }

        $attrs = [
            'cod_deficiencia' => 'id',
            'nm_deficiencia' => 'nome'
        ];

        $deficiencias = Portabilis_Array_Utils::filterSet($deficiencias, $attrs);

        return ['deficiencias' => $deficiencias ];
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
