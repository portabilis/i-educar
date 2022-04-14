<?php

use iEducar\Legacy\Model;

class clsPmieducarTurmaDisciplina extends Model
{
    public $ref_cod_turma;
    public $ref_cod_disciplina;
    public $ref_cod_escola;
    public $ref_cod_serie;

    public function __construct($ref_cod_turma = null, $ref_cod_disciplina = null, $ref_cod_escola = null, $ref_cod_serie = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}turma_disciplina";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_turma, ref_cod_disciplina, ref_cod_escola, ref_cod_serie';

        if (is_numeric($ref_cod_serie) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_disciplina)) {
            $this->ref_cod_serie = $ref_cod_serie;
            $this->ref_cod_escola = $ref_cod_escola;
            $this->ref_cod_disciplina = $ref_cod_disciplina;
        }
        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_turma)) {
                $campos .= "{$gruda}ref_cod_turma";
                $valores .= "{$gruda}'{$this->ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
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
        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'");

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
    public function lista($int_ref_cod_turma = null, $int_ref_cod_disciplina = null, $int_ref_cod_escola = null, $int_ref_cod_serie = null, $str_disciplina_not_in = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} ref_cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_disciplina_not_in)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina not in ($str_disciplina_not_in)";
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
        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'");
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
        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'");
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
        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos($ref_cod_turma = null, $ref_cod_escola = null, $ref_cod_serie = null)
    {
        if (is_numeric($ref_cod_turma) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_turma = '{$ref_cod_turma}' AND ref_cod_escola = '{$ref_cod_escola}' AND ref_cod_serie = '{$ref_cod_serie}'");

            return true;
        } elseif (is_numeric($ref_cod_escola) && is_numeric($ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_escola = '{$ref_cod_escola}' AND ref_cod_serie = '{$ref_cod_serie}'");

            return true;
        }

        return false;
    }

    public function diferente($disciplinas)
    {
        if (is_array($disciplinas) && is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_escola)) {
            $disciplina_in = '';
            $conc = '';
            foreach ($disciplinas as $disciplina) {
                for ($i = 0; $i < sizeof($disciplina); $i++) {
                    $disciplina_in .= "{$conc}{$disciplina[$i]}";
                    $conc = ',';
                }
            }

            $db = new clsBanco();
            $db->Consulta("SELECT ref_cod_disciplina FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_disciplina not in ({$disciplina_in})");

            $resultado = [];

            while ($db->ProximoRegistro()) {
                $resultado[] = $db->Tupla();
            }

            return $resultado;
        }

        return false;
    }

    public function jah_existe()
    {
        if (is_array($this->ref_cod_disciplina) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_escola)) {
            $db = new clsBanco();
            $db->Consulta("SELECT ref_cod_turma FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_cod_serie = '{$this->ref_cod_serie}' AND ref_cod_escola = '{$this->ref_cod_escola}'");

            $resultado = [];

            while ($db->ProximoRegistro()) {
                $resultado[] = $db->Tupla();
            }

            return $resultado;
        }

        return false;
    }

    public function eh_usado($disciplina)
    {
        if (is_numeric($disciplina) && is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_escola)) {
            $db = new clsBanco();
            $resultado = $db->CampoUnico("SELECT 1
                            FROM pmieducar.dispensa_disciplina dd
                            WHERE
                                    dd.disc_ref_ref_cod_disciplina = {$disciplina}
                                AND dd.disc_ref_ref_cod_turma = {$this->ref_cod_turma}
                                AND dd.disc_ref_ref_cod_serie = {$this->ref_cod_serie}
                                AND dd.disc_ref_ref_cod_escola = {$this->ref_cod_escola}

                            UNION

                            SELECT 1
                            FROM pmieducar.nota_aluno na
                            WHERE
                                    na.disc_ref_ref_cod_disciplina = {$disciplina}
                                AND na.disc_ref_cod_turma = {$this->ref_cod_turma}
                                AND na.disc_ref_ref_cod_serie = {$this->ref_cod_serie}
                                AND na.disc_ref_ref_cod_escola = {$this->ref_cod_escola}

                            UNION

                            SELECT 1
                            FROM pmieducar.falta_aluno fa
                            WHERE
                                    fa.disc_ref_ref_cod_disciplina = {$disciplina}
                                AND fa.disc_ref_ref_cod_turma = {$this->ref_cod_turma}
                                AND fa.ref_ref_cod_turma = {$this->ref_cod_turma}
                                AND fa.disc_ref_ref_cod_serie = {$this->ref_cod_serie}
                                AND fa.disc_ref_ref_cod_escola = {$this->ref_cod_escola}

                            UNION

                            SELECT 1
                            FROM pmieducar.quadro_horario_horarios qhh
                            WHERE
                                    qhh.ref_ref_cod_disciplina = {$disciplina}
                                AND qhh.ref_ref_cod_turma = {$this->ref_cod_turma}
                                AND qhh.ref_ref_cod_serie = {$this->ref_cod_serie}
                                AND qhh.ref_ref_cod_escola = {$this->ref_cod_escola}");

            return $resultado;
        }

        return false;
    }
}
