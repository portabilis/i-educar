<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';

class EtapacursoController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'modules', 'table' => 'etapas_educacenso', 'labelAttr' => 'nome', 'idAttr' => 'id'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getEtapacurso()
    {
        $arrayEtapacurso;
        $sql = 'SELECT * FROM modules.etapas_curso_educacenso WHERE curso_id = $1';

        foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => $this->getRequest()->curso_id]) as $reg) {
            $arrayEtapacurso[] = $reg['etapa_id'];
        }

        return ['etapacurso' => $arrayEtapacurso];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'etapacurso-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'etapacurso')) {
            $this->appendResponse($this->getEtapacurso());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
