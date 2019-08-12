<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

class clsPmieducarServidorDisciplina extends Model
{
    public $ref_cod_disciplina;
    public $ref_ref_cod_instituicao;
    public $ref_cod_servidor;
    public $ref_cod_curso;

    public function __construct(
        $ref_cod_disciplina = null,
        $ref_ref_cod_instituicao = null,
        $ref_cod_servidor = null,
        $ref_cod_curso = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'servidor_disciplina';

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_curso';

        if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
            $servidor = new clsPmieducarServidor(
                $ref_cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                $ref_ref_cod_instituicao
            );

            if ($servidor->existe()) {
                $this->ref_cod_servidor = $ref_cod_servidor;
                $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
            }
        }

        if (is_numeric($ref_cod_disciplina)) {
            $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
            try {
                $componenteMapper->find($ref_cod_disciplina);
                $this->ref_cod_disciplina = $ref_cod_disciplina;
            } catch (Exception $e) {
            }
        }

        if (is_numeric($ref_cod_curso)) {
            $curso = new clsPmieducarCurso($ref_cod_curso);

            if ($curso->existe()) {
                $this->ref_cod_curso = $ref_cod_curso;
            }
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            return true;
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
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_curso = '{$this->ref_cod_curso}'");

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
    public function lista(
        $int_ref_cod_disciplina = null,
        $int_ref_ref_cod_instituicao = null,
        $int_ref_cod_servidor = null,
        $int_ref_cod_curso = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'");
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
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_curso = '{$this->ref_cod_curso}'");
            if ($db->ProximoRegistro()) {
                return true;
            }
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
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
        }

        return false;
    }

    /**
     * Exclui todos os registros de disciplinas de um servidor.
     *
     * @return bool
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'");

            return true;
        }

        return false;
    }
}
