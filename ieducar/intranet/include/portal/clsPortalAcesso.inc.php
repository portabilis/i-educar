<?php

use iEducar\Legacy\Model;

class clsPortalAcesso extends Model
{
    public $cod_acesso;
    public $data_hora;
    public $ip_externo;
    public $ip_interno;
    public $cod_pessoa;
    public $obs;
    public $sucesso;

    public function __construct($cod_acesso = null, $data_hora = null, $ip_externo = null, $ip_interno = null, $cod_pessoa = null, $obs = null, $sucesso = null)
    {
        $db = new clsBanco();
        $this->_schema = 'portal.';
        $this->_tabela = "{$this->_schema}acesso";

        $this->_campos_lista = $this->_todos_campos = 'cod_acesso, data_hora, ip_externo, ip_interno, cod_pessoa, obs, sucesso';

        if (is_numeric($cod_acesso)) {
            $this->cod_acesso = $cod_acesso;
        }
        if (is_string($data_hora)) {
            $this->data_hora = $data_hora;
        }
        if (is_string($ip_externo)) {
            $this->ip_externo = $ip_externo;
        }
        if (is_string($ip_interno)) {
            $this->ip_interno = $ip_interno;
        }
        if (is_numeric($cod_pessoa)) {
            $this->cod_pessoa = $cod_pessoa;
        }
        if (is_string($obs)) {
            $this->obs = $obs;
        }
        if (!is_null($sucesso)) {
            $this->sucesso = $sucesso;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->data_hora) && is_string($this->ip_externo) && is_string($this->ip_interno) && is_numeric($this->cod_pessoa) && !is_null($this->sucesso)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->data_hora)) {
                $campos .= "{$gruda}data_hora";
                $valores .= "{$gruda}'{$this->data_hora}'";
                $gruda = ', ';
            }
            if (is_string($this->ip_externo)) {
                $campos .= "{$gruda}ip_externo";
                $valores .= "{$gruda}'{$this->ip_externo}'";
                $gruda = ', ';
            }
            if (is_string($this->ip_interno)) {
                $campos .= "{$gruda}ip_interno";
                $valores .= "{$gruda}'{$this->ip_interno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_pessoa)) {
                $campos .= "{$gruda}cod_pessoa";
                $valores .= "{$gruda}'{$this->cod_pessoa}'";
                $gruda = ', ';
            }
            if (is_string($this->obs)) {
                $campos .= "{$gruda}obs";
                $valores .= "{$gruda}'{$this->obs}'";
                $gruda = ', ';
            }
            if (!is_null($this->sucesso)) {
                $campos .= "{$gruda}sucesso";
                if (dbBool($this->sucesso)) {
                    $valores .= "{$gruda}true";
                } else {
                    $valores .= "{$gruda}false";
                }
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_acesso_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_acesso)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->data_hora)) {
                $set .= "{$gruda}data_hora = '{$this->data_hora}'";
                $gruda = ', ';
            }
            if (is_string($this->ip_externo)) {
                $set .= "{$gruda}ip_externo = '{$this->ip_externo}'";
                $gruda = ', ';
            }
            if (is_string($this->ip_interno)) {
                $set .= "{$gruda}ip_interno = '{$this->ip_interno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_pessoa)) {
                $set .= "{$gruda}cod_pessoa = '{$this->cod_pessoa}'";
                $gruda = ', ';
            }
            if (is_string($this->obs)) {
                $set .= "{$gruda}obs = '{$this->obs}'";
                $gruda = ', ';
            }
            if (!is_null($this->sucesso)) {
                $val = dbBool($this->sucesso) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}sucesso = {$val}";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_acesso = '{$this->cod_acesso}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($date_data_hora_ini = null, $date_data_hora_fim = null, $str_ip_externo = null, $str_ip_interno = null, $int_cod_pessoa = null, $str_obs = null, $bool_sucesso = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_acesso)) {
            $filtros .= "{$whereAnd} cod_acesso = '{$int_cod_acesso}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_hora_ini)) {
            $filtros .= "{$whereAnd} data_hora >= '{$date_data_hora_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_hora_fim)) {
            $filtros .= "{$whereAnd} data_hora <= '{$date_data_hora_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_ip_externo)) {
            $filtros .= "{$whereAnd} ip_externo LIKE '%{$str_ip_externo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_ip_interno)) {
            $filtros .= "{$whereAnd} ip_interno LIKE '%{$str_ip_interno}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cod_pessoa)) {
            $filtros .= "{$whereAnd} cod_pessoa = '{$int_cod_pessoa}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_obs)) {
            $filtros .= "{$whereAnd} obs LIKE '%{$str_obs}%'";
            $whereAnd = ' AND ';
        }
        if (!is_null($bool_sucesso)) {
            if (dbBool($bool_sucesso)) {
                $filtros .= "{$whereAnd} sucesso = TRUE";
            } else {
                $filtros .= "{$whereAnd} sucesso = FALSE";
            }
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna uma lista de falhas filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista_falhas($int_cod_pessoa = null, $int_min_quantidade_falhas = null, $int_max_quantidade_falhas = null, $date_ultimo_sucesso_ini = null, $date_ultimo_sucesso_fim = null, $date_quinto_erro_ini = null, $date_quinto_erro_fim = null)
    {
        $query_fonte = '
    SELECT COUNT(a1.cod_pessoa) AS falha, sub2.cod_pessoa, sub2.ultimo_sucesso
    , ( SELECT a3.data_hora FROM acesso AS a3 WHERE a3.cod_pessoa = sub2.cod_pessoa AND a3.sucesso = \'f\' ORDER BY a3.data_hora DESC LIMIT 1 OFFSET 4 ) AS quinto_erro
    FROM acesso AS a1,
    (
        SELECT sub1.cod_pessoa,
        CASE WHEN sub1.ultimo_sucesso > ( NOW() - time \'00:30\' ) THEN
            sub1.ultimo_sucesso
        ELSE
            NOW() - time \'00:30\'
        END AS ultimo_sucesso
        FROM (
            SELECT
            a2.cod_pessoa,
            MAX(a2.data_hora) AS ultimo_sucesso
            FROM acesso AS a2
            WHERE
            sucesso = \'t\'
            GROUP BY cod_pessoa
        ) AS sub1
    ) AS sub2
    WHERE a1.cod_pessoa = sub2.cod_pessoa
    AND a1.data_hora > sub2.ultimo_sucesso
    AND a1.sucesso = \'f\'
    GROUP BY sub2.cod_pessoa, sub2.ultimo_sucesso
';
        $sql = "
SELECT falha, cod_pessoa, ultimo_sucesso, quinto_erro FROM
(
{$query_fonte}
) AS sub3
";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_pessoa)) {
            $filtros .= "{$whereAnd} cod_pessoa = '{$int_cod_pessoa}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_min_quantidade_falhas)) {
            $filtros .= "{$whereAnd} falha >= '{$int_min_quantidade_falhas}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_max_quantidade_falhas)) {
            $filtros .= "{$whereAnd} falha <= '{$int_max_quantidade_falhas}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_ultimo_sucesso_ini)) {
            $filtros .= "{$whereAnd} ultimo_sucesso >= '{$date_ultimo_sucesso_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_ultimo_sucesso_fim)) {
            $filtros .= "{$whereAnd} ultimo_sucesso <= '{$date_ultimo_sucesso_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_quinto_erro_ini)) {
            $filtros .= "{$whereAnd} quinto_erro >= '{$date_quinto_erro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_quinto_erro_fim)) {
            $filtros .= "{$whereAnd} quinto_erro <= '{$date_quinto_erro_fim}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("
SELECT COUNT(0) FROM (
{$query_fonte}
) AS sub3 {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_acesso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acesso = '{$this->cod_acesso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_acesso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_acesso = '{$this->cod_acesso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_acesso)) {
        }

        return false;
    }
}
