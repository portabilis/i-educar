<?php

use iEducar\Legacy\Model;

class clsModulesTipoVeiculo extends Model
{
    public $cod_tipo_veiculo;
    public $descricao;

    public function __construct($cod_tipo_veiculo = null, $descricao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}tipo_veiculo";

        $this->_campos_lista = $this->_todos_campos = ' cod_tipo_veiculo, descricao';

        if (is_numeric($cod_tipo_veiculo)) {
            $this->cod_tipo_veiculo = $cod_tipo_veiculo;
        }

        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->descricao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}descricao";
            $valores .= "{$gruda}'{$this->descricao}'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_string($this->descricao)) {
            $db = new clsBanco();
            $set = '';

            $set .= "descricao = '{$this->descricao}'";

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista($cod_tipo_veiculo = null, $descricao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_tipo_veiculo)) {
            $filtros .= "{$whereAnd} cod_tipo_veiculo = '{$cod_tipo_veiculo}'";
            $whereAnd = ' AND ';
        }

        if (is_string($descricao)) {
            $filtros .= "{$whereAnd} (LOWER(descricao)) LIKE (LOWER('%{$descricao}%'))";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos}, (
              SELECT
                nome
              FROM
                cadastro.pessoa
              WHERE
                idpes = ref_idpes
             ) AS nome_empresa , (SELECT nome FROM cadastro.pessoa WHERE idpes = ref_resp_idpes) AS nome_responsavel FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_empresa_transporte_escolar)) {
            $sql = "DELETE FROM {$this->_tabela} WHERE cod_empresa_transporte_escolar = '{$this->cod_empresa_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
