<?php

use iEducar\Legacy\Model;

class clsPmieducarEscolaUsuario extends Model
{
    public $id;
    public $ref_cod_usuario;
    public $ref_cod_escola;
    public $escola_atual;

    public function __construct($id = 0, $ref_cod_usuario = null, $ref_cod_escola = null, $escola_atual = 0)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}escola_usuario";

        $this->_campos_lista = $this->_todos_campos = 'id, ref_cod_usuario, ref_cod_escola, escola_atual';

        if (is_numeric($id)) {
            $this->id = $id;
        }
        if (is_numeric($ref_cod_usuario)) {
            $this->ref_cod_usuario = $ref_cod_usuario;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($escola_atual)) {
            $this->escola_atual = $escola_atual;
        }
    }

    public function cadastra()
    {
        $db = new clsBanco();
        $campos = '';
        $valores = '';
        $gruda = '';

        if (is_numeric($this->ref_cod_usuario)) {
            $campos .= "{$gruda}ref_cod_usuario";
            $valores .= "{$gruda}'{$this->ref_cod_usuario}'";
            $gruda = ', ';
        }
        if (is_numeric($this->ref_cod_escola)) {
            $campos .= "{$gruda}ref_cod_escola";
            $valores .= "{$gruda}'{$this->ref_cod_escola}'";
            $gruda = ', ';
        }
        if (is_numeric($this->ref_cod_escola)) {
            $campos .= "{$gruda}escola_atual";
            $valores .= "{$gruda}'0'";
        }

        $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

        return $db->InsertId("{$this->_tabela}_id_seq");
    }

    public function edita()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_cod_usuario)) {
                $set .= "{$gruda}ref_cod_usuario = '{$this->ref_cod_usuario}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}escola_atual = '0'";
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");

                return true;
            }
        }

        return false;
    }

    public function detalhe()
    {
        if (is_numeric($this->ref_cod_usuario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_usuario = '{$this->ref_cod_usuario}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function lista($ref_cod_usuario = null, $ref_cod_escola = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ref_cod_usuario)) {
            $filtros .= "{$whereAnd} ref_cod_usuario = '{$ref_cod_usuario}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$ref_cod_escola}'";
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

    public function excluir()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_usuario = '{$this->ref_cod_usuario}'");

            return true;
        }
    }

    public function excluirTodos($codUsuario)
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_usuario = '{$codUsuario}'");

        return true;
    }
}
