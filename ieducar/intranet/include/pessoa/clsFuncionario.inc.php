<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsFuncionario extends clsPessoaFisica
{
    public $idpes;
    public $matricula;
    public $senha;
    public $ativo;
    public $ref_sec;
    public $sequencial;
    public $opcao_menu;
    public $ref_cod_setor;
    public $ref_cod_funcionario_vinculo;
    public $tempo_expira_senha;
    public $data_expiracao;
    public $data_troca_senha;
    public $data_reativa_conta;
    public $ref_ref_cod_pessoa_fj;
    public $cpf;
    public $ref_cod_setor_new;
    public $schema_cadastro = 'cadastro';
    public $schema_portal = 'portal';
    public $tabela_pessoa = 'pessoa';

    public function __construct($int_idpes = false, $str_matricula = false, $int_cpf = false, $int_ref_cod_setor = false, $str_senha = false, $data_troca_senha = false, $tempo_expira_senha = false, $data_reativa_conta = false, $tempo_expira_conta = false, $ref_cod_funcionario_vinculo = false, $ramal = false, $matricula_permanente = false, $banido = false, $email = null)
    {
        $this->idpes = $int_idpes;
        $this->matricula = $str_matricula;
        $this->cpf = $int_cpf;
        $this->ref_cod_setor = $int_ref_cod_setor;
        $this->senha = $str_senha;
        $this->data_troca_senha = $data_troca_senha;
        $this->data_reativa_conta = $data_reativa_conta;
        $this->tempo_expira_senha = $tempo_expira_senha;
        $this->data_expiracao = $data_expiracao;
        $this->ref_cod_funcionario_vinculo = $ref_cod_funcionario_vinculo;
        $this->email = $email;
        $this->_campos_lista = ' 
            f.ref_cod_pessoa_fj,
            f.matricula,
            f.matricula_interna,
            f.senha,
            f.ativo,
            f.sequencial,
            f.opcao_menu,
            f.ref_cod_setor,
            f.ref_cod_funcionario_vinculo,
            f.tempo_expira_senha,
            f.data_expiracao,
            f.data_troca_senha,
            f.data_reativa_conta,
            f.ref_ref_cod_pessoa_fj,
            f.nome,
            f.ref_cod_setor_new,
            f.email
        ';
    }

    public function edita()
    {
        $set = '';
        $setVirgula = 'SET';

        if ($this->idpes) {
            $db = new clsBanco();

            if ($this->ref_cod_setor) {
                $set = "{$setVirgula} ref_cod_setor_new = '$this->ref_cod_setor' ";
                $setVirgula = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->schema_portal}.funcionario $set WHERE ref_cod_pessoa_fj = '{$this->idpes}'");
            }
        }
    }

    public function lista(
        $str_matricula = false,
        $str_nome = false,
        $int_ativo = false,
        $int_secretaria = false,
        $int_departamento = false,
        $int_setor = false,
        $int_vinculo = false,
        $int_inicio_limit = false,
        $int_qtd_registros = false,
        $str_ramal = false,
        $matricula_is_not_null = false,
        $int_idpes = false,
        $email = null,
        $matricula_interna = null
    ) {
        // Recuperar lista
        $sql = " SELECT {$this->_campos_lista} FROM {$this->schema_portal}.v_funcionario f";
        $filtros = '';
        $filtro_pessoa = false;

        $whereAnd = ' WHERE ';

        if (is_string($str_matricula) && $str_matricula != '') {
            $filtros .= "{$whereAnd} (f.matricula) LIKE ('%{$str_matricula}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($matricula_interna) && $matricula_interna != '') {
            $filtros .= "{$whereAnd} (f.matricula_interna) LIKE ('%{$matricula_interna}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} translate(upper(f.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
            $filtro_pessoa = true;
        }

        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} f.ativo = '{$int_ativo}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_vinculo)) {
            $filtros .= "{$whereAnd} f.ref_cod_funcionario_vinculo = '{$int_vinculo}'";
            $whereAnd = ' AND ';
        }

        if ($matricula_is_not_null) {
            $filtros .= "{$whereAnd} f.matricula  IS NOT NULL";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpes)) {
            $filtros .= "{$whereAnd} f.ref_cod_pessoa_fj = '{$int_idpes}'";
            $whereAnd = ' AND ';
            $filtro_pessoa = true;
        }

        if (is_string($str_email)) {
            $filtros .= "{$whereAnd} f.email ILIKE '%{$str_email}%'f";
            $whereAnd = ' AND ';
        }

        $limite = '';
        if ($int_inicio_limit !== false && $int_qtd_registros !== false) {
            $limite = "LIMIT $int_qtd_registros OFFSET $int_inicio_limit ";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        if ($int_inicio_limit !== false && $int_qtd_registros !== false) {
            $sql .= "{$filtros}" . ' ORDER BY (f.nome) ASC ' . $limite;
        } else {
            $sql .= "{$filtros}" . $this->getOrderby() . $this->getLimite();
        }

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->schema_portal}.v_funcionario f {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $resultado = $db->Tupla();//$tupla;
            }
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function listaFuncionarioUsuario(
        $str_matricula = false,
        $str_nome = false,
        $matricula_interna = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_instituicao = null,
        $int_ref_cod_tipo_usuario = null,
        $int_nivel = null,
        $int_ativo = null
    ) {
        $sql = " SELECT DISTINCT f.ref_cod_pessoa_fj, f.nome, f.matricula, f.matricula_interna, f.ativo, tu.nm_tipo, tu.nivel
                            FROM {$this->schema_portal}.v_funcionario f
                            LEFT JOIN pmieducar.usuario u ON (u.cod_usuario = f.ref_cod_pessoa_fj)
                            LEFT JOIN pmieducar.tipo_usuario tu ON (u.ref_cod_tipo_usuario = tu.cod_tipo_usuario)
                            LEFT JOIN pmieducar.escola_usuario eu ON (eu.ref_cod_usuario = u.cod_usuario)  ";
        $filtros = '';
        $filtro_pessoa = false;

        $whereAnd = ' WHERE u.ativo = 1 AND ';

        if (is_string($str_matricula) && $str_matricula != '') {
            $filtros .= "{$whereAnd} (f.matricula) LIKE ('%{$str_matricula}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($matricula_interna) && $matricula_interna != '') {
            $filtros .= "{$whereAnd} (f.matricula_interna) LIKE ('%{$matricula_interna}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} translate(upper(f.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
            $filtro_pessoa = true;
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} eu.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} u.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_tipo_usuario)) {
            $filtros .= "{$whereAnd} u.ref_cod_tipo_usuario = '{$int_ref_cod_tipo_usuario}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_nivel)) {
            $filtros .= "{$whereAnd} tu.nivel = '$int_nivel'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} f.ativo = '$int_ativo'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= "{$filtros}" . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->schema_portal}.v_funcionario f
                                                                                LEFT JOIN pmieducar.usuario u ON (u.cod_usuario = f.ref_cod_pessoa_fj)
                                                                                LEFT JOIN pmieducar.tipo_usuario tu ON (u.ref_cod_tipo_usuario = tu.cod_tipo_usuario)
                                                                                LEFT JOIN pmieducar.escola_usuario eu ON (eu.ref_cod_usuario = u.cod_usuario) {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $resultado = $db->Tupla();
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function detalhe()
    {
        $idpesOk = false;
        if (is_numeric($this->idpes)) {
            $idpesOk = true;
        } elseif ($this->matricula) {
            $db = new clsBanco();

            $db->Consulta("SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE matricula = '{$this->matricula}'");
            if ($db->ProximoRegistro()) {
                list($this->idpes) = $db->Tupla();
                $idpesOk = true;
            }
        } elseif (is_numeric($this->cpf)) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpes FROM {$this->schema_cadastro}.fisica WHERE cpf = '{$this->cpf}'");
            if ($db->ProximoRegistro()) {
                list($this->idpes) = $db->Tupla();
                $idpesOk = true;
            }
        }
        if ($idpesOk) {
            $tupla = parent::detalhe();
            $db = new clsBanco();

            $db->Consulta("SELECT ref_cod_pessoa_fj, matricula, matricula_interna, senha, ativo, ref_sec, sequencial, opcao_menu, ref_cod_setor, ref_cod_funcionario_vinculo, tempo_expira_senha, data_expiracao, data_troca_senha, data_reativa_conta, ref_ref_cod_pessoa_fj, ref_cod_setor_new, email FROM portal.funcionario WHERE ref_cod_pessoa_fj = '{$this->idpes}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                list($this->idpes, $this->matricula, $this->senha, $this->ativo, $this->ref_sec, $this->sequencial, $this->opcao_menu, $this->ref_cod_setor, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, $this->data_expiracao, $this->data_troca_senha, $this->data_reativa_conta, $this->ref_ref_cod_pessoa_fj, $this->ref_cod_setor_new) = $tupla;

                $tupla['idpes'] = new clsPessoaFisica($tupla['ref_cod_pessoa_fj']);
                $tupla[] = $tupla['idpes'];

                return $tupla;
            }
        }

        return false;
    }

    public function queryRapida($int_idpes)
    {
        $this->idpes = $int_idpes;
        $this->detalhe();
        $resultado = [];
        $pos = 0;
        for ($i = 1; $i < func_num_args(); $i++) {
            $campo = func_get_arg($i);
            $resultado[$pos] = ($this->$campo) ? $this->$campo : '';
            $resultado[$campo] = &$resultado[$pos];
            $pos++;
        }
        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }
}
