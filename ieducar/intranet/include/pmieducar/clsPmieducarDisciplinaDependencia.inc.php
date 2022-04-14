<?php

use iEducar\Legacy\Model;

class clsPmieducarDisciplinaDependencia extends Model
{
    public $ref_cod_matricula;
    public $ref_cod_serie;
    public $ref_cod_escola;
    public $ref_cod_disciplina;
    public $observacao;
    public $cod_disciplina_dependencia;

    public function __construct(
        $ref_cod_matricula = null,
        $ref_cod_serie = null,
        $ref_cod_escola = null,
        $ref_cod_disciplina = null,
        $observacao = null,
        $cod_disciplina_dependencia = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'disciplina_dependencia';

        $this->_campos_lista = $this->_todos_campos = ' cod_disciplina_dependencia,ref_cod_matricula, ref_cod_serie,
        ref_cod_escola, ref_cod_disciplina, observacao';

        if (is_numeric($ref_cod_matricula)) {
            $matricula = new clsPmieducarMatricula($ref_cod_matricula);
            if ($matricula->existe()) {
                $this->ref_cod_matricula = $ref_cod_matricula;
            }
        }

        if (is_numeric($ref_cod_disciplina) && is_numeric($ref_cod_escola) &&
            is_numeric($ref_cod_serie)
        ) {
            $anoEscolarMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
            $componenteAnos = $anoEscolarMapper->findAll([], [
                'componenteCurricular' => $ref_cod_disciplina,
                'anoEscolar' => $ref_cod_serie
            ]);

            if (1 == count($componenteAnos)) {
                $this->ref_cod_disciplina = $ref_cod_disciplina;
                $this->ref_cod_serie = $ref_cod_serie;
                $this->ref_cod_escola = $ref_cod_escola;
            }
        }

        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }

        if (is_numeric($cod_disciplina_dependencia)) {
            $this->cod_disciplina_dependencia = $cod_disciplina_dependencia;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->cod_disciplina_dependencia)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$this->observacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cod_disciplina_dependencia)) {
                $campos .= "{$gruda}cod_disciplina_dependencia";
                $valores .= "{$gruda}'{$this->cod_disciplina_dependencia}'";
                $gruda = ', ';
            }

            $sql = "INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)";
            $db->Consulta($sql);

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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->observacao)) {
                $set .= "{$gruda}observacao = '{$this->observacao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");

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
        $int_ref_cod_matricula = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_disciplina = null,
        $str_observacao = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_observacao)) {
            $filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
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
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina)
        ) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");

            return true;
        }

        return false;
    }
}
