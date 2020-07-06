<?php

use Illuminate\Support\Facades\DB;

require_once 'include/clsBancoPgSql.inc.php';

class clsBanco extends clsBancoSQL_
{
    public function __construct($strDataBase = false)
    {
        parent::__construct($strDataBase);

        $connection = DB::connection()->getConfig();

        $this->setHost($connection['host']);
        $this->setDbname($connection['database']);
        $this->setPassword($connection['password']);
        $this->setUser($connection['username']);
        $this->setPort($connection['port']);
    }

    /**
     * Retorna a quantidade de registros de uma tabela baseado no objeto que a
     * abstrai. Este deve ter um atributo público Object->_tabela.
     *
     * @param   mixed   Objeto que abstrai a tabela
     * @param   string  Nome da coluna para cálculo COUNT()
     *
     * @return int Quantidade de registros da tabela
     *
     * @throws Exception
     */
    public function doCountFromObj($obj, $column = '*')
    {
        if ($obj->_tabela == null) {
            return false;
        }

        $sql = sprintf('SELECT COUNT(%s) FROM %s', $column, $obj->_tabela);
        $this->Consulta($sql);

        return (int) $this->UnicoCampo($sql);
    }

    /**
     * Retorna os dados convertidos para a sintaxe SQL aceita por ext/pgsql.
     *
     * <code>
     * <?php
     * $data = array(
     *   'id' => 1,
     *   'hasChild' = FALSE
     * );
     *
     * $clsBanco->getDbValuesFromArray($data);
     * // array(
     * //   'id' => 1,
     * //   'hasChild' => 'f'
     * // );
     * </code>
     *
     * Apenas o tipo booleano é convertido.
     *
     * @param array $data Array associativo com os valores a serem convertidos.
     *
     * @return array
     */
    public function formatValues(array $data)
    {
        $db = [];

        foreach ($data as $key => $val) {
            if (is_bool($val)) {
                $db[$key] = $this->_formatBool($val);
                continue;
            }

            $db[$key] = $val;
        }

        return $db;
    }

    /**
     * Retorna um valor formatado de acordo com o tipo output do tipo booleano
     * no PostgreSQL.
     *
     * @link   http://www.postgresql.org/docs/8.2/interactive/datatype-boolean.html
     * @link   http://www.php.net/manual/en/function.pg-query-params.php#78072
     *
     * @param mixed $val
     *
     * @return string "t" para TRUE e "f" para false
     */
    protected function _formatBool($val)
    {
        return ($val == true ? 't' : 'f');
    }
    public function escapeString($string)
    {
        return pg_escape_string($string);
    }
}
