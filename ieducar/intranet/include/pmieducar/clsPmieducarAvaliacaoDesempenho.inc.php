<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarAvaliacaoDesempenho extends Model
{
    public $sequencial;
    public $ref_cod_servidor;
    public $ref_ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $titulo_avaliacao;

    public function __construct($sequencial = null, $ref_cod_servidor = null, $ref_ref_cod_instituicao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $descricao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $titulo_avaliacao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}avaliacao_desempenho";

        $this->_campos_lista = $this->_todos_campos = 'sequencial, ref_cod_servidor, ref_ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, descricao, data_cadastro, data_exclusao, ativo, titulo_avaliacao';

        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
                    $this->ref_cod_servidor = $ref_cod_servidor;
                    $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
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
        if (is_string($titulo_avaliacao)) {
            $this->titulo_avaliacao = $titulo_avaliacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) && is_string($this->descricao) && is_string($this->titulo_avaliacao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_servidor)) {
                $campos .= "{$gruda}ref_cod_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_string($this->titulo_avaliacao)) {
                $campos .= "{$gruda}titulo_avaliacao";
                $valores .= "{$gruda}'{$this->titulo_avaliacao}'";
                $gruda = ', ';
            }

            $sequencial = $db->campoUnico("SELECT COALESCE( MAX(sequencial), 0 ) + 1 FROM {$this->_tabela} WHERE ref_cod_servidor = {$this->ref_cod_servidor} AND ref_ref_cod_instituicao = {$this->ref_ref_cod_instituicao}");

            $db->Consulta("INSERT INTO {$this->_tabela} ( sequencial, $campos ) VALUES( $sequencial, $valores )");

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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
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
            if (is_string($this->titulo_avaliacao)) {
                $set .= "{$gruda}titulo_avaliacao = '{$this->titulo_avaliacao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE sequencial = '{$this->sequencial}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'");

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
    public function lista($int_sequencial = null, $int_ref_cod_servidor = null, $int_ref_ref_cod_instituicao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_descricao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_titulo_avaliacao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
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
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
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
        if (is_string($str_titulo_avaliacao)) {
            $filtros .= "{$whereAnd} titulo_avaliacao LIKE '%{$str_titulo_avaliacao}%'";
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'");
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'");
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
        if (is_numeric($this->sequencial) && is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
