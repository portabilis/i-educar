<?php

use iEducar\Legacy\Model;

class clsPmieducarCategoriaObra extends Model
{
    public $id;
    public $descricao;
    public $observacoes;

    public function __construct($id = '', $descricao = '', $observacoes = '')
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}categoria_obra";

        $this->_campos_lista = $this->_todos_campos = 'id, descricao, observacoes';

        if (is_numeric($id)) {
            $this->id = $id;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_string($observacoes)) {
            $this->observacoes = $observacoes;
        }
    }

    public function lista($descricao = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_string($descricao)) {
            $desc = $db->escapeString($descricao);
            $filtros .= "{$whereAnd} descricao LIKE '%{$desc}%'";
            $whereAnd = ' AND ';
        }

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

    public function detalhe()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE id = '{$this->id}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function cadastra()
    {
        if (is_string($this->descricao)) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$descricao}'";
                $gruda = ', ';
            }
            if (is_string($this->observacoes)) {
                $observacoes = $db->escapeString($this->observacoes);
                $campos .= "{$gruda}observacoes";
                $valores .= "{$gruda}'{$observacoes}'";
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_id_seq");
        }

        return false;
    }

    public function edita()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $set = '';
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set .= "{$gruda}descricao = '{$descricao}'";
                $gruda = ', ';
            }
            if (is_string($this->observacoes)) {
                $observacoes = $db->escapeString($this->observacoes);
                $set .= "{$gruda}observacoes = '{$observacoes}'";
                $gruda = ', ';
            }
            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");

                return true;
            }
        }

        return false;
    }

    public function excluir()
    {
        if (is_numeric($this->id)) {
            $db = new clsBanco();
            $getVinculoObra = $db->Consulta("SELECT *
                                               FROM relacao_categoria_acervo
                                              WHERE categoria_id = {$this->id}");
            if (pg_num_rows($getVinculoObra) > 0) {
                return false;
            } else {
                $db->Consulta("DELETE FROM {$this->_tabela} WHERE id = '{$this->id}'");

                return true;
            }
        }
    }
}
