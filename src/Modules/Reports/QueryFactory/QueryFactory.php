<?php

namespace iEducar\Modules\Reports\QueryFactory;

require_once 'include/clsBanco.inc.php';

class QueryFactory
{
    protected $pdo;

    protected $keys = [];

    protected $defaults = [];

    protected $query = '';

    protected $params = [];

    public function __construct($params = [])
    {
        $this->params = $params;

        $banco = new \clsBanco();

        $banco->FraseConexao();

        $this->pdo = new \PDO('pgsql:' . $banco->getFraseConexao());
    }

    public function getData()
    {
        $query = $this->query;
        $values = [];

        foreach ($this->keys as $key) {
            $value = $this->params[$key] ?? $this->defaults[$key] ?? null;

            if (is_null($value)) {
                throw new \InvalidArgumentException(
                    sprintf('O parâmetro "%s" não está presente ou não possui um valor padrão definido.', $key)
                );
            }

            if (is_array($value)) {
                $tmpValues = [];

                foreach ($value as $k => $v) {
                    $tmpValues[':' . $key . '_' . $k] = $v;
                }

                $query = str_replace(':' . $key, join(', ', array_keys($tmpValues)), $query);

                $values += $tmpValues;
            } else {
                $values[':' . $key] = $value;
            }
        }

        $statement = $this->pdo->prepare($query);

        $statement->execute($values);

        return $statement->fetchAll();
    }

    public function setParams($params)
    {
        $this->params = $params;
    }
}
