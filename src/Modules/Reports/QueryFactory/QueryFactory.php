<?php

namespace iEducar\Modules\Reports\QueryFactory;

class QueryFactory
{
    protected $keys = [];

    protected $defaults = [];

    protected $query = '';

    protected $params = [];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function generate()
    {
        $query = $this->query;

        foreach ($this->keys as $key) {
            $value = $this->params[$key] ?? $this->defaults[$key] ?? null;

            if (is_null($value)) {
                throw new \InvalidArgumentException(
                    sprintf('O parâmetro "%s" não está presente ou não possui um valor padrão definido.', $key)
                );
            }

            $searchKey = '{' . $key . '}';
            $query = str_replace($searchKey, $value, $query);
        }

        return $query;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }
}
