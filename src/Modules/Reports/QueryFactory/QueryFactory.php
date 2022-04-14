<?php

namespace iEducar\Modules\Reports\QueryFactory;

class QueryFactory
{
    protected $pdo;

    protected $keys = [];

    protected $defaults = [];

    protected $query = '';

    protected $params = [];

    public function __construct(\PDO $connection, $params = [])
    {
        $this->params = $params;
        $this->pdo = $connection;
    }

    public function query()
    {
        return $this->query;
    }

    public function getData()
    {
        $query = $this->query();
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

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function setParams($params)
    {
        $this->params = $params;
    }
}
