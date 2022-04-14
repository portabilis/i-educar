<?php

use iEducar\Legacy\Model;

class clsPmieducarSituacao extends Model
{
    public $cod_situacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_situacao;
    public $permite_emprestimo;
    public $descricao;
    public $situacao_padrao;
    public $situacao_emprestada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function __construct($cod_situacao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_situacao = null, $permite_emprestimo = null, $descricao = null, $situacao_padrao = null, $situacao_emprestada = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_biblioteca = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}situacao";

        $this->_campos_lista = $this->_todos_campos = 's.cod_situacao, s.ref_usuario_exc, s.ref_usuario_cad, s.nm_situacao, s.permite_emprestimo, s.descricao, s.situacao_padrao, s.situacao_emprestada, s.data_cadastro, s.data_exclusao, s.ativo, s.ref_cod_biblioteca';

        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($cod_situacao)) {
            $this->cod_situacao = $cod_situacao;
        }
        if (is_string($nm_situacao)) {
            $this->nm_situacao = $nm_situacao;
        }
        if (is_numeric($permite_emprestimo)) {
            $this->permite_emprestimo = $permite_emprestimo;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_numeric($situacao_padrao)) {
            $this->situacao_padrao = $situacao_padrao;
        }
        if (is_numeric($situacao_emprestada)) {
            $this->situacao_emprestada = $situacao_emprestada;
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
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_situacao) && is_numeric($this->permite_emprestimo) && is_numeric($this->situacao_padrao) && is_numeric($this->situacao_emprestada) && is_numeric($this->ref_cod_biblioteca)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_situacao)) {
                $nm_situacao = $db->escapeString($this->nm_situacao);
                $campos .= "{$gruda}nm_situacao";
                $valores .= "{$gruda}'{$nm_situacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->permite_emprestimo)) {
                $campos .= "{$gruda}permite_emprestimo";
                $valores .= "{$gruda}'{$this->permite_emprestimo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_padrao)) {
                $campos .= "{$gruda}situacao_padrao";
                $valores .= "{$gruda}'{$this->situacao_padrao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_emprestada)) {
                $campos .= "{$gruda}situacao_emprestada";
                $valores .= "{$gruda}'{$this->situacao_emprestada}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_situacao_seq");
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
        if (is_numeric($this->cod_situacao) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_situacao)) {
                $nm_situacao = $db->escapeString($this->nm_situacao);
                $set .= "{$gruda}nm_situacao = '{$nm_situacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->permite_emprestimo)) {
                $set .= "{$gruda}permite_emprestimo = '{$this->permite_emprestimo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set .= "{$gruda}descricao = '{$descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_padrao)) {
                $set .= "{$gruda}situacao_padrao = '{$this->situacao_padrao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_emprestada)) {
                $set .= "{$gruda}situacao_emprestada = '{$this->situacao_emprestada}'";
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
            if (is_numeric($this->ref_cod_biblioteca)) {
                $set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_situacao = '{$this->cod_situacao}'");

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
    public function lista($int_cod_situacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_situacao = null, $int_permite_emprestimo = null, $str_descricao = null, $int_situacao_padrao = null, $int_situacao_emprestada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista}, b.ref_cod_instituicao, b.ref_cod_escola FROM {$this->_tabela} s, {$this->_schema}biblioteca b";

        $whereAnd = ' AND ';
        $filtros = ' WHERE s.ref_cod_biblioteca = b.cod_biblioteca ';

        if (is_numeric($int_cod_situacao)) {
            $filtros .= "{$whereAnd} s.cod_situacao = '{$int_cod_situacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} s.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} s.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_situacao)) {
            $nm_situacao = $db->escapeString($str_nm_situacao);
            $filtros .= "{$whereAnd} s.nm_situacao LIKE '%{$nm_situacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_permite_emprestimo)) {
            $filtros .= "{$whereAnd} s.permite_emprestimo = '{$int_permite_emprestimo}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} s.descricao LIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_situacao_padrao)) {
            $filtros .= "{$whereAnd} s.situacao_padrao = '{$int_situacao_padrao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_situacao_emprestada)) {
            $filtros .= "{$whereAnd} s.situacao_emprestada = '{$int_situacao_emprestada}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} s.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} s.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} s.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} s.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} s.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} s.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} s.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} b.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} s, {$this->_schema}biblioteca b {$filtros}");

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
        if (is_numeric($this->cod_situacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_situacao = '{$this->cod_situacao}'");
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
        if (is_numeric($this->cod_situacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_situacao = '{$this->cod_situacao}'");
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
        if (is_numeric($this->cod_situacao) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
