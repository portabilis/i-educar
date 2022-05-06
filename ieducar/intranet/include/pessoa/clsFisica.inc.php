<?php

use App\Models\LegacyPerson;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class clsFisica
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
    public $nome_social;
    public $observacao;

    /**
     * Nacionalidade:
     *
     *  1 - Brasileiro
     *  2 - Naturalizado Brasileiro
     *  3 - Estrangeiro
     *
     * @var $nacionalidade
     */
    public $nacionalidade;

    public $idpais_estrangeiro = false;
    public $data_chagada_brasil;
    public $idmun_nascimento;
    public $ultima_empresa;
    public $idocup;
    public $nome_mae;
    public $nome_pai;
    public $nome_conjuge;
    public $nome_responsavel;
    public $justificativa_provisorio;
    public $idpes_cad;
    public $idpes_rev;
    public $ref_cod_sistema;
    public $cpf;
    public $ref_cod_religiao;
    public $sus = false;
    public $nis_pis_pasep;
    public $ocupacao;
    public $empresa;
    public $ddd_telefone_empresa;
    public $telefone_empresa;
    public $pessoa_contato;
    public $renda_mensal;
    public $data_admissao;
    public $falecido;
    public $ativo;
    public $zona_localizacao_censo;
    public $tipo_trabalho;
    public $local_trabalho;
    public $horario_inicial_trabalho;
    public $horario_final_trabalho;
    public $pais_residencia;
    public $localizacao_diferenciada;
    public $tabela;
    public $schema;
    public $ref_cod_profissao;

    public function __construct(
        $idpes = false,
        $data_nasc = false,
        $sexo = false,
        $idpes_mae = false,
        $idpes_pai = false,
        $idpes_responsavel = false,
        $idesco = false,
        $ideciv = false,
        $idpes_con = false,
        $data_uniao = false,
        $data_obito = false,
        $nacionalidade = false,
        $idpais_estrangeiro = false,
        $data_chagada_brasil = false,
        $idmun_nascimento = false,
        $ultima_empresa = false,
        $idocup = false,
        $nome_mae = false,
        $nome_pai = false,
        $nome_conjuge = false,
        $nome_responsavel = false,
        $justificativa_provisorio = false,
        $idpes_cad = false,
        $idpes_rev = false,
        $ref_cod_sistema = false,
        $cpf = false,
        $ref_cod_religiao = false,
        $ocupacao = false,
        $empresa = false,
        $ddd_telefone_empresa = false,
        $telefone_empresa = false,
        $renda_mensal = false,
        $data_admissao = false,
        $falecido = false,
        $zona_localizacao_censo = false,
        $tipo_trabalho = false,
        $local_trabalho = false,
        $horario_inicial_trabalho = false,
        $horario_final_trabalho = false,
        $nome_social = false,
        $observacao = false,
        $ref_cod_profissao = false
    ) {
        $objPessoa = new clsPessoa_($idpes);
        if ($objPessoa->detalhe()) {
            $this->idpes  = $idpes;
        }
        $objPessoaMae = new clsPessoa_($idpes_mae);
        if ($objPessoaMae->detalhe()) {
            $this->idpes_mae  = $idpes_mae;
        } elseif ($idpes_mae == 'NULL') {
            $this->idpes_mae = 'NULL';
        }
        $objPessoaPai = new clsPessoa_($idpes_pai);
        if ($objPessoaPai->detalhe()) {
            $this->idpes_pai  = $idpes_pai;
        } elseif ($idpes_pai == 'NULL') {
            $this->idpes_pai = 'NULL';
        }

        $objPessoaResponsavel = new clsPessoa_($idpes_responsavel);
        if ($objPessoaResponsavel->detalhe() ||  $idpes_responsavel == 'NULL') {
            $this->idpes_responsavel  = $idpes_responsavel;
        }
        $objEscolaridade = new clsEscolaridade($idesco);
        if ($objEscolaridade->detalhe()) {
            $this->idesco = $idesco;
        }
        $objEstadoCivil = new clsEstadoCivil($ideciv);
        if ($objEstadoCivil->detalhe()) {
            $this->ideciv = $ideciv;
        }
        $objPessoaCon = new clsPessoa_($idpes_con);
        if ($objPessoaCon->detalhe()) {
            $this->idpes_con = $idpes_con;
        }
        if (is_numeric($idpais_estrangeiro)) {
            $this->idpais_estrangeiro = $idpais_estrangeiro;
        } elseif ($idpais_estrangeiro == 'NULL') {
            $this->idpais_estrangeiro = $idpais_estrangeiro;
        }
        if (is_numeric($idmun_nascimento)) {
            $this->idmun_nascimento = $idmun_nascimento;
        } elseif ($idmun_nascimento == 'NULL') {
            $this->idmun_nascimento = $idmun_nascimento;
        }
        $objOcupacao = new clsOcupacao($idocup);
        if ($objOcupacao->detalhe()) {
            $this->idocup = $idocup;
        }
        
        $objProfissao = new clsProfissao($ref_cod_profissao);
        if($objProfissao->detalhe()) {
            $this->ref_cod_profissao = $ref_cod_profissao;
        }

        $this->data_nasc                = $data_nasc;
        $this->sexo                     = $sexo;
        $this->data_uniao               = $data_uniao;
        $this->data_obito               = $data_obito;
        $this->nacionalidade            = $nacionalidade;

        // todo Remover variável não usada
        $this->data_chegada_brasil      = $data_chegada_brasil ?? null;
        $this->ultima_empresa           = $ultima_empresa;
        $this->nome_mae                 = $nome_mae;
        $this->nome_pai                 = $nome_pai;
        $this->nome_conjuge             = $nome_conjuge;
        $this->nome_responsavel         = $nome_responsavel;
        $this->ref_cod_sistema          = $ref_cod_sistema;
        $this->ref_cod_religiao         = $ref_cod_religiao;
        $this->ocupacao                 = $ocupacao;
        $this->empresa                  = $empresa;
        $this->ddd_telefone_empresa     = $ddd_telefone_empresa;
        $this->telefone_empresa         = $telefone_empresa;
        $this->pessoa_contato           = $pessoa_contato ?? null;
        $this->renda_mensal             = $renda_mensal;
        $this->data_admissao            = $data_admissao;
        $this->zona_localizacao_censo   = $zona_localizacao_censo;
        $this->tipo_trabalho            = $tipo_trabalho;
        $this->local_trabalho           = $local_trabalho;
        $this->horario_inicial_trabalho = $horario_inicial_trabalho;
        $this->horario_final_trabalho   = $horario_final_trabalho;
        $this->nome_social              = $nome_social;
        $this->observacao               = $observacao;
        $this->ref_cod_profissao        = $ref_cod_profissao;

        $cpf = idFederal2int($cpf);

        if (is_numeric($cpf)) {
            $this->cpf = $cpf;
        }

        $this->justificativa_provisorio = $justificativa_provisorio;
        $this->idpes_cad = $idpes_cad ? $idpes_cad : Auth::id();
        $this->idpes_rev = $idpes_rev ? $idpes_rev : Auth::id();

        $this->tabela = 'fisica';
        $this->schema = 'cadastro';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->idpes) && is_numeric($this->idpes_cad)) {
            $campos = '';
            $valores = '';

            // data_nasc

            $campos  .= ', data_nasc';

            if ($this->data_nasc) {
                $valores .= ", '{$this->data_nasc}'";
            } else {
                $valores .= ', NULL';
            }

            // sexo

            $campos .= ', sexo';

            if ($this->sexo) {
                $valores .= ", '$this->sexo' ";
            } else {
                $valores .= ', NULL';
            }

            if (is_numeric($this->idpes_mae)) {
                $campos .= ', idpes_mae';
                $valores .= ", '$this->idpes_mae' ";
            }
            if (is_numeric($this->idpes_pai)) {
                $campos .= ', idpes_pai';
                $valores .= ", '$this->idpes_pai' ";
            }
            if (is_numeric($this->idpes_responsavel)) {
                $campos .= ', idpes_responsavel';
                $valores .= ", '$this->idpes_responsavel' ";
            }
            if (is_numeric($this->idesco)) {
                $campos .= ', idesco';
                $valores .= ", '$this->idesco' ";
            }
            if (is_numeric($this->ideciv)) {
                $campos .= ', ideciv';
                $valores .= ", '$this->ideciv' ";
            }
            if (is_numeric($this->idpes_con)) {
                $campos .= ', idpes_con';
                $valores .= ", '$this->idpes_con' ";
            }
            if (is_string($this->data_uniao)) {
                $campos .= ', data_uniao';
                $valores .= ", '$this->data_uniao' ";
            }
            if (is_string($this->data_obito)) {
                $campos .= ', data_obito';
                $valores .= ", '$this->data_obito' ";
            }
            if (is_numeric($this->nacionalidade)) {
                $campos .= ', nacionalidade';
                $valores .= ", '$this->nacionalidade' ";
            }
            if (is_numeric($this->idpais_estrangeiro)) {
                $campos .= ', idpais_estrangeiro';
                $valores .= ", '$this->idpais_estrangeiro' ";
            }
            if (is_string($this->data_chegada_brasil)) {
                $campos .= ', data_chegada_brasil';
                $valores .= ", '$this->data_chegada_brasil' ";
            }
            if (is_numeric($this->idmun_nascimento)) {
                $campos .= ', idmun_nascimento';
                $valores .= ", '$this->idmun_nascimento' ";
            }
            if (is_string($this->ultima_empresa)) {
                $campos .= ', ultima_empresa';
                $valores .= ", '$this->ultima_empresa' ";
            }
            if (is_numeric($this->idocup)) {
                $campos .= ', idocup';
                $valores .= ", '$this->idocup' ";
            }
            if (is_string($this->nome_mae)) {
                $campos .= ', nome_mae';
                $valores .= ", '$this->nome_mae' ";
            }
            if (is_string($this->nome_pai)) {
                $campos .= ', nome_pai';
                $valores .= ", '$this->nome_pai' ";
            }
            if (is_string($this->nome_conjuge)) {
                $campos .= ', nome_conjuge';
                $valores .= ", '$this->nome_conjuge' ";
            }
            if (is_string($this->nome_responsavel)) {
                $campos .= ', nome_responsavel';
                $valores .= ", '$this->nome_responsavel' ";
            }
            if (is_string($this->justificativa_provisorio)) {
                $campos .= ', justificativa_provisorio';
                $valores .= ", '$this->justificativa_provisorio' ";
            }
            if (is_numeric($this->ref_cod_sistema)) {
                $campos .= ', ref_cod_sistema';
                $valores .= ", $this->ref_cod_sistema ";
            }
            if (is_numeric($this->ref_cod_religiao)) {
                $campos .= ', ref_cod_religiao';
                $valores .= ", $this->ref_cod_religiao";
            } elseif ($this->ref_cod_religiao !== false) {
                $campos .= ', ref_cod_religiao';
                $valores .= ', NULL';
            }

            if (is_numeric($this->cpf)) {
                $campos .= ', cpf';
                $valores .= ", $this->cpf";
            }

            if (!empty($this->sus)) {
                $campos .= ', sus';
                $valores .= ", '$this->sus'";
            }

            if (is_numeric($this->nis_pis_pasep)) {
                $campos .= ', nis_pis_pasep';
                $valores .= ", $this->nis_pis_pasep";
            }

            if (is_string($this->ocupacao)) {
                $ocupacao = $db->escapeString($this->ocupacao);
                $campos .=  ', ocupacao';
                $valores .= ", '{$ocupacao}'";
            }

            if (is_string($this->empresa)) {
                $empresa = $db->escapeString($this->empresa);
                $campos  .= ', empresa';
                $valores .= ", '{$empresa}'";
            }

            if (is_numeric($this->ddd_telefone_empresa)) {
                $campos  .= ', ddd_telefone_empresa';
                $valores .= ", $this->ddd_telefone_empresa";
            }

            if (is_numeric($this->telefone_empresa)) {
                $campos  .= ', telefone_empresa';
                $valores .= ", $this->telefone_empresa";
            }

            if (is_string($this->pessoa_contato)) {
                $pessoa_contato = $db->escapeString($this->pessoa_contato);
                $campos .=  ', pessoa_contato';
                $valores .= ", '{$pessoa_contato}'";
            }

            if (is_numeric($this->renda_mensal)) {
                $campos  .= ', renda_mensal';
                $valores .= ", $this->renda_mensal";
            }

            if (is_string($this->data_admissao)) {
                $campos .=  ', data_admissao';
                $valores .= ", '$this->data_admissao'";
            }

            if (is_string($this->pais_residencia)) {
                $campos .=  ', pais_residencia';
                $valores .= ", '$this->pais_residencia'";
            }

            if (is_numeric($this->localizacao_diferenciada)) {
                $campos .=  ', localizacao_diferenciada';
                $valores .= ", '$this->localizacao_diferenciada'";
            }

            if ($this->falecido) {
                $campos .=  ', falecido';
                $valores .= ', \'t\'';
            } else {
                $campos .=  ', falecido';
                $valores .= ', \'f\'';
            }

            if (is_numeric($this->zona_localizacao_censo)) {
                $campos  .= ', zona_localizacao_censo';
                $valores .= ", $this->zona_localizacao_censo";
            }

            if (is_numeric($this->tipo_trabalho)) {
                $campos  .= ', tipo_trabalho';
                $valores .= ", $this->tipo_trabalho";
            }

            if (is_string($this->local_trabalho)) {
                $campos  .= ', local_trabalho';
                $valores .= ", '$this->local_trabalho'";
            }

            if (is_string($this->horario_inicial_trabalho) && !empty($this->horario_inicial_trabalho)) {
                $campos  .= ', horario_inicial_trabalho';
                $valores .= ", '$this->horario_inicial_trabalho'";
            } else {
                $campos  .= ', horario_inicial_trabalho';
                $valores .= ', NULL';
            }

            if (is_string($this->horario_final_trabalho) && !empty($this->horario_final_trabalho)) {
                $campos  .= ', horario_final_trabalho';
                $valores .= ", '$this->horario_final_trabalho'";
            } else {
                $campos  .= ', horario_final_trabalho';
                $valores .= ', NULL';
            }

            if (is_string($this->nome_social) && !empty($this->nome_social)) {
                $slug = Str::lower(Str::slug($this->nome_social, ' '));

                $person = LegacyPerson::query()->find($this->idpes);
                $person->slug = "{$slug} {$person->slug}";
                $person->save();
                $person = $db->escapeString($this->nome_social);
                $campos  .= ', nome_social';
                $valores .= ", '{$person}'";
            } else {
                $person = LegacyPerson::query()->find($this->idpes);
                $person->slug = Str::lower(Str::slug($person->nome, ' '));
                $person->save();

                $campos  .= ', nome_social';
                $valores .= ', NULL';
            }

            if (is_string($this->observacao) && $this->observacao != 'NULL') {
                $campos  .= ', observacao';
                $valores .= ", '$this->observacao'";
            }else{
                $campos  .= ', observacao';
                $valores .= ", NULL";
            }

            if(is_numeric($this->ref_cod_profissao)){
                $campos .= ', ref_cod_profissao';
                $valores .= ", '$this->ref_cod_profissao'";
            }


            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (idpes, origem_gravacao, data_cad, operacao, idpes_cad, ref_cod_profissao $campos) VALUES ( '{$this->idpes}', 'M', NOW(), 'I', '$this->idpes_cad' $valores )");

            if ($this->idpes) {
                $detalhe = $this->detalheSimples();
                // salvar cpf como string;
                $detalhe['cpf'] = str_pad((string)$detalhe['cpf'], 11, '0', STR_PAD_LEFT);
            }

            return true;
        }

        return false;
    }

    /**
     * Edita o registro atual
     *
     * @return bool
     */
    public function edita()
    {
        // verifica campos obrigatorios para edicao
        if (is_numeric($this->idpes) && $this->idpes_rev) {
            $set = '';
            $gruda = '';
            if ($this->data_nasc) {
                $set .= " data_nasc = '{$this->data_nasc}'";
                $gruda = ', ';
            }
            if ($this->sexo) {
                $set .= "$gruda sexo = '{$this->sexo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->idpes_mae)) {
                $set .= "$gruda idpes_mae = '{$this->idpes_mae}'";
                $gruda = ', ';
            } elseif ($this->idpes_mae == 'NULL') {
                $set .= "$gruda idpes_mae = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->idpes_pai)) {
                $set .= "$gruda idpes_pai = '{$this->idpes_pai}'";
                $gruda = ', ';
            } elseif ($this->idpes_pai == 'NULL') {
                $set .= "$gruda idpes_pai = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->idpes_responsavel)) {
                $set .= "$gruda idpes_responsavel = {$this->idpes_responsavel}";
                $gruda = ', ';
            } elseif ($this->idpes_responsavel == 'NULL') {
                $set .= "$gruda idpes_responsavel = NULL";
                $gruda = ', ';
            }

            if ($this->idesco) {
                $set .= "$gruda idesco = {$this->idesco}";
                $gruda = ', ';
            }
            if ($this->ideciv) {
                $set .= "$gruda ideciv = {$this->ideciv}";
                $gruda = ', ';
            }
            if ($this->idpes_con) {
                $set .= "$gruda idpes_con = {$this->idpes_con}";
                $gruda = ', ';
            }
            if ($this->data_uniao) {
                $set .= "$gruda data_uniao = '{$this->data_uniao}'";
                $gruda = ', ';
            }
            if ($this->data_obito) {
                $set .= "$gruda data_obito = '{$this->data_obito}'";
                $gruda = ', ';
            }
            if ($this->nacionalidade) {
                $set .= "$gruda nacionalidade = {$this->nacionalidade}";
                $gruda = ', ';
            }
            if ($this->idpais_estrangeiro && $this->nacionalidade != Nacionalidade::BRASILEIRA) {
                $set .= "$gruda idpais_estrangeiro = {$this->idpais_estrangeiro}";
                $gruda = ', ';
            } elseif ($this->idpais_estrangeiro !== false) {
                $set .= "$gruda idpais_estrangeiro = null";
                $gruda = ', ';
            }
            if ($this->data_chegada_brasil) {
                $set .= "$gruda data_chegada_brasil = '{$this->data_chegada_brasil}'";
                $gruda = ', ';
            }
            if ($this->idmun_nascimento) {
                $set .= "$gruda idmun_nascimento = {$this->idmun_nascimento}";
                $gruda = ', ';
            }
            if ($this->ultima_empresa) {
                $set .= "$gruda ultima_empresa = '{$this->ultima_empresa}'";
                $gruda = ', ';
            }
            if ($this->idocup) {
                $set .= "$gruda idocup = '{$this->idocup}'";
                $gruda = ', ';
            }
            if ($this->nome_mae) {
                $set .= "$gruda nome_mae = '{$this->nome_mae}'";
                $gruda = ', ';
            }
            if ($this->nome_pai) {
                $set .= "$gruda nome_pai = '{$this->nome_pai}'";
                $gruda = ', ';
            }
            if ($this->nome_conjuge) {
                $set .= "$gruda nome_conjuge = '{$this->nome_conjuge}'";
                $gruda = ', ';
            }
            if ($this->nome_responsavel) {
                $set .= "$gruda nome_responsavel = '{$this->nome_responsavel}'";
                $gruda = ', ';
            }
            if ($this->justificativa_provisorio) {
                $set .= "$gruda justificativa_provisorio = '{$this->justificativa_provisorio}'";
                $gruda = ', ';
            }
            if ($this->idpes_rev) {
                $set .= "$gruda idpes_rev = '{$this->idpes_rev}'";
                $gruda = ', ';
            }

            if ($this->cpf) {
                $set .= "$gruda cpf = {$this->cpf}";
                $gruda = ', ';
            }

            if ($this->sus) {
                $set .= "$gruda sus = '{$this->sus}'";
                $gruda = ', ';
            } elseif ($this->sus !== false) {
                $set .= "$gruda sus = NULL";
                $gruda = ', ';
            }

            if ($this->nis_pis_pasep) {
                $set .= "$gruda nis_pis_pasep = {$this->nis_pis_pasep}";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_sistema) || $this->ref_cod_sistema == 'NULL') {
                $set .= "$gruda ref_cod_sistema = {$this->ref_cod_sistema}";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_religiao)) {
                $set .= "$gruda ref_cod_religiao = {$this->ref_cod_religiao}";
                $gruda = ', ';
            } elseif ($this->ref_cod_religiao !== false) {
                $set .= "$gruda ref_cod_religiao = NULL";
                $gruda = ', ';
            }

            if (is_string($this->ocupacao)) {
                $set  .= "$gruda ocupacao = '{$this->ocupacao}'";
                $gruda = ', ';
            }

            if (is_string($this->empresa)) {
                $set  .= "$gruda empresa = '{$this->empresa}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ddd_telefone_empresa)) {
                $set    .= "$gruda ddd_telefone_empresa = {$this->ddd_telefone_empresa}";
                $gruda   = ', ';
            }
            if (is_numeric($this->telefone_empresa)) {
                $set     .= "$gruda telefone_empresa = {$this->telefone_empresa}";
                $gruda    = ', ';
            }

            if (is_string($this->pessoa_contato)) {
                $set  .= "$gruda pessoa_contato = '{$this->pessoa_contato}'";
                $gruda = ', ';
            }

            if (is_numeric($this->renda_mensal)) {
                $set     .= "$gruda renda_mensal = {$this->renda_mensal}";
                $gruda    = ', ';
            } else {
                $set .= "$gruda renda_mensal = NULL";
                $gruda = ', ';
            }

            if ($this->data_admissao) {
                $set  .= "$gruda data_admissao = '{$this->data_admissao}'";
                $gruda = ', ';
            } else {
                $set .= "$gruda data_admissao = NULL";
                $gruda = ', ';
            }

            if ($this->pais_residencia) {
                $set  .= "$gruda pais_residencia = '{$this->pais_residencia}'";
                $gruda = ', ';
            }

            if ($this->localizacao_diferenciada) {
                $set  .= "$gruda localizacao_diferenciada = {$this->localizacao_diferenciada} ";
                $gruda = ', ';
            }

            if ($this->falecido) {
                $set  .= "$gruda falecido = 't'";
                $gruda = ', ';
            } else {
                $set .= "$gruda falecido = 'f'";
                $gruda = ', ';
            }

            if (is_numeric($this->zona_localizacao_censo)) {
                $set .= "$gruda zona_localizacao_censo = {$this->zona_localizacao_censo}";
                $gruda = ', ';
            } elseif (is_null($this->zona_localizacao_censo)) {
                $set .= "$gruda zona_localizacao_censo = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_trabalho)) {
                $set .= "$gruda tipo_trabalho = {$this->tipo_trabalho}";
                $gruda = ', ';
            } elseif (is_null($this->tipo_trabalho) || $this->tipo_trabalho == false) {
                $set .= "$gruda tipo_trabalho = NULL";
                $gruda = ', ';
            }

            if (is_string($this->local_trabalho) && !empty($this->local_trabalho)) {
                $set .= "$gruda local_trabalho = '{$this->local_trabalho}'";
                $gruda = ', ';
            }

            if (is_string($this->horario_inicial_trabalho) && !empty($this->horario_inicial_trabalho)) {
                $set .= "$gruda horario_inicial_trabalho = '{$this->horario_inicial_trabalho}'";
                $gruda = ', ';
            } elseif (is_null($this->horario_inicial_trabalho)) {
                $set .= "$gruda horario_inicial_trabalho = NULL";
                $gruda = ', ';
            }

            if (is_string($this->horario_final_trabalho) && !empty($this->horario_final_trabalho)) {
                $set .= "$gruda horario_final_trabalho = '{$this->horario_final_trabalho}'";
                $gruda = ', ';
            } elseif (is_null($this->horario_final_trabalho)) {
                $set .= "$gruda horario_final_trabalho = NULL";
                $gruda = ', ';
            }
            if(is_numeric($this->ref_cod_profissao)){
                $set .= "$gruda ref_cod_profissao = '{$this->ref_cod_profissao}'";
                $gruda = ', ';
               
            }

            if (is_string($this->nome_social)) {
                $slug = Str::lower(Str::slug($this->nome_social, ' '));

                $person = LegacyPerson::query()->find($this->idpes);
                $person->slug = trim("{$slug} {$person->slug}");
                $person->save();

                $set .= "$gruda nome_social = '{$this->nome_social}'";
            }

            if (is_string($this->observacao) && $this->observacao != 'NULL') {
                $set .= "$gruda observacao = '{$this->observacao}'";
                $gruda = ', ';
            }else{
                $set .= "$gruda observacao = NULL";
                $gruda = ', ';
            }
            

            if ($set) {
                $set = "SET {$set}";
                $db = new clsBanco();
                $detalheAntigo = $this->detalheSimples();

                $detalheAntigo['cpf'] = str_pad((string)$detalheAntigo['cpf'], 11, '0', STR_PAD_LEFT);
                $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE idpes = '$this->idpes'");

                $detalheAtual = $this->detalheSimples();
                $detalheAtual['cpf'] = str_pad((string)$detalheAtual['cpf'], 11, '0', STR_PAD_LEFT);

                return true;
            }
        }

        return false;
    }

    /**
     * Remove o registro atual
     *
     * @return bool
     */
    public function exclui()
    {
        if (is_numeric($this->idpes)) {
            $detalheAntigo = $this->detalheSimples();

            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idpes = {$this->idpes}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_idpes = false, $data_data_nasc = false, $str_sexo = false, $int_idpes_mae = false, $int_idpes_pai = false, $int_idpes_responsavel = false, $int_idesco = false, $int_ideciv = false, $int_idpes_con = false, $data_data_uniao = false, $data_data_obito = false, $int_nacionalidade = false, $int_idpais_estrangeiro = false, $data_data_chagada_brasil = false, $int_idmun_nascimento = false, $str_ultima_empresa = false, $int_idocup = false, $str_nome_mae = false, $str_nome_pai = false, $str_nome_conjuge = false, $str_nome_responsavel = false, $str_justificativa_provisorio = false, $str_ordenacao = false, $int_limite_ini = 0, $int_limite_qtd = 20, $arrayint_idisin = false, $arrayint_idnotin = false, $str_data_nasc_ini = false, $str_data_nasc_fim = false, $int_mes_aniversario = false, $int_ref_cod_sistema = false, $int_cpf = false, $ref_cod_profissao = false)
    {
        $whereAnd = 'WHERE ';

        if (is_numeric($int_idpes)) {
            $where .= "{$whereAnd}idpes = '$int_idpes'";
            $whereAnd = ' AND ';
        } elseif (is_string($int_idpes)) {
            $where .= "{$whereAnd}idpes IN ({$int_idpes})";
            $whereAnd = ' AND ';
        }

        if (is_string($data_data_nasc)) {
            $where .= "{$whereAnd}data_nasc = '$data_data_nasc'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_mes_aniversario)) {
            $where .= "{$whereAnd} EXTRACT (MONTH FROM data_nasc) = '$int_mes_aniversario'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sexo)) {
            $where .= "{$whereAnd}sexo = '$str_sexo'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_mae)) {
            $where .= "{$whereAnd}idpes_mae =  '$int_idpes_mae'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_pai)) {
            $where .= "{$whereAnd}idpes_pai =  '$int_idpes_pai'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_responsavel)) {
            $where .= "{$whereAnd}idpes_responsavel =  '$int_idpes_responsavel'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idesco)) {
            $where .= "{$whereAnd}idesco =  '$int_idesco'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ideciv)) {
            $where .= "{$whereAnd}ideciv =  '$int_ideciv'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpes_con)) {
            $where .= "{$whereAnd}idpes_con =  '$int_idpes_con'";
            $whereAnd = ' AND ';
        }
        if (is_string($data_data_uniao)) {
            $where .= "{$whereAnd}data_uniao =  '$data_data-uniao'";
            $whereAnd = ' AND ';
        }
        if (is_string($data_data_obito)) {
            $where .= "{$whereAnd}data_obito =  '$data_data_obito'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nacionalidade)) {
            $where .= "{$whereAnd}nacionalidade =  '$int_nacionalidade'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpais_estrangeiro)) {
            $where .= "{$whereAnd}idpais_estrangeiro =  '$int_idpais_estrangeiro'";
            $whereAnd = ' AND ';
        }
        if (is_string($data_data_chegada_brasil)) {
            $where .= "{$whereAnd}data_chegada_brasil =  '$data_data_chegada_brasil'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idmun_nascimento)) {
            $where .= "{$whereAnd}idmun_nascimento =  '$int_idmun_nascimento'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_ultima_empresa)) {
            $where .= "{$whereAnd}ultima_empresa =  '$str_ultima_empresa'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idocup)) {
            $where .= "{$whereAnd}idocup = '$int_idocup'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome_mae)) {
            $where .= "{$whereAnd}nome_mae = '$str_nome_mae'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome_pai)) {
            $where .= "{$whereAnd}nome_pai = '$str_nome_pai'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome_conjuge)) {
            $where .= "{$whereAnd}nome_conjuge = '$str_nome_conjuge'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome_responsavel)) {
            $where .= "{$whereAnd}nome_responsavel = '$str_nome_responsavel'";
            $whereAnd = ' AND ';
        }
        if (is_string($int_ref_cod_sistema)) {
            $where .= "{$whereAnd}ref_cod_sistema = '$int_ref_cod_sistema'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_justificativa_provisorio)) {
            $where .= "{$whereAnd}justificativa_provisorio = '$str_justificativa_provisorio'";
        }

        $int_cpf = idFederal2int($int_cpf);

        if (is_numeric($int_cpf)) {
            $where .= "{$whereAnd}cpf like '%$int_cpf%'";
        }
        if(is_numeric($this->ref_cod_profissao)){
           $where .= "{$whereAnd} ref_cod_profissao = '$ref_cod_profissao'";
        }

        if (is_array($arrayint_idisin)) {
            $ok = true;
            foreach ($arrayint_idisin as $val) {
                if (!is_numeric($val)) {
                    $ok = false;
                }
            }
            if ($ok) {
                $where .= "{$whereAnd}idpes IN ( " . implode(',', $arrayint_idisin) . ' )';
                $whereAnd = ' AND ';
            }
        }

        if (is_array($arrayint_idnotin)) {
            $ok = true;
            foreach ($arrayint_idnotin as $val) {
                if (!is_numeric($val)) {
                    $ok = false;
                }
            }
            if ($ok) {
                $where .= "{$whereAnd}idpes NOT IN ( " . implode(',', $arrayint_idnotin) . ' )';
                $whereAnd = ' AND ';
            }
        }

        if (is_string($str_data_nasc_ini)) {
            $dia = substr($str_data_nasc_ini, 8, 2);
            $mes = substr($str_data_nasc_ini, 5, 2);
            $ano = substr($str_data_nasc_ini, 0, 4);

            $operador = $str_data_nasc_fim ? '>=' : '=';

            if ($dia != '' && $dia != '00') {
                $where .= "{$whereAnd}EXTRACT(DAY FROM data_nasc) {$operador} '{$dia}'";
                $whereAnd = ' AND ';
            }

            if ($mes != '' && $mes != '00') {
                $where .= "{$whereAnd}EXTRACT(MONTH FROM data_nasc) {$operador} '{$mes}'";
                $whereAnd = ' AND ';
            }

            if ($ano != '' && $ano != '0000') {
                $where .= "{$whereAnd}EXTRACT(YEAR FROM data_nasc) {$operador} '{$ano}'";
                $whereAnd = ' AND ';
            }
        }

        if (is_string($str_data_nasc_fim)) {
            $dia = substr($str_data_nasc_fim, 8, 2);
            $mes = substr($str_data_nasc_fim, 5, 2);
            $ano = substr($str_data_nasc_fim, 0, 4);

            if ($dia != '' && $dia != '00') {
                $where .= "{$whereAnd}EXTRACT(DAY FROM data_nasc) <= '{$dia}'";
                $whereAnd = ' AND ';
            }

            if ($mes != '' && $mes != '00') {
                $where .= "{$whereAnd}EXTRACT(MONTH FROM data_nasc) <= '{$mes}'";
                $whereAnd = ' AND ';
            }

            if ($ano != '' && $ano != '0000') {
                $where .= "{$whereAnd}EXTRACT(YEAR FROM data_nasc) <= '{$ano}'";
                $whereAnd = ' AND ';
            }
        }

        $orderBy = '';
        if (is_string($str_ordenacao)) {
            $orderBy = "ORDER BY $str_ordenacao";
        }
        $limit = '';
        if (is_numeric($int_limite_ini) &&                       is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT idpes, data_nasc, sexo, idpes_mae, idpes_pai, idpes_responsavel, idesco, ideciv, idpes_con, data_uniao, data_obito, nacionalidade, idpais_estrangeiro, data_chegada_brasil, idmun_nascimento, ultima_empresa, idocup, nome_mae, nome_pai, nome_conjuge, nome_responsavel, justificativa_provisorio, ref_cod_religiao, ref_cod_profissao FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");
        $resultado = [];
        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['idesco'] =  $tupla['idesco'];
            $tupla['ideciv'] = new clsEstadoCivil($tupla['ideciv']);
            $tupla['idocup'] = new clsOcupacao($tupla['idocup']);
            
            $tupla['total'] = $total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os detalhes do objeto
     *
     * @return Array
     */
    public function detalhe()
    {
        if ($this->idpes) {
            $db = new clsBanco();
            $db->Consulta("SELECT fisica.idpes, data_nasc, sexo, idpes_mae, idpes_pai, idpes_responsavel, idesco, ideciv, idpes_con, data_uniao, data_obito, nacionalidade, idpais_estrangeiro, data_chegada_brasil, idmun_nascimento, ultima_empresa, idocup, nome_mae, nome_pai, nome_conjuge, nome_responsavel, justificativa_provisorio, cpf , ref_cod_religiao, sus, nis_pis_pasep, ocupacao, empresa, ddd_telefone_empresa, telefone_empresa, pessoa_contato, data_admissao, renda_mensal, falecido, ativo, data_exclusao, zona_localizacao_censo, nome, nome_social, pais_residencia, localizacao_diferenciada, observacao, ref_cod_profissao FROM {$this->schema}.{$this->tabela}
            INNER JOIN cadastro.pessoa ON (pessoa.idpes = fisica.idpes) 
             WHERE fisica.idpes = {$this->idpes}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['idesco'] = new clsEscolaridade($tupla['idesco']);
                $tupla['ideciv'] = new clsEstadoCivil($tupla['ideciv']);
                $tupla['idocup'] = new clsOcupacao($tupla['idocup']);

                return $tupla;
            }
        } elseif ($this->cpf) {
            $db = new clsBanco();
            $db->Consulta("SELECT idpes, data_nasc, sexo, idpes_mae, idpes_pai, idpes_responsavel, idesco, ideciv, idpes_con, data_uniao, data_obito, nacionalidade, idpais_estrangeiro, data_chegada_brasil, idmun_nascimento, ultima_empresa, idocup, nome_mae, nome_pai, nome_conjuge, nome_responsavel, justificativa_provisorio,cpf, ref_cod_religiao, ocupacao, empresa, ddd_telefone_empresa, telefone_empresa, pessoa_contato, data_admissao, renda_mensal, ativo, data_exclusao, zona_localizacao_censo, ref_cod_profissao FROM {$this->schema}.{$this->tabela} WHERE cpf = '{$this->cpf}'");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['idesco'] = new clsEscolaridade($tupla['idesco']);
                $tupla['ideciv'] = new clsEstadoCivil($tupla['ideciv']);
                $tupla['idocup'] = new clsOcupacao($tupla['idocup']);

                return $tupla;
            }
        }

        return false;
    }

    public function detalheCPF()
    {
        if (!is_numeric($this->cpf)) {
            return false;
        }

        $db = new clsBanco();
        $db->Consulta("SELECT idpes, cpf FROM {$this->schema}.{$this->tabela} WHERE cpf = {$this->cpf}");
        if ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            return $tupla;
        }

        return false;
    }

    public function detalheSimples()
    {
        if (is_numeric($this->idpes)) {
            $sql = "SELECT * FROM {$this->schema}.{$this->tabela} WHERE idpes = '{$this->idpes}' AND ativo = 1;";

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function getIdade($data_nasc)
    {
        if (is_string($data_nasc)) {
            $db = new clsBanco();
            $db->Consulta("SELECT extract( year FROM age( NOW(),'{$data_nasc}') )");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla['date_part'];
            }
        }

        return false;
    }
}
