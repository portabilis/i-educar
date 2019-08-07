<?php

use iEducar\Legacy\Model;

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarResponsaveisAluno extends Model
{
    public $ref_cod_aluno;
    public $ref_idpes;
    public $vinculo_familiar;

    public function __construct(
        $ref_cod_aluno = null,
        $ref_idpes = null,
        $vinculo_familiar = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}responsaveis_aluno";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_aluno,
                                                       ref_idpes,
                                                       vinculo_familiar';

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }
        if (is_numeric($ref_idpes)) {
            $this->ref_idpes = $ref_idpes;
        }
        if (is_numeric($vinculo_familiar)) {
            $this->vinculo_familiar = $vinculo_familiar;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno) &&
            is_numeric($this->ref_idpes) &&
            is_numeric($this->vinculo_familiar)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}ref_cod_aluno";
            $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
            $gruda = ', ';

            $campos .= "{$gruda}ref_idpes";
            $valores .= "{$gruda}'{$this->ref_idpes}'";
            $gruda = ', ';

            $campos .= "{$gruda}vinculo_familiar";
            $valores .= "{$gruda}'{$this->vinculo_familiar}'";
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
        $sql = "SELECT {$this->_campos_lista},
                       pessoa.nome,
                       fisica.cpf,
                       fisica.tipo_trabalho,
                       fisica.local_trabalho,
                       fisica.horario_inicial_trabalho,
                       fisica.horario_final_trabalho
                  FROM {$this->_tabela}
                 INNER JOIN cadastro.fisica ON (fisica.idpes = responsaveis_aluno.ref_idpes)
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = responsaveis_aluno.ref_idpes)";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($this->ref_cod_aluno)) {
            $filtros .= "{$whereAnd} ref_cod_aluno = {$this->ref_cod_aluno}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($this->ref_idpes)) {
            $filtros .= "{$whereAnd} ref_idpes = {$this->ref_idpes}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($this->vinculo_familiar)) {
            $filtros .= "{$whereAnd} vinculo_familiar = {$this->vinculo_familiar}";
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = {$this->ref_cod_aluno}");

            return true;
        }

        return false;
    }
}
