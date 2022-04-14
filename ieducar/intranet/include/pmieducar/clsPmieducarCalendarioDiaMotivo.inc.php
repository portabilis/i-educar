<?php

use iEducar\Legacy\Model;

class clsPmieducarCalendarioDiaMotivo extends Model
{
    public $cod_calendario_dia_motivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $sigla;
    public $descricao;
    public $tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_motivo;
    public $codUsuario;

    public function __construct($cod_calendario_dia_motivo = null, $ref_cod_escola = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $sigla = null, $descricao = null, $tipo = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $nm_motivo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}calendario_dia_motivo";

        $this->_campos_lista = $this->_todos_campos = 'cdm.cod_calendario_dia_motivo, cdm.ref_cod_escola, cdm.ref_usuario_exc, cdm.ref_usuario_cad, cdm.sigla, cdm.descricao, cdm.tipo, cdm.data_cadastro, cdm.data_exclusao, cdm.ativo, cdm.nm_motivo';

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_calendario_dia_motivo)) {
            $this->cod_calendario_dia_motivo = $cod_calendario_dia_motivo;
        }
        if (is_string($sigla)) {
            $this->sigla = $sigla;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_string($tipo)) {
            $this->tipo = $tipo;
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
        if (is_string($nm_motivo)) {
            $this->nm_motivo = $nm_motivo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_usuario_cad) && is_string($this->sigla) && is_string($this->tipo) && is_string($this->nm_motivo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->sigla)) {
                $sigla = $db->escapeString($this->sigla);
                $campos .= "{$gruda}sigla";
                $valores .= "{$gruda}'{$sigla}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$descricao}'";
                $gruda = ', ';
            }
            if (is_string($this->tipo)) {
                $campos .= "{$gruda}tipo";
                $valores .= "{$gruda}'{$this->tipo}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_string($this->nm_motivo)) {
                $motivo = $db->escapeString($this->nm_motivo);
                $campos .= "{$gruda}nm_motivo";
                $valores .= "{$gruda}'{$motivo}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_calendario_dia_motivo_seq");
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
        if (is_numeric($this->cod_calendario_dia_motivo) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
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
            if (is_string($this->sigla)) {
                $sigla = $db->escapeString($this->sigla);
                $set .= "{$gruda}sigla = '{$sigla}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set .= "{$gruda}descricao = '{$descricao}'";
                $gruda = ', ';
            }
            if (is_string($this->tipo)) {
                $set .= "{$gruda}tipo = '{$this->tipo}'";
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
            if (is_string($this->nm_motivo)) {
                $motivo = $db->escapeString($this->nm_motivo);
                $set .= "{$gruda}nm_motivo = '{$motivo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_calendario_dia_motivo = '{$this->cod_calendario_dia_motivo}'");

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
    public function lista($int_cod_calendario_dia_motivo = null, $int_ref_cod_escola = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_sigla = null, $str_descricao = null, $str_tipo = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nm_motivo = null, $int_ref_cod_instituicao = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista}, e.ref_cod_instituicao FROM {$this->_tabela } cdm, {$this->_schema}escola e";

        $filtros = ' WHERE cdm.ref_cod_escola = e.cod_escola';
        $whereAnd = ' AND ';

        if (is_numeric($int_cod_calendario_dia_motivo)) {
            $filtros .= "{$whereAnd} cdm.cod_calendario_dia_motivo = '{$int_cod_calendario_dia_motivo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} cdm.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                              WHERE escola_usuario.ref_cod_escola = cdm.ref_cod_escola
                                                AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} cdm.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} cdm.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sigla)) {
            $filtros .= "{$whereAnd} cdm.sigla LIKE '%{$str_sigla}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} cdm.descricao LIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_tipo)) {
            $filtros .= "{$whereAnd} cdm.tipo LIKE '%{$str_tipo}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} cdm.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} cdm.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} cdm.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} cdm.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} cdm.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} cdm.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_motivo)) {
            $str_nome_motivo = $db->escapeString($str_nm_motivo);
            $filtros .= "{$whereAnd} cdm.nm_motivo LIKE '%{$str_nome_motivo}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} e.ref_cod_instituicao = '$int_ref_cod_instituicao'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} cdm, {$this->_schema}escola e {$filtros}");

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
        if (is_numeric($this->cod_calendario_dia_motivo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} cdm WHERE cdm.cod_calendario_dia_motivo = '{$this->cod_calendario_dia_motivo}'");
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
        if (is_numeric($this->cod_calendario_dia_motivo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_calendario_dia_motivo = '{$this->cod_calendario_dia_motivo}'");
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
        if (is_numeric($this->cod_calendario_dia_motivo) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
