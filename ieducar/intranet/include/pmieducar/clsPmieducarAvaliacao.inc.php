<?php

use iEducar\Legacy\Model;

class clsPmieducarAvaliacao extends Model
{
    public $cod_avaliacao;
    public $disc_ref_ref_cod_serie;
    public $disc_ref_ref_cod_escola;
    public $disc_ref_ref_cod_disciplina;
    public $disc_ref_ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $titulo;
    public $descricao;
    public $aplicada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($cod_avaliacao = null, $disc_ref_ref_cod_serie = null, $disc_ref_ref_cod_escola = null, $disc_ref_ref_cod_disciplina = null, $disc_ref_ref_cod_turma = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $titulo = null, $descricao = null, $aplicada = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}avaliacao";

        $this->_campos_lista = $this->_todos_campos = 'cod_avaliacao, disc_ref_ref_cod_serie, disc_ref_ref_cod_escola, disc_ref_ref_cod_disciplina, disc_ref_ref_cod_turma, ref_usuario_exc, ref_usuario_cad, titulo, descricao, aplicada, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($disc_ref_ref_cod_turma) && is_numeric($disc_ref_ref_cod_disciplina) && is_numeric($disc_ref_ref_cod_escola) && is_numeric($disc_ref_ref_cod_serie)) {
            $this->disc_ref_ref_cod_turma = $disc_ref_ref_cod_turma;
            $this->disc_ref_ref_cod_disciplina = $disc_ref_ref_cod_disciplina;
            $this->disc_ref_ref_cod_escola = $disc_ref_ref_cod_escola;
            $this->disc_ref_ref_cod_serie = $disc_ref_ref_cod_serie;
        }

        if (is_numeric($cod_avaliacao)) {
            $this->cod_avaliacao = $cod_avaliacao;
        }
        if (is_string($titulo)) {
            $this->titulo = $titulo;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_numeric($aplicada)) {
            $this->aplicada = $aplicada;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
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
        if (is_numeric($this->disc_ref_ref_cod_serie) && is_numeric($this->disc_ref_ref_cod_escola) && is_numeric($this->disc_ref_ref_cod_disciplina) && is_numeric($this->disc_ref_ref_cod_turma) && is_numeric($this->ref_usuario_cad) && is_string($this->titulo) && is_numeric($this->aplicada)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->disc_ref_ref_cod_serie)) {
                $campos .= "{$gruda}disc_ref_ref_cod_serie";
                $valores .= "{$gruda}'{$this->disc_ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->disc_ref_ref_cod_escola)) {
                $campos .= "{$gruda}disc_ref_ref_cod_escola";
                $valores .= "{$gruda}'{$this->disc_ref_ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->disc_ref_ref_cod_disciplina)) {
                $campos .= "{$gruda}disc_ref_ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->disc_ref_ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->disc_ref_ref_cod_turma)) {
                $campos .= "{$gruda}disc_ref_ref_cod_turma";
                $valores .= "{$gruda}'{$this->disc_ref_ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->titulo)) {
                $campos .= "{$gruda}titulo";
                $valores .= "{$gruda}'{$this->titulo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->aplicada)) {
                $campos .= "{$gruda}aplicada";
                $valores .= "{$gruda}'{$this->aplicada}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_avaliacao_seq");
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
        if (is_numeric($this->cod_avaliacao) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->disc_ref_ref_cod_serie)) {
                $set .= "{$gruda}disc_ref_ref_cod_serie = '{$this->disc_ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->disc_ref_ref_cod_escola)) {
                $set .= "{$gruda}disc_ref_ref_cod_escola = '{$this->disc_ref_ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->disc_ref_ref_cod_disciplina)) {
                $set .= "{$gruda}disc_ref_ref_cod_disciplina = '{$this->disc_ref_ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->disc_ref_ref_cod_turma)) {
                $set .= "{$gruda}disc_ref_ref_cod_turma = '{$this->disc_ref_ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->titulo)) {
                $set .= "{$gruda}titulo = '{$this->titulo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->aplicada)) {
                $set .= "{$gruda}aplicada = '{$this->aplicada}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_avaliacao = '{$this->cod_avaliacao}'");

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
    public function lista($int_cod_avaliacao = null, $int_disc_ref_ref_cod_serie = null, $int_disc_ref_ref_cod_escola = null, $int_disc_ref_ref_cod_disciplina = null, $int_disc_ref_ref_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_titulo = null, $str_descricao = null, $int_aplicada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_avaliacao)) {
            $filtros .= "{$whereAnd} cod_avaliacao = '{$int_cod_avaliacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_disc_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd} disc_ref_ref_cod_serie = '{$int_disc_ref_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_disc_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} disc_ref_ref_cod_escola = '{$int_disc_ref_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_disc_ref_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} disc_ref_ref_cod_disciplina = '{$int_disc_ref_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_disc_ref_ref_cod_turma)) {
            $filtros .= "{$whereAnd} disc_ref_ref_cod_turma = '{$int_disc_ref_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_titulo)) {
            $filtros .= "{$whereAnd} titulo LIKE '%{$str_titulo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_aplicada)) {
            $filtros .= "{$whereAnd} aplicada = '{$int_aplicada}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
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
        if (is_numeric($this->cod_avaliacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_avaliacao = '{$this->cod_avaliacao}'");
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
        if (is_numeric($this->cod_avaliacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_avaliacao = '{$this->cod_avaliacao}'");
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
        if (is_numeric($this->cod_avaliacao) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
