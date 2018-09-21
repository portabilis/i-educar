<?php

require_once 'include/clsBanco.inc.php';
require_once 'lib/Portabilis/Controller/ApiCoreController.php';

class ConsultaBaseController extends ApiCoreController
{
    protected $pdo;

    protected function canGetAlunos()
    {
        return true;
    }

    protected function getPDO()
    {
        if (is_null($this->pdo)) {
            $base = new clsBanco();
            $base->FraseConexao();
            $connectionString = 'pgsql:' . $base->getFraseConexao();

            $this->pdo = new \PDO($connectionString);
        }

        return $this->pdo;
    }

    protected function getAlunos() {
        if (!$this->canGetAlunos()) {
            return null;
        }

        return $this->getData();
    }

    protected function getData() {
        return [];
    }

    protected function getMethodName($type)
    {
        $name = 'getData';
        $type = str_replace('_', ' ', $type);
        $type = ucwords($type);
        $type = str_replace(' ', '', $type);

        return $name . $type;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'alunos')) {
            $this->appendResponse($this->getAlunos());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
