<?php

use Illuminate\Support\Facades\Auth;

class clsPessoaFisica extends clsPessoaFj
{
    public $idpes;
    public $data_nasc;
    public $sexo;
    public $idpes_mae;
    public $idpes_pai; 
    public $idpes_responsavel;
    public $idesco;
    public $ideciv;
    public $idpes_con;
    public $data_uniao;
    public $data_obito;
    public $nacionalidade;
    public $idpais_estrangeiro;
    public $data_chagada_brasil;
    public $idmun_nascimento;
    public $ultima_empresa;
    public $idocup;
    public $nome_mae;
    public $nome_pai;
    public $nome_conjuge;
    public $nome_responsavel;
    public $justificativa_provisorio;
    public $cpf;
    public $ref_cod_religiao;
    public $tipo_endereco;
    public $ativo;
    public $data_exclusao;
    public $zona_localizacao_censo;
    public $localizacao_diferenciada;
    public $nome_social;
    public $pais_residencia;
    public $banco = 'pmi';
    public $schema_cadastro = 'cadastro';
    public $ref_cod_profissao;
    public $ref_cod_banco;
    public $agencia;
    public $conta;
    public $tipo_conta;

    /**
     * Construtor.
     */
    public function __construct(
        $int_idpes = false,
        $numeric_cpf = false,
        $date_data_nasc = false,
        $str_sexo = false,
        $int_idpes_mae = false,
        $int_idpes_pai = false
    ) {
        $this->idpes = $int_idpes;
        $this->cpf = $numeric_cpf;
    }

    public function lista(
        $str_nome = false,
        $numeric_cpf = false,
        $inicio_limite = false,
        $qtd_registros = false,
        $str_orderBy = false,
        $int_ref_cod_sistema = false,
        $int_idpes = false,
        $ativo = 1
    ) {
        $whereAnd = '';
        $where = '';
        $db = new clsBanco();

        if (is_string($str_nome) && $str_nome != '') {
            $str_nome = $db->escapeString($str_nome);
            $where .= "{$whereAnd} coalesce(slug, unaccent(nome)) ILIKE unaccent('%{$str_nome}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($numeric_cpf)) {
            $numeric_cpf = pg_escape_string($numeric_cpf);

            $where .= "{$whereAnd} cpf::varchar ILIKE E'%{$numeric_cpf}%' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_sistema)) {
            $where .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR cpf is not null  )";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd} idpes = '$int_idpes'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ativo)) {
            $where .= "{$whereAnd} ativo = $ativo";
            $whereAnd = ' AND ';
        } 

        if (is_numeric($this->tipo_endereco)) {
            // Interno
            $where .= "{$whereAnd} idpes IN (SELECT idpes FROM cadastro.endereco_pessoa)";
        }

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        $orderBy = ' ORDER BY ';

        if ($str_orderBy) {
            $orderBy .= $str_orderBy . ' ';
        } else {
            $orderBy .= 'COALESCE(nome_social, nome) ';
        }

        $dba = new clsBanco();

        if ($where) {
            $where = 'WHERE ' . $where;
        }

        if (!$where) {
            $total = $db->CampoUnico('SELECT COUNT(0) FROM cadastro.fisica ' . $where);
        } else {
            $total = $db->CampoUnico('SELECT COUNT(0) FROM cadastro.v_pessoa_fisica ' . $where);
        }

        $db->Consulta(sprintf(
            'SELECT idpes, nome, nome_social, url, \'F\' AS tipo, email, cpf FROM cadastro.v_pessoa_fisica %s %s %s',
            $where,
            $orderBy,
            $limite
        ));

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['total'] = $total;

            $dba->Consulta(sprintf(
                'SELECT
                ddd_1, fone_1, ddd_2, fone_2, ddd_mov, fone_mov, ddd_fax, fone_fax
                FROM
                cadastro.v_fone_pessoa
                WHERE idpes = %d',
                $tupla['idpes']
            ));

            if ($dba->ProximoRegistro()) {
                $tupla_fone = $dba->Tupla();
            } else {
                $tupla_fone = '';
            }

            $tupla['ddd_1'] = $tupla_fone['ddd_1'];
            $tupla['fone_1'] = $tupla_fone['fone_1'];
            $tupla['ddd_2'] = $tupla_fone['ddd_2'];
            $tupla['fone_2'] = $tupla_fone['fone_2'];
            $tupla['ddd_mov'] = $tupla_fone['ddd_mov'];
            $tupla['fone_mov'] = $tupla_fone['fone_mov'];
            $tupla['ddd_fax'] = $tupla_fone['ddd_fax'];
            $tupla['fone_fax'] = $tupla_fone['fone_fax'];

            $resultado[] = $tupla;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    public function detalhe()
    {
        if ($this->idpes) {
            $tupla = parent::detalhe();

            $objFisica = new clsFisica($this->idpes);
            $detalhe_fisica = $objFisica->detalhe();

            if ($detalhe_fisica) {
                $this->data_nasc = $detalhe_fisica['data_nasc'];
                $this->sexo = $detalhe_fisica['sexo'];
                $this->idpes_mae = $detalhe_fisica['idpes_mae'];
                $this->idpes_pai = $detalhe_fisica['idpes_pai'];
                $this->idpes_responsavel = $detalhe_fisica['idpes_responsavel'];
                $this->idesco = $detalhe_fisica['idesco'];
                $this->ideciv = $detalhe_fisica['ideciv'];
                $this->idpes_con = $detalhe_fisica['idpes_con'];
                $this->data_uniao = $detalhe_fisica['data_uniao'];
                $this->data_obito = $detalhe_fisica['data_obito'];
                $this->nacionalidade = $detalhe_fisica['nacionalidade'];
                $this->idpais_estrangeiro = $detalhe_fisica['idpais_estrangeiro'];
                $this->data_chagada_brasil = $detalhe_fisica['data_chagada_brasil'] ?? null;
                $this->idmun_nascimento = $detalhe_fisica['idmun_nascimento'];
                $this->ultima_empresa = $detalhe_fisica['ultima_empresa'];
                $this->idocup = $detalhe_fisica['idocup'];
                $this->nome_mae = $detalhe_fisica['nome_mae'];
                $this->nome_pai = $detalhe_fisica['nome_pai'];
                $this->nome_conjuge = $detalhe_fisica['nome_conjuge'];
                $this->nome_responsavel = $detalhe_fisica['nome_responsavel'];
                $this->justificativa_provisorio = $detalhe_fisica['justificativa_provisorio'];
                $this->cpf = $detalhe_fisica['cpf'];
                $this->ref_cod_religiao = $detalhe_fisica['ref_cod_religiao'];
                $this->sus = $detalhe_fisica['sus'];
                $this->nis_pis_pasep = $detalhe_fisica['nis_pis_pasep'];
                $this->ocupacao = $detalhe_fisica['ocupacao'];
                $this->empresa = $detalhe_fisica['empresa'];
                $this->ddd_telefone_empresa = $detalhe_fisica['ddd_telefone_empresa'];
                $this->telefone_empresa = $detalhe_fisica['telefone_empresa'];
                $this->pessoa_contato = $detalhe_fisica['pessoa_contato'];
                $this->renda_mensal = $detalhe_fisica['renda_mensal'];
                $this->data_admissao = $detalhe_fisica['data_admissao'];
                $this->falecido = $detalhe_fisica['falecido'];
                $this->ativo = $detalhe_fisica['ativo'];
                $this->data_exclusao = $detalhe_fisica['data_exclusao'];
                $this->zona_localizacao_censo = $detalhe_fisica['zona_localizacao_censo'];
                $this->localizacao_diferenciada = $detalhe_fisica['localizacao_diferenciada'];
                $this->nome_social = $detalhe_fisica['nome_social'];
                $this->pais_residencia = $detalhe_fisica['pais_residencia'];
                $this->ref_cod_profissao = $detalhe_fisica['ref_cod_profissao'];
                $this->ref_cod_banco = $detalhe_fisica['ref_cod_banco'];
                $this->agencia = $detalhe_fisica['agencia'];
                $this->conta = $detalhe_fisica['conta'];
                $this->tipo_conta = $detalhe_fisica['tipo_conta'];

                $tupla['idpes'] = $this->idpes;
                $tupla[] = &$tupla['idpes'];

                $tupla['cpf'] = $this->cpf;
                $tupla[] = &$tupla['cpf'];

                $tupla['ref_cod_religiao'] = $this->ref_cod_religiao;
                $tupla[] = &$tupla['ref_cod_religiao'];

                $tupla['data_nasc'] = $this->data_nasc;
                $tupla[] = &$tupla['data_nasc'];

                $tupla['sexo'] = $this->sexo;
                $tupla[] = &$tupla['sexo'];

                $tupla['idpes_mae'] = $this->idpes_mae;
                $tupla[] = &$tupla['idpes_mae'];

                $tupla['idpes_pai'] = $this->idpes_pai;
                $tupla[] = &$tupla['idpes_pai'];

                $tupla['idpes_responsavel'] = $this->idpes_responsavel;
                $tupla[] = &$tupla['idpes_responsavel'];

                $tupla['idesco'] = $this->idesco;
                $tupla[] = &$tupla['idesco'];

                $tupla['ideciv'] = $this->ideciv;
                $tupla[] = &$tupla['ideciv'];

                $tupla['idpes_con'] = $this->idpes_con;
                $tupla[] = &$tupla['idpes_con'];

                $tupla['data_uniao'] = $this->data_uniao;
                $tupla[] = &$tupla['data_uniao'];

                $tupla['data_obito'] = $this->data_obito;
                $tupla[] = &$tupla['data_obito'];

                $tupla['nacionalidade'] = $this->nacionalidade;
                $tupla[] = &$tupla['nacionalidade'];

                $tupla['idpais_estrangeiro'] = $this->idpais_estrangeiro;
                $tupla[] = &$tupla['idpais_estrangeiro'];

                $tupla['data_chagada_brasil'] = $this->data_chagada_brasil;
                $tupla[] = &$tupla['data_chagada_brasil'];

                $tupla['idmun_nascimento'] = $this->idmun_nascimento;
                $tupla[] = &$tupla['idmun_nascimento'];

                $tupla['ultima_empresa'] = $this->ultima_empresa;
                $tupla[] = &$tupla['ultima_empresa'];

                $tupla['idocup'] = $this->idocup;
                $tupla[] = &$tupla['idocup'];

                $tupla['nome_mae'] = $this->nome_mae;
                $tupla[] = &$tupla['nome_mae'];

                $tupla['nome_pai'] = $this->nome_pai;
                $tupla[] = &$tupla['nome_pai'];

                $tupla['nome_conjuge'] = $this->nome_conjuge;
                $tupla[] = &$tupla['nome_conjuge'];

                $tupla['nome_responsavel'] = $this->nome_responsavel;
                $tupla[] = &$tupla['nome_responsavel'];

                $tupla['justificativa_provisorio'] = $this->justificativa_provisorio;
                $tupla[] = &$tupla['justificativa_provisorio'];

                $tupla['falecido'] = $this->falecido;
                $tupla[] = &$tupla['falecido'];

                $tupla['ativo'] = $this->ativo;
                $tupla[] = &$tupla['ativo'];

                $tupla['data_exclusao'] = $this->data_exclusao;
                $tupla[] = &$tupla['data_exclusao'];

                $tupla['zona_localizacao_censo'] = $this->zona_localizacao_censo;
                $tupla[] = &$tupla['zona_localizacao_censo'];

                $tupla['localizacao_diferenciada'] = $this->localizacao_diferenciada;
                $tupla[] = &$tupla['localizacao_diferenciada'];

                $tupla['nome_social'] = $this->nome_social;
                $tupla[] = &$tupla['nome_social'];

                $tupla['pais_residencia'] = $this->pais_residencia;
                $tupla[] = &$tupla['pais_residencia'];

                $tupla['ref_cod_profissao'] = $this->ref_cod_profissao;
                $tupla[] = &$tupla['ref_cod_profissao'];

                $tupla['ref_cod_banco'] = $this->ref_cod_banco;
                $tupla[] = &$tupla['ref_cod_banco'];

                $tupla['agencia'] = $this->agencia;
                $tupla[] = &$tupla['agencia'];

                $tupla['conta'] = $this->conta;
                $tupla[] = &$tupla['conta'];

                $tupla['tipo_conta'] = $this->tipo_conta;
                $tupla[] = &$tupla['tipo_conta'];

                return $tupla;
            }
        } elseif ($this->cpf) {
            $tupla = parent::detalhe();

            $objFisica = new clsFisica();
            $lista = $objFisica->lista(
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                $this->cpf,
                false, 
                false,
                false,
                false
            );

            $this->idpes = $lista[0]['idpes'];

            if ($this->idpes) {
                $objFisica = new clsFisica($this->idpes);
                $detalhe_fisica = $objFisica->detalhe();

                if ($detalhe_fisica) {
                    $this->data_nasc = $detalhe_fisica['data_nasc'];
                    $this->sexo = $detalhe_fisica['sexo'];
                    $this->idpes_mae = $detalhe_fisica['idpes_mae'];
                    $this->idpes_pai = $detalhe_fisica['idpes_pai'];
                    $this->idpes_responsavel = $detalhe_fisica['idpes_responsavel'];
                    $this->idesco = $detalhe_fisica['idesco'];
                    $this->ideciv = $detalhe_fisica['ideciv'];
                    $this->idpes_con = $detalhe_fisica['idpes_con'];
                    $this->data_uniao = $detalhe_fisica['data_uniao'];
                    $this->data_obito = $detalhe_fisica['data_obito'];
                    $this->nacionalidade = $detalhe_fisica['nacionalidade'];
                    $this->idpais_estrangeiro = $detalhe_fisica['idpais_estrangeiro'];
                    $this->data_chagada_brasil = $detalhe_fisica['data_chagada_brasil'];
                    $this->idmun_nascimento = $detalhe_fisica['idmun_nascimento'];
                    $this->ultima_empresa = $detalhe_fisica['ultima_empresa'];
                    $this->idocup = $detalhe_fisica['idocup'];
                    $this->nome_mae = $detalhe_fisica['nome_mae'];
                    $this->nome_pai = $detalhe_fisica['nome_pai'];
                    $this->nome_conjuge = $detalhe_fisica['nome_conjuge'];
                    $this->nome_responsavel = $detalhe_fisica['nome_responsavel'];
                    $this->justificativa_provisorio = $detalhe_fisica['justificativa_provisorio'];
                    $this->cpf = $detalhe_fisica['cpf'];
                    $this->ocupacao = $detalhe_fisica['ocupacao'];
                    $this->empresa = $detalhe_fisica['empresa'];
                    $this->ddd_telefone_empresa = $detalhe_fisica['ddd_telefone_empresa'];
                    $this->telefone_empresa = $detalhe_fisica['telefone_empresa'];
                    $this->renda_mensal = $detalhe_fisica['renda_mensal'];
                    $this->data_admissao = $detalhe_fisica['data_admissao'];
                    $this->ativo = $detalhe_fisica['ativo'];
                    $this->data_exclusao = $detalhe_fisica['data_exclusao'];
                    $this->zona_localizacao_censo = $detalhe_fisica['zona_localizacao_censo'];
                    $this->localizacao_diferenciada = $detalhe_fisica['localizacao_diferenciada'];
                    $this->nome_social = $detalhe_fisica['nome_social'];
                    $this->pais_residencia = $detalhe_fisica['pais_residencia'];
                    $this->ref_cod_profissao = $detalhe_fisica['ref_cod_profissao'];
                    $this->ref_cod_banco = $detalhe_fisica['ref_cod_banco'];
                    $this->agencia = $detalhe_fisica['agencia'];
                    $this->conta = $detalhe_fisica['conta'];
                    $this->tipo_conta = $detalhe_fisica['tipo_conta'];


                    $tupla['idpes'] = $this->idpes;
                    $tupla[] = &$tupla['idpes'];

                    $tupla['cpf'] = $this->cpf;
                    $tupla[] = &$tupla['cpf'];

                    $tupla['data_nasc'] = $this->data_nasc;
                    $tupla[] = &$tupla['data_nasc'];

                    $tupla['sexo'] = $this->sexo;
                    $tupla[] = &$tupla['sexo'];

                    $tupla['idpes_mae'] = $this->idpes_mae;
                    $tupla[] = &$tupla['idpes_mae'];

                    $tupla['idpes_pai'] = $this->idpes_pai;
                    $tupla[] = &$tupla['idpes_pai'];

                    $tupla['idpes_responsavel'] = $this->idpes_responsavel;
                    $tupla[] = &$tupla['idpes_responsavel'];

                    $tupla['idesco'] = $this->idesco;
                    $tupla[] = &$tupla['idesco'];

                    $tupla['ideciv'] = $this->ideciv;
                    $tupla[] = &$tupla['ideciv'];

                    $tupla['idpes_con'] = $this->idpes_con;
                    $tupla[] = &$tupla['idpes_con'];

                    $tupla['data_uniao'] = $this->data_uniao;
                    $tupla[] = &$tupla['data_uniao'];

                    $tupla['data_obito'] = $this->data_obito;
                    $tupla[] = &$tupla['data_obito'];

                    $tupla['nacionalidade'] = $this->nacionalidade;
                    $tupla[] = &$tupla['nacionalidade'];

                    $tupla['idpais_estrangeiro'] = $this->idpais_estrangeiro;
                    $tupla[] = &$tupla['idpais_estrangeiro'];

                    $tupla['data_chagada_brasil'] = $this->data_chagada_brasil;
                    $tupla[] = &$tupla['data_chagada_brasil'];

                    $tupla['idmun_nascimento'] = $this->idmun_nascimento;
                    $tupla[] = &$tupla['idmun_nascimento'];

                    $tupla['ultima_empresa'] = $this->ultima_empresa;
                    $tupla[] = &$tupla['ultima_empresa'];

                    $tupla['idocup'] = $this->idocup;
                    $tupla[] = &$tupla['idocup'];

                    $tupla['nome_mae'] = $this->nome_mae;
                    $tupla[] = &$tupla['nome_mae'];

                    $tupla['nome_pai'] = $this->nome_pai;
                    $tupla[] = &$tupla['nome_pai'];

                    $tupla['nome_conjuge'] = $this->nome_conjuge;
                    $tupla[] = &$tupla['nome_conjuge'];

                    $tupla['nome_responsavel'] = $this->nome_responsavel;
                    $tupla[] = &$tupla['nome_responsavel'];

                    $tupla['justificativa_provisorio'] = $this->justificativa_provisorio;
                    $tupla[] = &$tupla['justificativa_provisorio'];

                    $tupla['ativo'] = $this->ativo;
                    $tupla[] = &$tupla['ativo'];

                    $tupla['data_exclusao'] = $this->data_exclusao;
                    $tupla[] = &$tupla['data_exclusao'];

                    $tupla['zona_localizacao_censo'] = $this->zona_localizacao_censo;
                    $tupla[] = &$tupla['zona_localizacao_censo'];

                    $tupla['localizacao_diferenciada'] = $this->localizacao_diferenciada;
                    $tupla[] = &$tupla['localizacao_diferenciada'];

                    $tupla['nome_social'] = $this->nome_social;
                    $tupla[] = &$tupla['nome_social'];

                    $tupla['pais_residencia'] = $this->pais_residencia;
                    $tupla[] = &$tupla['pais_residencia'];

                    $tupla['ref_cod_profissao'] = $this->ref_cod_profissao;
                    $tupla[] = &$tupla['ref_cod_profissao'];
                    
                    $tupla['ref_cod_banco'] = $this->ref_cod_banco;
                    $tupla[] = &$tupla['ref_cod_banco'];

                    $tupla['agencia'] = $this->agencia;
                    $tupla[] = &$tupla['agencia'];

                    $tupla['conta'] = $this->conta;
                    $tupla[] = &$tupla['conta'];

                    $tupla['tipo_conta'] = $this->tipo;
                    $tupla[] = &$tupla['tipo_conta'];

                    return $tupla;
                }
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

            $resultado[$pos] = $this->$campo ? $this->$campo : '';
            $resultado[$campo] = &$resultado[$pos];

            $pos++;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    public function excluir()
    {
        if ($this->idpes) {
            $this->pessoa_logada = Auth::id();
            $db = new clsBanco();
            $excluir = $db->Consulta('UPDATE cadastro.fisica SET ativo = 0 WHERE idpes = ' . $this->idpes);

            if ($excluir) {
                $db->Consulta("UPDATE cadastro.fisica SET ref_usuario_exc = $this->pessoa_logada, data_exclusao = NOW() WHERE idpes = $this->idpes");
            }
        }
    }

    public function getNomeUsuario()
    {
        if ($this->idpes) {
            $db = new clsBanco();

            $db->Consulta("SELECT pessoa.nome, funcionario.matricula, usuario.cod_usuario
                       FROM cadastro.fisica
                 INNER JOIN pmieducar.usuario ON (fisica.ref_usuario_exc = usuario.cod_usuario)
                 INNER JOIN portal.funcionario ON (usuario.cod_usuario = funcionario.ref_cod_pessoa_fj)
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = funcionario.ref_cod_pessoa_fj)
                      WHERE fisica.idpes = $this->idpes");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla['matricula'];
            }
        }
    }

    public function detalheSimples()
    {
        if (is_numeric($this->idpes)) {
            $sql = "SELECT * FROM cadastro.fisica WHERE idpes = '{$this->idpes}' AND ativo = 1;";

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
