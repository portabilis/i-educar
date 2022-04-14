<?php

use iEducar\Legacy\Model;

class clsPmieducarClienteTipoExemplarTipo extends Model
{
    public $ref_cod_cliente_tipo;
    public $ref_cod_exemplar_tipo;
    public $dias_emprestimo;

    public function __construct($ref_cod_cliente_tipo = null, $ref_cod_exemplar_tipo = null, $dias_emprestimo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}cliente_tipo_exemplar_tipo";

        $this->_campos_lista = $this->_todos_campos = 'ctet.ref_cod_cliente_tipo, ctet.ref_cod_exemplar_tipo, ctet.dias_emprestimo';

        if (is_numeric($ref_cod_exemplar_tipo)) {
            $this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
        }
        if (is_numeric($ref_cod_cliente_tipo)) {
            $this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
        }

        if (is_numeric($dias_emprestimo)) {
            $this->dias_emprestimo = $dias_emprestimo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_exemplar_tipo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_cliente_tipo)) {
                $campos .= "{$gruda}ref_cod_cliente_tipo";
                $valores .= "{$gruda}'{$this->ref_cod_cliente_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_exemplar_tipo)) {
                $campos .= "{$gruda}ref_cod_exemplar_tipo";
                $valores .= "{$gruda}'{$this->ref_cod_exemplar_tipo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dias_emprestimo)) {
                $campos .= "{$gruda}dias_emprestimo";
                $valores .= "{$gruda}'{$this->dias_emprestimo}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;
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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_exemplar_tipo)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->dias_emprestimo)) {
                $set .= "{$gruda}dias_emprestimo = '{$this->dias_emprestimo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'");

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
    public function lista($int_ref_cod_cliente_tipo = null, $int_ref_cod_exemplar_tipo = null, $int_dias_emprestimo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ctet, {$this->_schema}cliente_tipo ct, {$this->_schema}exemplar_tipo et";

        $whereAnd = ' AND ';
        $filtros = ' WHERE ctet.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ctet.ref_cod_exemplar_tipo = et.cod_exemplar_tipo ';

        if (is_numeric($int_ref_cod_cliente_tipo)) {
            $filtros .= "{$whereAnd} ctet.ref_cod_cliente_tipo = '{$int_ref_cod_cliente_tipo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_exemplar_tipo)) {
            $filtros .= "{$whereAnd} ctet.ref_cod_exemplar_tipo = '{$int_ref_cod_exemplar_tipo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dias_emprestimo)) {
            $filtros .= "{$whereAnd} ctet.dias_emprestimo = '{$int_dias_emprestimo}'";
            $whereAnd = ' AND ';
        }

        $filtros .= "{$whereAnd} ct.ativo = '1'";
        $whereAnd = ' AND ';

        $filtros .= "{$whereAnd} et.ativo = '1'";
        $whereAnd = ' AND ';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ctet, {$this->_schema}cliente_tipo ct, {$this->_schema}exemplar_tipo et {$filtros}");

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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_exemplar_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ctet WHERE ctet.ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ctet.ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'");
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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_exemplar_tipo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'");
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
        if (is_numeric($this->ref_cod_cliente_tipo) && is_numeric($this->ref_cod_exemplar_tipo)) {
        }

        return false;
    }
}
