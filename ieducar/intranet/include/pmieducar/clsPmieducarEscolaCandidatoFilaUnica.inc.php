<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarEscolaCandidatoFilaUnica extends Model
{
    public $ref_cod_candidato_fila_unica;
    public $ref_cod_escola;
    public $sequencial;

    public function __construct(
        $ref_cod_candidato_fila_unica = null,
        $ref_cod_escola = null,
        $sequencial = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}escola_candidato_fila_unica";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_candidato_fila_unica,
                                                       ref_cod_escola,
                                                       sequencial';

        if (is_numeric($ref_cod_candidato_fila_unica)) {
            $this->ref_cod_candidato_fila_unica = $ref_cod_candidato_fila_unica;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_candidato_fila_unica) &&
            is_numeric($this->ref_cod_escola) &&
            is_numeric($this->sequencial)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}ref_cod_candidato_fila_unica";
            $valores .= "{$gruda}'{$this->ref_cod_candidato_fila_unica}'";
            $gruda = ', ';

            $campos .= "{$gruda}ref_cod_escola";
            $valores .= "{$gruda}'{$this->ref_cod_escola}'";
            $gruda = ', ';

            $campos .= "{$gruda}sequencial";
            $valores .= "{$gruda}'{$this->sequencial}'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");

            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista()
    {
        $sql = "
            SELECT
                {$this->_campos_lista},
                pessoa.nome AS nm_escola
            FROM
                {$this->_tabela}
            INNER JOIN pmieducar.escola ON escola.cod_escola = escola_candidato_fila_unica.ref_cod_escola
            INNER JOIN cadastro.pessoa ON pessoa.idpes = escola.ref_idpes
        ";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($this->ref_cod_candidato_fila_unica)) {
            $filtros .= "{$whereAnd} ref_cod_candidato_fila_unica = {$this->ref_cod_candidato_fila_unica}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($this->ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = {$this->ref_cod_escola}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($this->sequencial)) {
            $filtros .= "{$whereAnd} sequencial = {$this->sequencial}";
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
     * Exclui todos os registros referentes a uma turma
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_candidato_fila_unica)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_candidato_fila_unica = {$this->ref_cod_candidato_fila_unica}");

            return true;
        }

        return false;
    }
}
