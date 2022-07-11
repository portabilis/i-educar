<?php

use iEducar\Legacy\Model;

class clsPmieducarBibliotecaFeriados extends Model
{
    public $cod_feriado;
    public $ref_cod_biblioteca;
    public $nm_feriado;
    public $descricao;
    public $data_feriado;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_feriado = null, $ref_cod_biblioteca = null, $nm_feriado = null, $descricao = null, $data_feriado = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}biblioteca_feriados";

        $this->_campos_lista = $this->_todos_campos = 'cod_feriado, ref_cod_biblioteca, nm_feriado, descricao, data_feriado, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }

        if (is_numeric($cod_feriado)) {
            $this->cod_feriado = $cod_feriado;
        }
        if (is_string($nm_feriado)) {
            $this->nm_feriado = $nm_feriado;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (($data_feriado)) {
            $this->data_feriado = $data_feriado;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_biblioteca) && is_string($this->nm_feriado) && ($this->data_feriado)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_feriado)) {
                $campos .= "{$gruda}nm_feriado";
                $valores .= "{$gruda}'{$this->nm_feriado}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            if (($this->data_feriado)) {
                $campos .= "{$gruda}data_feriado";
                $valores .= "{$gruda}'{$this->data_feriado}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_feriado_seq");
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
        if (is_numeric($this->cod_feriado)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_cod_biblioteca)) {
                $set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_feriado)) {
                $set .= "{$gruda}nm_feriado = '{$this->nm_feriado}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }
            if (($this->data_feriado)) {
                $set .= "{$gruda}data_feriado = '{$this->data_feriado}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_feriado = '{$this->cod_feriado}'");

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
    public function lista($int_cod_feriado = null, $int_ref_cod_biblioteca = null, $str_nm_feriado = null, $str_descricao = null, $time_data_feriado_ini = null, $time_data_feriado_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_feriado)) {
            $filtros .= "{$whereAnd} cod_feriado = '{$int_cod_feriado}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_feriado)) {
            $filtros .= "{$whereAnd} nm_feriado LIKE '%{$str_nm_feriado}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (($time_data_feriado_ini)) {
            $filtros .= "{$whereAnd} data_feriado >= '{$time_data_feriado_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_data_feriado_fim)) {
            $filtros .= "{$whereAnd} data_feriado <= '{$time_data_feriado_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
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
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_feriado)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_feriado = '{$this->cod_feriado}'");
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
        if (is_numeric($this->cod_feriado)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_feriado = '{$this->cod_feriado}'");
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
        if (is_numeric($this->cod_feriado)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a uma biblioteca
     */
    public function excluirTodos($ref_cod_biblioteca)
    {
        if (is_numeric($ref_cod_biblioteca)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$ref_cod_biblioteca}'");

            return true;
        }

        return false;
    }
}
