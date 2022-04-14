<?php

use iEducar\Legacy\Model;

class clsPmieducarMatriculaExcessao extends Model
{
    public $cod_aluno_excessao;
    public $ref_cod_matricula;
    public $ref_cod_turma;
    public $ref_sequencial;
    public $ref_cod_serie;
    public $ref_cod_escola;
    public $ref_cod_disciplina;
    public $reprovado_faltas;
    public $precisa_exame;
    public $permite_exame;

    public function __construct($cod_aluno_excessao = null, $ref_cod_matricula = null, $ref_cod_turma = null, $ref_sequencial = null, $ref_cod_serie = null, $ref_cod_escola = null, $ref_cod_disciplina = null, $reprovado_faltas = null, $precisa_exame = null, $permite_exame = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}matricula_excessao";

        $this->_campos_lista = $this->_todos_campos = 'cod_aluno_excessao, ref_cod_matricula, ref_cod_turma, ref_sequencial, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, reprovado_faltas, precisa_exame, permite_exame';

        if (is_numeric($ref_cod_serie) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_disciplina)) {
            $this->ref_cod_serie = $ref_cod_serie;
            $this->ref_cod_escola = $ref_cod_escola;
            $this->ref_cod_disciplina = $ref_cod_disciplina;
        }
        if (is_numeric($ref_cod_matricula) && is_numeric($ref_cod_turma) && is_numeric($ref_sequencial)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
            $this->ref_cod_turma = $ref_cod_turma;
            $this->ref_sequencial = $ref_sequencial;
        }

        if (is_numeric($cod_aluno_excessao)) {
            $this->cod_aluno_excessao = $cod_aluno_excessao;
        }
        if (!is_null($reprovado_faltas)) {
            $this->reprovado_faltas = $reprovado_faltas;
        }
        if (!is_null($precisa_exame)) {
            $this->precisa_exame = $precisa_exame;
        }
        if (!is_null($permite_exame)) {
            $this->permite_exame = $permite_exame;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) && is_numeric($this->ref_sequencial) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina) && !is_null($this->reprovado_faltas) && !is_null($this->precisa_exame)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_turma)) {
                $campos .= "{$gruda}ref_cod_turma";
                $valores .= "{$gruda}'{$this->ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_sequencial)) {
                $campos .= "{$gruda}ref_sequencial";
                $valores .= "{$gruda}'{$this->ref_sequencial}'";
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
            if (!is_null($this->reprovado_faltas)) {
                $campos .= "{$gruda}reprovado_faltas";
                if (dbBool($this->reprovado_faltas)) {
                    $valores .= "{$gruda}true";
                } else {
                    $valores .= "{$gruda}false";
                }
                $gruda = ', ';
            }
            if (!is_null($this->precisa_exame)) {
                $campos .= "{$gruda}precisa_exame";
                if (dbBool($this->precisa_exame)) {
                    $valores .= "{$gruda}true";
                } else {
                    $valores .= "{$gruda}false";
                }
                $gruda = ', ';
            }
            if (!is_null($this->permite_exame)) {
                $campos .= "{$gruda}permite_exame";
                if (dbBool($this->permite_exame)) {
                    $valores .= "{$gruda}true";
                } else {
                    $valores .= "{$gruda}false";
                }
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_aluno_excessao_seq");
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
        if (is_numeric($this->cod_aluno_excessao)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $set .= "{$gruda}ref_cod_matricula = '{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_turma)) {
                $set .= "{$gruda}ref_cod_turma = '{$this->ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_sequencial)) {
                $set .= "{$gruda}ref_sequencial = '{$this->ref_sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_disciplina)) {
                $set .= "{$gruda}ref_cod_disciplina = '{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (!is_null($this->reprovado_faltas)) {
                $val = dbBool($this->reprovado_faltas) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}reprovado_faltas = {$val}";
                $gruda = ', ';
            }
            if (!is_null($this->precisa_exame)) {
                $val = dbBool($this->precisa_exame) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}precisa_exame = {$val}";
                $gruda = ', ';
            }
            if (!is_null($this->permite_exame)) {
                $val = dbBool($this->permite_exame) ? 'TRUE' : 'FALSE';
                $set .= "{$gruda}permite_exame = {$val}";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_ref_cod_matricula
     * @param integer int_ref_cod_turma
     * @param integer int_ref_sequencial
     * @param integer int_ref_cod_serie
     * @param integer int_ref_cod_escola
     * @param integer int_ref_cod_disciplina
     * @param bool bool_reprovado_faltas
     * @param bool bool_precisa_exame
     * @param bool bool_permite_exame
     *
     * @return array
     */
    public function lista($int_ref_cod_matricula = null, $int_ref_cod_turma = null, $int_ref_sequencial = null, $int_ref_cod_serie = null, $int_ref_cod_escola = null, $int_ref_cod_disciplina = null, $bool_reprovado_faltas = null, $bool_precisa_exame = null, $bool_permite_exame = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_aluno_excessao)) {
            $filtros .= "{$whereAnd} cod_aluno_excessao = '{$int_cod_aluno_excessao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} ref_cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_sequencial)) {
            $filtros .= "{$whereAnd} ref_sequencial = '{$int_ref_sequencial}'";
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
        if (!is_null($bool_reprovado_faltas)) {
            if (dbBool($bool_reprovado_faltas)) {
                $filtros .= "{$whereAnd} reprovado_faltas = TRUE";
            } else {
                $filtros .= "{$whereAnd} reprovado_faltas = FALSE";
            }
            $whereAnd = ' AND ';
        }
        if (!is_null($bool_precisa_exame)) {
            if (dbBool($bool_precisa_exame)) {
                $filtros .= "{$whereAnd} precisa_exame = TRUE";
            } else {
                $filtros .= "{$whereAnd} precisa_exame = FALSE";
            }
            $whereAnd = ' AND ';
        }
        if (!is_null($bool_permite_exame)) {
            if ($bool_permite_exame == '!-! is null !-!') {
                $filtros .= "{$whereAnd} permite_exame IS NULL";
            } elseif ($bool_permite_exame == '!-! is not null !-!') {
                $filtros .= "{$whereAnd} permite_exame IS NOT NULL";
            } elseif (dbBool($bool_permite_exame)) {
                $filtros .= "{$whereAnd} permite_exame = TRUE";
            } else {
                $filtros .= "{$whereAnd} permite_exame = FALSE";
            }
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
        if (is_numeric($this->cod_aluno_excessao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna true se o registro existir. Caso contrário retorna false.
     *
     * @return bool
     */
    public function existe()
    {
        if (is_numeric($this->cod_aluno_excessao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'");
            if ($db->ProximoRegistro()) {
                return true;
            }
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
        if (is_numeric($this->cod_aluno_excessao)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'");

            return true;
        }

        return false;
    }

    /**
     * Exclui todos os registros associados a matricula $cod_matricula
     *
     * @return bool
     */
    public function excluirPorMatricula($cod_matricula)
    {
        if (is_numeric($cod_matricula)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$cod_matricula}'");

            return true;
        }

        return false;
    }
}
