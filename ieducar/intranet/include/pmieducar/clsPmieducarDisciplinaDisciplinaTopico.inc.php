<?php

use iEducar\Legacy\Model;

class clsPmieducarDisciplinaDisciplinaTopico extends Model
{
    public $ref_ref_cod_disciplina;
    public $ref_ref_ref_cod_escola;
    public $ref_ref_ref_cod_serie;
    public $ref_cod_disciplina_topico;

    public function __construct($ref_ref_cod_disciplina = null, $ref_ref_ref_cod_escola = null, $ref_ref_ref_cod_serie = null, $ref_cod_disciplina_topico = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}disciplina_disciplina_topico";

        $this->_campos_lista = $this->_todos_campos = 'ref_ref_cod_disciplina, ref_ref_ref_cod_escola, ref_ref_ref_cod_serie, ref_cod_disciplina_topico';

        if (is_numeric($ref_cod_disciplina_topico)) {
            $this->ref_cod_disciplina_topico = $ref_cod_disciplina_topico;
        }
        if (is_numeric($ref_ref_ref_cod_serie) && is_numeric($ref_ref_ref_cod_escola) && is_numeric($ref_ref_cod_disciplina)) {
            $this->ref_ref_ref_cod_serie = $ref_ref_ref_cod_serie;
            $this->ref_ref_ref_cod_escola = $ref_ref_ref_cod_escola;
            $this->ref_ref_cod_disciplina = $ref_ref_cod_disciplina;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_ref_cod_disciplina) && is_numeric($this->ref_ref_ref_cod_escola) && is_numeric($this->ref_ref_ref_cod_serie) && is_numeric($this->ref_cod_disciplina_topico)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_ref_cod_escola)) {
                $campos .= "{$gruda}ref_ref_ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_ref_ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_ref_cod_serie)) {
                $campos .= "{$gruda}ref_ref_ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_disciplina_topico)) {
                $campos .= "{$gruda}ref_cod_disciplina_topico";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina_topico}'";
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
        if (is_numeric($this->ref_ref_cod_disciplina) && is_numeric($this->ref_ref_ref_cod_escola) && is_numeric($this->ref_ref_ref_cod_serie) && is_numeric($this->ref_cod_disciplina_topico)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND ref_ref_ref_cod_escola = '{$this->ref_ref_ref_cod_escola}' AND ref_ref_ref_cod_serie = '{$this->ref_ref_ref_cod_serie}' AND ref_cod_disciplina_topico = '{$this->ref_cod_disciplina_topico}'");

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
    public function lista($int_ref_ref_cod_disciplina = null, $int_ref_ref_ref_cod_escola = null, $int_ref_ref_ref_cod_serie = null, $int_ref_cod_disciplina_topico = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_ref_cod_disciplina = '{$int_ref_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_ref_ref_cod_escola = '{$int_ref_ref_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_ref_ref_cod_serie = '{$int_ref_ref_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_disciplina_topico)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina_topico = '{$int_ref_cod_disciplina_topico}'";
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
        if (is_numeric($this->ref_ref_cod_disciplina) && is_numeric($this->ref_ref_ref_cod_escola) && is_numeric($this->ref_ref_ref_cod_serie) && is_numeric($this->ref_cod_disciplina_topico)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND ref_ref_ref_cod_escola = '{$this->ref_ref_ref_cod_escola}' AND ref_ref_ref_cod_serie = '{$this->ref_ref_ref_cod_serie}' AND ref_cod_disciplina_topico = '{$this->ref_cod_disciplina_topico}'");
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
        if (is_numeric($this->ref_ref_cod_disciplina) && is_numeric($this->ref_ref_ref_cod_escola) && is_numeric($this->ref_ref_ref_cod_serie) && is_numeric($this->ref_cod_disciplina_topico)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND ref_ref_ref_cod_escola = '{$this->ref_ref_ref_cod_escola}' AND ref_ref_ref_cod_serie = '{$this->ref_ref_ref_cod_serie}' AND ref_cod_disciplina_topico = '{$this->ref_cod_disciplina_topico}'");
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
        if (is_numeric($this->ref_ref_cod_disciplina) && is_numeric($this->ref_ref_ref_cod_escola) && is_numeric($this->ref_ref_ref_cod_serie) && is_numeric($this->ref_cod_disciplina_topico)) {
        }

        return false;
    }
}
