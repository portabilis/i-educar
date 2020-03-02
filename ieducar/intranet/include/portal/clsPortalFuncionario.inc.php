<?php

use iEducar\Legacy\Model;

require_once 'include/portal/geral.inc.php';

class clsPortalFuncionario extends Model
{
    public $ref_cod_pessoa_fj;
    public $matricula;
    public $senha;
    public $ativo;
    public $ref_sec;
    public $sequencial;
    public $opcao_menu;
    public $ref_cod_administracao_secretaria;
    public $ref_ref_cod_administracao_secretaria;
    public $ref_cod_departamento;
    public $ref_ref_ref_cod_administracao_secretaria;
    public $ref_ref_cod_departamento;
    public $ref_cod_setor;
    public $ref_cod_funcionario_vinculo;
    public $tempo_expira_senha;
    public $data_expiracao = false;
    public $data_troca_senha;
    public $data_reativa_conta;
    public $ref_ref_cod_pessoa_fj;
    public $ref_cod_setor_new;
    public $matricula_new;
    public $matricula_interna;
    public $tipo_menu;
    public $receber_novidades;
    public $atualizou_cadastro;

    public function __construct($ref_cod_pessoa_fj = null, $matricula = null, $senha = null, $ativo = null, $ref_sec = null, $ramal = null, $sequencial = null, $opcao_menu = null, $ref_cod_administracao_secretaria = null, $ref_ref_cod_administracao_secretaria = null, $ref_cod_departamento = null, $ref_ref_ref_cod_administracao_secretaria = null, $ref_ref_cod_departamento = null, $ref_cod_setor = null, $ref_cod_funcionario_vinculo = null, $tempo_expira_senha = null, $data_expiracao = null, $data_troca_senha = null, $data_reativa_conta = null, $ref_ref_cod_pessoa_fj = null, $proibido = null, $ref_cod_setor_new = null, $matricula_new = null, $matricula_permanente = null, $tipo_menu = null, $email = null, $matricula_interna = null)
    {
        $db = new clsBanco();
        $this->_schema = 'portal.';
        $this->_tabela = "{$this->_schema}funcionario";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_pessoa_fj, matricula, matricula_interna, senha, ativo, ref_sec, sequencial, opcao_menu, ref_cod_setor, ref_cod_funcionario_vinculo, tempo_expira_senha, data_expiracao, data_troca_senha, data_reativa_conta, ref_ref_cod_pessoa_fj, ref_cod_setor_new, matricula_new, tipo_menu, email, receber_novidades, atualizou_cadastro';

        if (is_numeric($ref_ref_cod_pessoa_fj)) {
            if ($db->CampoUnico("SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_ref_cod_pessoa_fj}'")) {
                $this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
            }
        }
        if (is_numeric($ref_ref_cod_departamento) && is_numeric($ref_ref_ref_cod_administracao_secretaria) && is_numeric($ref_cod_setor)) {
            if ($db->CampoUnico("SELECT 1 FROM administracao_setor WHERE ref_cod_departamento = '{$ref_ref_cod_departamento}' AND ref_ref_cod_administracao_secretaria = '{$ref_ref_ref_cod_administracao_secretaria}' AND cod_setor = '{$ref_cod_setor}'")) {
                $this->ref_ref_cod_departamento = $ref_ref_cod_departamento;
                $this->ref_ref_ref_cod_administracao_secretaria = $ref_ref_ref_cod_administracao_secretaria;
                $this->ref_cod_setor = $ref_cod_setor;
            }
        }
        if (is_numeric($ref_ref_cod_administracao_secretaria) && is_numeric($ref_cod_departamento)) {
            if ($db->CampoUnico("SELECT 1 FROM administracao_departamento WHERE ref_cod_administracao_secretaria = '{$ref_ref_cod_administracao_secretaria}' AND cod_departamento = '{$ref_cod_departamento}'")) {
                $this->ref_ref_cod_administracao_secretaria = $ref_ref_cod_administracao_secretaria;
                $this->ref_cod_departamento = $ref_cod_departamento;
            }
        }
        if (is_numeric($ref_cod_administracao_secretaria)) {
            if ($db->CampoUnico("SELECT 1 FROM administracao_secretaria WHERE cod_administracao_secretaria = '{$ref_cod_administracao_secretaria}'")) {
                $this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
            }
        }
        if (is_numeric($ref_cod_pessoa_fj)) {
            if ($db->CampoUnico("SELECT 1 FROM cadastro.fisica WHERE idpes = '{$ref_cod_pessoa_fj}'")) {
                $this->ref_cod_pessoa_fj = $ref_cod_pessoa_fj;
            }
        }

        if (is_string($matricula)) {
            $this->matricula = $matricula;
        }
        if (is_string($senha)) {
            $this->senha = $senha;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
        if (is_numeric($ref_sec)) {
            $this->ref_sec = $ref_sec;
        }
        if (is_string($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($opcao_menu)) {
            $this->opcao_menu = $opcao_menu;
        }
        if (is_numeric($ref_cod_funcionario_vinculo)) {
            $this->ref_cod_funcionario_vinculo = $ref_cod_funcionario_vinculo;
        }
        if (is_numeric($tempo_expira_senha)) {
            $this->tempo_expira_senha = $tempo_expira_senha;
        }

        if ($data_expiracao) {
            $this->data_expiracao = $data_expiracao;
        } elseif ($data_expiracao !== false) {
            $this->data_expiracao = null;
        }

        if (is_string($data_troca_senha)) {
            $this->data_troca_senha = $data_troca_senha;
        }
        if (is_string($data_reativa_conta)) {
            $this->data_reativa_conta = $data_reativa_conta;
        }
        if (is_numeric($matricula_new)) {
            $this->matricula_new = $matricula_new;
        }
        if (is_numeric($tipo_menu)) {
            $this->tipo_menu = $tipo_menu;
        }

        if (is_string($email)) {
            $this->email = $email;
        }

        if (is_string($matricula_interna)) {
            $this->matricula_interna = $matricula_interna;
        }
    }

    public function cadastra()
    {
        if (is_numeric($this->ref_cod_pessoa_fj)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_pessoa_fj)) {
                $campos .= "{$gruda}ref_cod_pessoa_fj";
                $valores .= "{$gruda}'{$this->ref_cod_pessoa_fj}'";
                $gruda = ', ';
            }
            if (is_string($this->matricula)) {
                $campos .= "{$gruda}matricula";
                $valores .= "{$gruda}'{$this->matricula}'";
                $gruda = ', ';
            }
            if (is_string($this->matricula_interna)) {
                $campos .= "{$gruda}matricula_interna";
                $valores .= "{$gruda}'{$this->matricula_interna}'";
                $gruda = ', ';
            }
            if (is_string($this->senha)) {
                $campos .= "{$gruda}senha";
                $valores .= "{$gruda}'{$this->senha}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->ref_sec)) {
                $campos .= "{$gruda}ref_sec";
                $valores .= "{$gruda}'{$this->ref_sec}'";
                $gruda = ', ';
            }
            if (is_string($this->sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_string($this->opcao_menu)) {
                $campos .= "{$gruda}opcao_menu";
                $valores .= "{$gruda}'{$this->opcao_menu}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_administracao_secretaria)) {
                $campos .= "{$gruda}ref_cod_administracao_secretaria";
                $valores .= "{$gruda}'{$this->ref_cod_administracao_secretaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_administracao_secretaria)) {
                $campos .= "{$gruda}ref_ref_cod_administracao_secretaria";
                $valores .= "{$gruda}'{$this->ref_ref_cod_administracao_secretaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_departamento)) {
                $campos .= "{$gruda}ref_cod_departamento";
                $valores .= "{$gruda}'{$this->ref_cod_departamento}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_ref_cod_administracao_secretaria)) {
                $campos .= "{$gruda}ref_ref_ref_cod_administracao_secretaria";
                $valores .= "{$gruda}'{$this->ref_ref_ref_cod_administracao_secretaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_departamento)) {
                $campos .= "{$gruda}ref_ref_cod_departamento";
                $valores .= "{$gruda}'{$this->ref_ref_cod_departamento}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_setor)) {
                $campos .= "{$gruda}ref_cod_setor";
                $valores .= "{$gruda}'{$this->ref_cod_setor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_funcionario_vinculo)) {
                $campos .= "{$gruda}ref_cod_funcionario_vinculo";
                $valores .= "{$gruda}'{$this->ref_cod_funcionario_vinculo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tempo_expira_senha)) {
                $campos .= "{$gruda}tempo_expira_senha";
                $valores .= "{$gruda}'{$this->tempo_expira_senha}'";
                $gruda = ', ';
            }
            if ($this->data_expiracao) {
                $campos .= "{$gruda}data_expiracao";
                $valores .= "{$gruda}'{$this->data_expiracao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_troca_senha)) {
                $campos .= "{$gruda}data_troca_senha";
                $valores .= "{$gruda}{$this->data_troca_senha}";
                $gruda = ', ';
            }
            if (is_string($this->data_reativa_conta)) {
                $campos .= "{$gruda}data_reativa_conta";
                $valores .= "{$gruda}{$this->data_reativa_conta}";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_pessoa_fj)) {
                $campos .= "{$gruda}ref_ref_cod_pessoa_fj";
                $valores .= "{$gruda}'{$this->ref_ref_cod_pessoa_fj}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_setor_new)) {
                $campos .= "{$gruda}ref_cod_setor_new";
                $valores .= "{$gruda}'{$this->ref_cod_setor_new}'";
                $gruda = ', ';
            }
            if (is_numeric($this->matricula_new)) {
                $campos .= "{$gruda}matricula_new";
                $valores .= "{$gruda}'{$this->matricula_new}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tipo_menu)) {
                $campos .= "{$gruda}tipo_menu";
                $valores .= "{$gruda}'{$this->tipo_menu}'";
                $gruda = ', ';
            }

            if (is_string($this->email)) {
                $campos .= "{$gruda}email";
                $valores .= "{$gruda}'{$this->email}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;//$db->InsertId( "{$this->_tabela}_ref_cod_pessoa_fj_seq");
        }

        return false;
    }

    public function edita()
    {
        if (is_numeric($this->ref_cod_pessoa_fj)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->matricula)) {
                $set .= "{$gruda}matricula = '{$this->matricula}'";
                $gruda = ', ';
            }
            if (is_string($this->matricula_interna)) {
                $set .= "{$gruda}matricula_interna = '{$this->matricula_interna}'";
                $gruda = ', ';
            }
            if (is_string($this->senha)) {
                $set .= "{$gruda}senha = '{$this->senha}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_sec)) {
                $set .= "{$gruda}ref_sec = '{$this->ref_sec}'";
                $gruda = ', ';
            }
            if (is_string($this->sequencial)) {
                $set .= "{$gruda}sequencial = '{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_string($this->opcao_menu)) {
                $set .= "{$gruda}opcao_menu = '{$this->opcao_menu}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_administracao_secretaria)) {
                $set .= "{$gruda}ref_cod_administracao_secretaria = '{$this->ref_cod_administracao_secretaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_administracao_secretaria)) {
                $set .= "{$gruda}ref_ref_cod_administracao_secretaria = '{$this->ref_ref_cod_administracao_secretaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_departamento)) {
                $set .= "{$gruda}ref_cod_departamento = '{$this->ref_cod_departamento}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_ref_cod_administracao_secretaria)) {
                $set .= "{$gruda}ref_ref_ref_cod_administracao_secretaria = '{$this->ref_ref_ref_cod_administracao_secretaria}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_departamento)) {
                $set .= "{$gruda}ref_ref_cod_departamento = '{$this->ref_ref_cod_departamento}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_setor)) {
                $set .= "{$gruda}ref_cod_setor = '{$this->ref_cod_setor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_funcionario_vinculo)) {
                $set .= "{$gruda}ref_cod_funcionario_vinculo = '{$this->ref_cod_funcionario_vinculo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tempo_expira_senha)) {
                $set .= "{$gruda}tempo_expira_senha = '{$this->tempo_expira_senha}'";
                $gruda = ', ';
            }

            if ($this->data_expiracao) {
                $set .= "{$gruda}data_expiracao = '{$this->data_expiracao}'";
                $gruda = ', ';
            } elseif (is_null($this->data_expiracao)) {
                $set .= "{$gruda}data_expiracao = NULL";
                $gruda = ', ';
            }

            if (is_string($this->data_troca_senha)) {
                $set .= "{$gruda}data_troca_senha = '{$this->data_troca_senha}'";
                $gruda = ', ';
            }
            if (is_string($this->data_reativa_conta)) {
                $set .= "{$gruda}data_reativa_conta = '{$this->data_reativa_conta}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_pessoa_fj)) {
                $set .= "{$gruda}ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_setor_new)) {
                $set .= "{$gruda}ref_cod_setor_new = '{$this->ref_cod_setor_new}'";
                $gruda = ', ';
            }
            if (is_numeric($this->matricula_new)) {
                $set .= "{$gruda}matricula_new = '{$this->matricula_new}'";
                $gruda = ', ';
            }
            if (is_numeric($this->tipo_menu)) {
                $set .= "{$gruda}tipo_menu = '{$this->tipo_menu}'";
                $gruda = ', ';
            }

            if (is_string($this->email)) {
                $set .= "{$gruda}email = '{$this->email}'";
                $gruda = ', ';
            }

            if (is_numeric($this->receber_novidades)) {
                $set .= "{$gruda}receber_novidades = '{$this->receber_novidades}'";
                $gruda = ', ';
            }

            if (is_numeric($this->atualizou_cadastro)) {
                $set .= "{$gruda}atualizou_cadastro = '{$this->atualizou_cadastro}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'");

                return true;
            }
        }

        return false;
    }

    public function lista($str_matricula = null, $str_senha = null, $int_ativo = null, $int_ref_sec = null, $str_ramal = null, $str_sequencial = null, $str_opcao_menu = null, $int_ref_cod_administracao_secretaria = null, $int_ref_ref_cod_administracao_secretaria = null, $int_ref_cod_departamento = null, $int_ref_ref_ref_cod_administracao_secretaria = null, $int_ref_ref_cod_departamento = null, $int_ref_cod_setor = null, $int_ref_cod_funcionario_vinculo = null, $int_tempo_expira_senha = null, $data_expiracao = null, $date_data_troca_senha_ini = null, $date_data_troca_senha_fim = null, $date_data_reativa_conta_ini = null, $date_data_reativa_conta_fim = null, $int_ref_ref_cod_pessoa_fj = null, $int_proibido = null, $int_ref_cod_setor_new = null, $int_matricula_new = null, $int_matricula_permanente = null, $int_tipo_menu = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($this->ref_cod_pessoa_fj)) {
            $filtros .= "{$whereAnd} ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_matricula)) {
            $filtros .= "{$whereAnd} matricula LIKE '%{$str_matricula}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_senha)) {
            $filtros .= "{$whereAnd} senha LIKE '%{$str_senha}%'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_sec)) {
            $filtros .= "{$whereAnd} ref_sec = '{$int_ref_sec}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sequencial)) {
            $filtros .= "{$whereAnd} sequencial LIKE '%{$str_sequencial}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_opcao_menu)) {
            $filtros .= "{$whereAnd} opcao_menu LIKE '%{$str_opcao_menu}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_administracao_secretaria)) {
            $filtros .= "{$whereAnd} ref_cod_administracao_secretaria = '{$int_ref_cod_administracao_secretaria}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_administracao_secretaria)) {
            $filtros .= "{$whereAnd} ref_ref_cod_administracao_secretaria = '{$int_ref_ref_cod_administracao_secretaria}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_departamento)) {
            $filtros .= "{$whereAnd} ref_cod_departamento = '{$int_ref_cod_departamento}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_ref_cod_administracao_secretaria)) {
            $filtros .= "{$whereAnd} ref_ref_ref_cod_administracao_secretaria = '{$int_ref_ref_ref_cod_administracao_secretaria}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_departamento)) {
            $filtros .= "{$whereAnd} ref_ref_cod_departamento = '{$int_ref_ref_cod_departamento}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_setor)) {
            $filtros .= "{$whereAnd} ref_cod_setor = '{$int_ref_cod_setor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_funcionario_vinculo)) {
            $filtros .= "{$whereAnd} ref_cod_funcionario_vinculo = '{$int_ref_cod_funcionario_vinculo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_tempo_expira_senha)) {
            $filtros .= "{$whereAnd} tempo_expira_senha = '{$int_tempo_expira_senha}'";
            $whereAnd = ' AND ';
        }
        if ($data_expiracao) {
            $filtros .= "{$whereAnd} data_expiracao = '{$data_expiracao}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_troca_senha_ini)) {
            $filtros .= "{$whereAnd} data_troca_senha >= '{$date_data_troca_senha_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_troca_senha_fim)) {
            $filtros .= "{$whereAnd} data_troca_senha <= '{$date_data_troca_senha_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_reativa_conta_ini)) {
            $filtros .= "{$whereAnd} data_reativa_conta >= '{$date_data_reativa_conta_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_reativa_conta_fim)) {
            $filtros .= "{$whereAnd} data_reativa_conta <= '{$date_data_reativa_conta_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_pessoa_fj)) {
            $filtros .= "{$whereAnd} ref_ref_cod_pessoa_fj = '{$int_ref_ref_cod_pessoa_fj}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_setor_new)) {
            $filtros .= "{$whereAnd} ref_cod_setor_new = '{$int_ref_cod_setor_new}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_matricula_new)) {
            $filtros .= "{$whereAnd} matricula_new = '{$int_matricula_new}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_tipo_menu)) {
            $filtros .= "{$whereAnd} tipo_menu = '{$int_tipo_menu}'";
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

    public function detalhe()
    {
        if (is_numeric($this->ref_cod_pessoa_fj)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function existe()
    {
        if (is_numeric($this->ref_cod_pessoa_fj)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_pessoa_fj = '{$this->ref_cod_pessoa_fj}'");

            if ($db->ProximoRegistro()) {
                return true;
            }
        }

        return false;
    }

    public function excluir()
    {
        if (is_numeric($this->ref_cod_pessoa_fj)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna a string com o nome do vinculo cujo código foi passado por parâmetro
     *
     * @return string
     */
    public function getNomeVinculo($cod_funcionario_vinculo)
    {
        if (is_numeric($cod_funcionario_vinculo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT nm_vinculo FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo = '{$cod_funcionario_vinculo}'");

            if ($db->ProximoRegistro()) {
                $registro = $db->Tupla();

                return $registro['nm_vinculo'];
            }
        }

        return false;
    }
}
