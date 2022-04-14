<?php

use iEducar\Legacy\Model;

class clsPmieducarHabilitacaoCurso extends Model
{
    public $ref_cod_habilitacao;
    public $ref_cod_curso;

    public function __construct($ref_cod_habilitacao = null, $ref_cod_curso = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}habilitacao_curso";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_habilitacao, ref_cod_curso';

        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }
        if (is_numeric($ref_cod_habilitacao)) {
            $this->ref_cod_habilitacao = $ref_cod_habilitacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_habilitacao) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_habilitacao)) {
                $campos .= "{$gruda}ref_cod_habilitacao";
                $valores .= "{$gruda}'{$this->ref_cod_habilitacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
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
        if (is_numeric($this->ref_cod_habilitacao) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_habilitacao = '{$this->ref_cod_habilitacao}' AND ref_cod_curso = '{$this->ref_cod_curso}'");

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
    public function lista($int_ref_cod_habilitacao = null, $int_ref_cod_curso = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_habilitacao)) {
            $filtros .= "{$whereAnd} ref_cod_habilitacao = '{$int_ref_cod_habilitacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} ref_cod_curso = '{$int_ref_cod_curso}'";
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
        if (is_numeric($this->ref_cod_habilitacao) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_habilitacao = '{$this->ref_cod_habilitacao}' AND ref_cod_curso = '{$this->ref_cod_curso}'");
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
        if (is_numeric($this->ref_cod_habilitacao) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_habilitacao = '{$this->ref_cod_habilitacao}' AND ref_cod_curso = '{$this->ref_cod_curso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_curso = '{$this->ref_cod_curso}'");

            return true;
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
        if (is_numeric($this->ref_cod_habilitacao) && is_numeric($this->ref_cod_curso)) {
        }

        return false;
    }
}
