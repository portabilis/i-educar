<?php

use iEducar\Legacy\Model;

class clsPmieducarDisciplinaSerie extends Model
{
    public $ref_cod_disciplina;
    public $ref_cod_serie;
    public $ativo;

    public function __construct($ref_cod_disciplina = null, $ref_cod_serie = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}disciplina_serie";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_disciplina, ref_cod_serie, ativo';

        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }
        if (is_numeric($ref_cod_disciplina)) {
            $this->ref_cod_disciplina = $ref_cod_disciplina;
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
        if (is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

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
        if (is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_serie = '{$this->ref_cod_serie}'");

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
    public function lista($int_ref_cod_disciplina = null, $int_ref_cod_serie = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
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
        if (is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_serie = '{$this->ref_cod_serie}'");
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
        if (is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_serie = '{$this->ref_cod_serie}'");
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
        if (is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_serie)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Exclui todos registros
     *
     * @return bool
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->_tabela} SET ativo = '0' WHERE ref_cod_serie = '{$this->ref_cod_serie}'");

            return true;
        }

        return false;
    }

    public function retornaQtdDiscMat($int_ref_ref_cod_serie, $int_ref_ref_cod_escola, $int_ref_ref_cod_turma, $int_ref_cod_turma, $int_ref_ref_cod_matricula)
    {
        $db = new clsBanco();
        $sql = "SELECT COUNT( * ) - COALESCE( ( SELECT COUNT( dd.ref_cod_tipo_dispensa )
                                                  FROM pmieducar.dispensa_disciplina dd
                                                 WHERE dd.ref_ref_cod_turma       = {$int_ref_cod_turma}
                                                   AND dd.ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
                                                   AND dd.disc_ref_ref_cod_turma  = {$int_ref_ref_cod_turma}
                                                   AND dd.disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                                                   AND dd.disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola} ), 0)
                  FROM pmieducar.disciplina_serie ds
                 WHERE ds.ref_cod_serie = {$int_ref_ref_cod_serie}";

        return $db->CampoUnico($sql);
    }

    public function desativarDisciplinasSerie($ativo)
    {
        if (is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->_tabela} set ativo = '0' WHERE ref_cod_serie = '{$this->ref_cod_serie}'");

            return true;
        }

        return false;
    }
}
