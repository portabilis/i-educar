<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 *///die();
use Illuminate\Support\Facades\Session;

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Cadastra Pessoa!" );
        $this->processoAp         = "0";
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsCadastro
{
    /**
     * Atributos recebidos por GET
     *
     * @var unknown_type
     */
    var $pessoa;
    var $ref_cod_sistema;
    var $pessoa_cpf;

    /**
     * Atributos da pessoa
     *
     * @var unknown_type
     */
    var $razao_social;
    var $fantasia;
    var $capital_social;
    var $insc_est;
    var $cod_pessoa_fj;
    var $nm_pessoa;
    var $id_federal;
    var $cidade;
    var $endereco;
    var $cep;
    var $cep_;
    var $logradouro;
    var $idlog;
    var $idtlog;
    var $idbai;
    var $bairro;
    var $sigla_uf;
    var $ddd_telefone_1;
    var $telefone_1;
    var $ddd_telefone_2;
    var $telefone_2;
    var $ddd_telefone_mov;
    var $telefone_mov;
    var $ddd_telefone_fax;
    var $telefone_fax;
    var $email;
    var $http;
    var $data_nasc;
    var $sexo;
    var $busca_pessoa;
    var $complemento;
    var $apartamento;
    var $bloco;
    var $andar;
    var $numero;
    var $retorno;
    var $vazio;
    var $letra;
    var $rg;
    var $data_exp_rg;
    var $sigla_uf_exp_rg;
    var $idorg_exp_rg;

    function Inicializar()
    {
        $this->pessoa              = @$_GET["pessoa"];
        if ( $_GET["cod"] )
            $this->cod_pessoa_fj       = @$_GET["cod"];
        $this->ref_cod_sistema     = @$_GET["ref_cod_sistema"];
        $this->pessoa_cpf          = @$_GET["pessoa_cpf"];
        if ( $_POST["pessoa"] )
            $this->pessoa     = $_POST["pessoa"];
        if ( $_POST["pessoa_cpf"] )
            $this->pessoa_cpf = $_POST["pessoa_cpf"];
        $this->id_federal          = @$_POST["id_federal"];
        if ( $_POST["vazio"] ) {
            $this->vazio      = $_POST["vazio"];
        }
        else {
            $this->vazio      = "true";
        }

        $this->retorno             = "Novo";

        if($this->id_federal != null && (!is_numeric($this->cod_pessoa_fj) || $this->cod_pessoa_fj ==0 ))
        {
            $obj_fisica = new clsFisica(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,idFederal2int($this->id_federal));
            $detalhe = $obj_fisica->detalhe();
            if( $detalhe )
                $this->cod_pessoa_fj = $detalhe['idpes'];
            else {
                $obj_juridica = new clsJuridica(false, idFederal2int($this->id_federal));
                $det_jur = $obj_juridica->detalhe();
                if ($det_jur)
                    $this->cod_pessoa_fj = $det_jur["idpes"];
            }
        }

        if ( $this->pessoa == "F" || $this->pessoa == "J" ) {
            if ( is_numeric( $this->cod_pessoa_fj ) && $this->cod_pessoa_fj != 0 ) {
                if ( $this->pessoa == "F" ) {
                    $obj_fisica          = new clsPessoaFisica();
                    list( $this->nm_pessoa
                         ,$this->id_federal
                         ,$this->data_nasc
                         ,$this->ddd_telefone_1
                         ,$this->telefone_1
                         ,$this->ddd_telefone_2
                         ,$this->telefone_2
                         ,$this->ddd_telefone_mov
                         ,$this->telefone_mov
                         ,$this->ddd_telefone_fax
                         ,$this->telefone_fax
                         ,$this->email
                         ,$this->http
                         ,$this->pessoa
                         ,$this->sexo
                         ,$this->cidade
                         ,$this->bairro
                         ,$this->logradouro
                         ,$this->cep
                         ,$this->idlog
                         ,$this->idbai
                         ,$this->idtlog
                         ,$this->sigla_uf
                         ,$this->complemento
                         ,$this->numero
                         ,$this->bloco
                         ,$this->apartamento
                         ,$this->andar) = $obj_fisica->queryRapida( $this->cod_pessoa_fj
                                                                    ,"nome"
                                                                    ,"cpf"
                                                                    ,"data_nasc"
                                                                    ,"ddd_1"
                                                                    ,"fone_1"
                                                                    ,"ddd_2"
                                                                    ,"fone_2"
                                                                    ,"ddd_mov"
                                                                    ,"fone_mov"
                                                                    ,"ddd_fax"
                                                                    ,"fone_fax"
                                                                    ,"email"
                                                                    ,"url"
                                                                    ,"tipo"
                                                                    ,"sexo"
                                                                    ,"cidade"
                                                                    ,"bairro"
                                                                    ,"logradouro"
                                                                    ,"cep"
                                                                    ,"idlog"
                                                                    ,"idbai"
                                                                    ,"idtlog"
                                                                    ,"sigla_uf"
                                                                    ,"complemento"
                                                                    ,"numero"
                                                                    ,"bloco"
                                                                    ,"apartamento"
                                                                    ,"andar" );
                    $this->cep      = int2Cep( $this->cep );

                    $obj_endereco = new clsPessoaEndereco( $this->cod_pessoa_fj );
                    $det_endereco = $obj_endereco->detalhe();
                    if ( $det_endereco ) {
                        $obj_cep           = $det_endereco["cep"];
                        $det_cep           = $obj_cep->detalhe();
                        $this->cep         = $det_cep["cep"];
                        $obj_idlog         = $det_endereco["idlog"];
                        $det_idlog         = $obj_idlog->detalhe();
                        $obj_idlog         = $det_idlog["idlog"];
                        $det_idlog         = $obj_idlog->detalhe();
                        $this->idlog       = $det_idlog["idlog"];
                        $this->numero      = $det_endereco["numero"];
                        $this->letra       = $det_endereco["letra"];
                        $this->complemento = $det_endereco["complemento"];
                        $obj_idbai         = $det_endereco["idbai"];
                        $det_idbai         = $obj_idbai->detalhe();
                        $this->idbai       = $det_idbai["idbai"];
                        $this->bloco       = $det_endereco["bloco"];
                        $this->andar       = $det_endereco["andar"];
                        $this->apartamento = $det_endereco["apartamento"];
                    }
                }
                elseif ( $this->pessoa == "J" ) {
                    $obj_juridica           = new clsPessoaJuridica( $this->cod_pessoa_fj );
                    $det_juridica           = $obj_juridica->detalhe();
                    $this->email            = $det_juridica['email'];
                    $this->url              = $det_juridica['url'];
                    $this->insc_est         = $det_juridica['insc_estadual'];
                    $this->capital_social   = $det_juridica['capital_social'];
                    $this->razao_social     = $det_juridica['nome'];
                    $this->fantasia         = $det_juridica['fantasia'];
                    $this->id_federal       = int2CNPJ( $det_juridica['cnpj'] );
                    $this->ddd_telefone_1   = $det_juridica['ddd_1'];
                    $this->telefone_1       = $det_juridica['fone_1'];
                    $this->ddd_telefone_2   = $det_juridica['ddd_2'];
                    $this->telefone_2       = $det_juridica['fone_2'];
                    $this->ddd_telefone_mov = $det_juridica['ddd_mov'];
                    $this->telefone_mov     = $det_juridica['fone_mov'];
                    $this->ddd_telefone_fax = $det_juridica['ddd_fax'];
                    $this->telefone_fax     = $det_juridica['fone_fax'];
                    $this->cidade           = $det_juridica['cidade'];
                    $this->bairro           = $det_juridica['bairro'];
                    $this->logradouro       = $det_juridica['logradouro'];
                    $this->cep              = int2CEP( $det_juridica['cep'] );
                    $this->idlog            = $det_juridica['idlog'];
                    $this->idbai            = $det_juridica['idbai'];
                    $this->idtlog           = $det_juridica['idtlog'];
                    $this->sigla_uf         = $det_juridica['sigla_uf'];
                    $this->complemento      = $det_juridica['complemento'];
                    $this->numero           = $det_juridica['numero'];
                    $this->letra            = $det_juridica['letra'];

                    $obj_endereco = new clsPessoaEndereco( $this->cod_pessoa_fj );
                    $det_endereco = $obj_endereco->detalhe();
                    if ( $det_endereco ) {
                        $obj_cep           = $det_endereco["cep"];
                        $det_cep           = $obj_cep->detalhe();
                        $this->cep         = $det_cep["cep"];
                        $obj_idlog         = $det_endereco["idlog"];
                        $det_idlog         = $obj_idlog->detalhe();
                        $obj_idlog         = $det_idlog["idlog"];
                        $det_idlog         = $obj_idlog->detalhe();
                        $this->idlog       = $det_idlog["idlog"];
                        $this->numero      = $det_endereco["numero"];
                        $this->letra       = $det_endereco["letra"];
                        $this->complemento = $det_endereco["complemento"];
                        $obj_idbai         = $det_endereco["idbai"];
                        $det_idbai         = $obj_idbai->detalhe();
                        $this->idbai       = $det_idbai["idbai"];
                        $this->bloco       = $det_endereco["bloco"];
                        $this->andar       = $det_endereco["andar"];
                        $this->apartamento = $det_endereco["apartamento"];
                    }
                }
                $this->retorno  = "Editar";
                $this->fexcluir = false;
            }
            elseif($this->id_federal == null) {
                $this->retorno = '';
            }
        }
        else {
            $this->retorno = '';
        }

        if ( !( $this->vazio == "true" ) && !is_numeric( $this->cod_pessoa_fj ) )
            $this->retorno = "Novo";
        elseif ( is_numeric( $this->cod_pessoa_fj ) )
            $this->retorno = "Editar";
        return $this->retorno;
    }

    function Gerar()
    {
        if ( $_POST["pessoa"] )
            $this->pessoa     = $_POST["pessoa"];
        if ( $_POST["pessoa_cpf"] )
            $this->pessoa_cpf = $_POST["pessoa_cpf"];
        if ( $_POST["vazio"] )
            $this->vazio      = $_POST["vazio"];

        if ( $this->pessoa == "FJ" ) {
            $this->campoRadio( "pessoa", "Tipo da pessoa:", array( "F" => "Física", "J" => "Jurídica" ), "F" );
            $this->campoOculto( "pessoa_cpf", $this->pessoa_cpf );
            $this->campoOculto( "vazio", "true" );
            $this->botao_enviar = false;
            $this->array_botao = array( "<< Voltar", "Cancelar", "Avançar >>" );
            $this->array_botao_url_script = array( "go( 'pesquisa_pessoa_lst.php' );", "window.parent.fechaExpansivel( 'div_dinamico_' + ( parent.DOM_divs.length * 1 - 1 ) );", "acao()" );
        }
        elseif ( $this->pessoa == "F" )
        {
            if ( !$this->id_federal && !$this->cod_pessoa_fj && ( $this->vazio == "true" ) )
            {
                if ( $this->pessoa_cpf == "N" )
                {
                    $this->campoCpf( "id_federal", "CPF", $this->id_federal, false );
                    $this->campoOculto( "vazio", "false" );
                }
                else
                {
                    $this->campoCpf( "id_federal", "CPF", $this->id_federal, true );
                    $this->campoOculto( "vazio", "true" );
                }
                $this->campoOculto( "pessoa", $this->pessoa );
                $this->campoOculto( "pessoa_cpf", $this->pessoa_cpf );
                $this->botao_enviar = false;
                $this->array_botao = array( "<< Voltar", "Cancelar", "Avançar >>" );
                $this->array_botao_url_script = array( "go( 'pesquisa_pessoa_lst.php' );", "window.parent.fechaExpansivel( 'div_dinamico_' + ( parent.DOM_divs.length * 1 - 1 ) );", "acao()" );
            }
            else {
                if ( !$this->cod_pessoa_fj ) {
                    $this->id_federal = idFederal2int( $this->id_federal );

                    $obj_pfs = new clsFisica( false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->id_federal  );
                    $det_pfs = $obj_pfs->detalhe();
                    if ( $det_fis ) {
                        $obj_pes = new clsPessoaFj( $det_pfs["idpes"] );
                        $det_pes = $obj_pes->detalhe();
                        if (  $det_pes ) {
                            $this->cod_pessoa_fj    = $det_pes["idpes"];
                            $this->nm_pessoa        = $det_pes["nome"];
                            $this->ddd_telefone_1   = $det_pes["ddd_1"];
                            $this->telefone_1       = $det_pes["fone_1"];
                            $this->ddd_telefone_2   = $det_pes["ddd_2"];
                            $this->telefone_2       = $det_pes["fone_2"];
                            $this->ddd_telefone_mov = $det_pes["ddd_mov"];
                            $this->telefone_mov     = $det_pes["fone_mov"];
                            $this->ddd_telefone_fax = $det_pes["ddd_fax"];
                            $this->telefone_fax     = $det_pes["fone_fax"];
                            $this->email            = $det_pes["email"];
                            $this->http             = $det_pes["url"];
                            $this->pessoa           = $det_pes["tipo"];
                            $this->sexo             = $det_pfs["sexo"];
                            $this->cidade           = $det_pes["cidade"];
                            $this->bairro           = $det_pes["bairro"];
                            $this->logradouro       = $det_pes["logradouro"];
                            $this->cep              = int2CEP( $det_pes["cep"] );
                            $this->idlog            = $det_pes["idlog"];
                            $this->idbai            = $det_pes["idbai"];
                            $this->idtlog           = $det_pes["idtlog"];
                            $this->sigla_uf         = $det_pes["sigla_uf"];
                            $this->complemento      = $det_pes["complemento"];
                            $this->numero           = $det_pes["numero"];
                            $this->bloco            = $det_pes["bloco"];
                            $this->apartamento      = $det_pes["apartamento"];
                            $this->andar            = $det_pes["andar"];

                            $this->fexcluir         = false;
                        }
                    }
                }
                $this->campoOculto( "ref_cod_sistema", $this->ref_cod_sistema );
                $this->campoOculto( "pessoa", $this->pessoa );
                $this->campoOculto( "cod_pessoa_fj", $this->cod_pessoa_fj );
                $this->campoTexto( "nm_pessoa", "Nome",  $this->nm_pessoa, "50", "255", true );
                if($this->id_federal) {
                    $this->campoOculto( "id_federal", $this->id_federal );
                    $this->campoRotulo( "id_federal_", "CPF", int2CPF( $this->id_federal ) );
                }
                else {
                    $this->campoCpf( "id_federal", "CPF", "", false );
                }

                $options = array(
                  'required'    => $required,
                  'label'       => 'RG / Data emissão',
                  'placeholder' => 'Documento identidade',
                  'value'       => $documentos['rg'],
                  'max_length'  => 25,
                  'size'        => 27,
                  'inline'      => true
                );

                $this->inputsHelper()->text('rg', $options);


                // data emissão rg

                $options = array(
                  'required'    => false,
                  'label'       => '',
                  'placeholder' => 'Data emissão',
                  'value'       => $documentos['data_exp_rg'],
                  'size'        => 19
                );

                $this->inputsHelper()->date('data_emissao_rg', $options);


                // orgão emissão rg

                $selectOptions = array( null => 'Orgão emissor' );
                $orgaos        = new clsOrgaoEmissorRg();
                $orgaos        = $orgaos->lista();

                foreach ($orgaos as $orgao)
                  $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];

                $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

                $options = array(
                  'required'  => false,
                  'label'     => '',
                  'value'     => $documentos['idorg_exp_rg'],
                  'resources' => $selectOptions,
                  'inline'    => true
                );

                $this->inputsHelper()->select('orgao_emissao_rg', $options);


                // uf emissão rg

                $options = array(
                  'required' => false,
                  'label'    => '',
                  'value'    => $documentos['sigla_uf_exp_rg']
                );

                $helperOptions = array(
                  'attrName' => 'uf_emissao_rg'
                );

                $this->inputsHelper()->uf($options, $helperOptions);

                if( $this->data_nasc )
                {
                    $this->data_nasc = dataFromPgToBr($this->data_nasc);
                }
                $this->campoData( "data_nasc", "Data de Nascimento", $this->data_nasc );

                $lista_sexos = array();
                $lista_sexos[""]  = "Escolha uma op&ccedil;&atilde;o...";
                $lista_sexos["M"] = "Masculino";
                $lista_sexos["F"] = "Feminino";
                $this->campoLista( "sexo", "Sexo", $lista_sexos, $this->sexo );

                // Detalhes do Endereço
                $objTipoLog = new clsTipoLogradouro();
                $listaTipoLog = $objTipoLog->lista();
                $listaTLog = array( "" => "Selecione" );
                if ( $listaTipoLog ) {
                    foreach ( $listaTipoLog as $tipoLog ) {
                        $listaTLog[$tipoLog['idtlog']] = $tipoLog['descricao'];
                    }
                }

                $objUf = new clsUf();
                $listauf = $objUf->lista();
                $listaEstado = array( "" => "Selecione" );
                if ( $listauf ) {
                    foreach ( $listauf as $uf ) {
                        $listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
                    }
                }

                $this->campoOculto( "idbai", $this->idbai );
                $this->campoOculto( "idlog", $this->idlog );

                if ( ( $this->idlog && $this->idbai ) || ( $this->retorno == 'Novo' ) )
                {
                    $this->campoOculto( "cep", $this->cep );
                    $this->campoCep( "cep_", "CEP", int2CEP( $this->cep ), true, "-", "&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f( 'pesquisa_cep_lst.php', 'enderecos' )\" style=\"cursor: hand;\">", true );
                    $this->campoLista( "idtlog", "Tipo Logradouro", $listaTLog, $this->idtlog, false, false, false, false, true );
                    $this->campoTextoInv( "logradouro", "Logradouro", $this->logradouro, "50", "255", true );
                    $this->campoTextoInv( "cidade", "Cidade", $this->cidade, "50", "255", true );
                    $this->campoTextoInv( "bairro", "Bairro", $this->bairro, "50", "255", true );
                    $this->campoLista( "sigla_uf", "Estado", $listaEstado, $this->sigla_uf, "", false, "", "", true );
                }
                else
                {
                    $this->campoOculto( "cep", $this->cep );
                    $this->campoCep( "cep_", "CEP", int2CEP( $this->cep ), true, "-", "&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f( 'pesquisa_cep_lst.php', 'enderecos' )\" style=\"cursor: hand;\">", false );
                    $this->campoLista( "idtlog", "Tipo Logradouro", $listaTLog, $this->idtlog, false, false, false, false, false );
                    $this->campoTexto( "logradouro", "Logradouro", $this->logradouro, "50", "255", false );
                    $this->campoTexto( "cidade", "Cidade", $this->cidade, "50", "255", false );
                    $this->campoTexto( "bairro", "Bairro", $this->bairro, "50", "255", false );
                    $this->campoLista( "sigla_uf", "Estado", $listaEstado, $this->sigla_uf, "", false, "", "", false );
                }
                $this->campoTexto( "complemento", "Complemento", $this->complemento, "50", "50", false );
                $this->campoTexto( "numero", "Número", $this->numero, "10", "10", false );
                $this->campoTexto( "letra", "Letra", $this->letra, "1", "1", false );
                $this->campoTexto( "apartamento", "Número Apartamento", $this->apartamento, "6", "6", false );
                $this->campoTexto( "bloco", "Bloco", $this->bloco, "20", "20", false );
                $this->campoTexto( "andar", "Andar", $this->andar, "2", "2", false );
                $this->campoNumero( "ddd_telefone_1", "DDD Telefone 1",  $this->ddd_telefone_1, "3", "2", false );
                $this->campoNumero( "telefone_1", "Telefone 1",  $this->telefone_1, "10", "15", false );
                $this->campoNumero( "ddd_telefone_2", "DDD Telefone 2",  $this->ddd_telefone_2, "3", "2", false );
                $this->campoNumero( "telefone_2", "Telefone",  $this->telefone_2, "10", "15", false );
                $this->campoNumero( "ddd_telefone_mov", "DDD Celular",  $this->ddd_telefone_mov, "3", "2", false );
                $this->campoNumero( "telefone_mov", "Celular",  $this->telefone_mov, "10", "15", false );
                $this->campoNumero( "ddd_telefone_fax", "DDD Fax",  $this->ddd_telefone_fax, "3", "2", false );
                $this->campoNumero( "telefone_fax", "Fax",  $this->telefone_fax, "10", "15", false );

                $this->campoTexto( "http", "Site",  $this->http, "50", "255", false );
                $this->campoTexto( "email", "E-mail",  $this->email, "50", "255", false );

                if( is_numeric( $this->cod_pessoa_fj ) != 0 && $this->cod_pessoa_fj )
                {
                    $this->campoRotulo( "documentos", "<b><i>Documentos</i></b>", "<a href='#' onclick=\" openPage( 'adicionar_documentos_cad.php?id_pessoa={$this->cod_pessoa_fj}', '400', '400', 'yes', '10', '10' ); \"><img src='imagens/nvp_bot_ad_doc.png' border='0'></a>" );
                }
            }
        }
        elseif ( $this->pessoa == "J" ) {
            if ( !$this->id_federal && !$this->cod_pessoa_fj ) {
                $this->campoCnpj( "id_federal", "CNPJ", $this->id_federal, true );
                $this->campoOculto( "pessoa", $this->pessoa );
                $this->botao_enviar = false;
                $this->array_botao = array( "<< Voltar", "Cancelar", "Avançar >>" );
                $this->array_botao_url_script = array( "go( 'pesquisa_pessoa_lst.php' );", "window.parent.fechaExpansivel( 'div_dinamico_' + ( parent.DOM_divs.length * 1 - 1 ) );", "acao()" );
            }
            else {
                if ( !$this->cod_pessoa_fj ) {
                    $this->id_federal = idFederal2int( $this->id_federal );
                    $obj_pfs = new clsJuridica( false, $this->id_federal );
                    $det_pfs = $obj_pfs->detalhe();
                    if ( $obj_pfs ) {
                        $obj_pes = new clsPessoaFj( $det_pfs["idpes"] );
                        $det_pes = $obj_pes->detalhe();
                        if (  $det_pes ) {
                            $this->cod_pessoa_fj    = $det_pes["idpes"];
                            $this->nm_pessoa        = $det_pes["nome"];
                            $this->ddd_telefone_1   = $det_pes["ddd_1"];
                            $this->telefone_1       = $det_pes["fone_1"];
                            $this->ddd_telefone_2   = $det_pes["ddd_2"];
                            $this->telefone_2       = $det_pes["fone_2"];
                            $this->ddd_telefone_mov = $det_pes["ddd_mov"];
                            $this->telefone_mov     = $det_pes["fone_mov"];
                            $this->ddd_telefone_fax = $det_pes["ddd_fax"];
                            $this->telefone_fax     = $det_pes["fone_fax"];
                            $this->email            = $det_pes["email"];
                            $this->http             = $det_pes["url"];
                            $this->pessoa           = $det_pes["tipo"];
                            $this->sexo             = $det_pfs["sexo"];
                            $this->cidade           = $det_pes["cidade"];
                            $this->bairro           = $det_pes["bairro"];
                            $this->logradouro       = $det_pes["logradouro"];
                            $this->cep              = int2CEP( $det_pes["cep"] );
                            $this->idlog            = $det_pes["idlog"];
                            $this->idbai            = $det_pes["idbai"];
                            $this->idtlog           = $det_pes["idtlog"];
                            $this->sigla_uf         = $det_pes["sigla_uf"];
                            $this->complemento      = $det_pes["complemento"];
                            $this->numero           = $det_pes["numero"];
                            $this->bloco            = $det_pes["bloco"];
                            $this->apartamento      = $det_pes["apartamento"];
                            $this->andar            = $det_pes["andar"];

                            $this->fexcluir         = false;
                        }
                    }
                }
                $this->campoOculto( "pessoa", $this->pessoa );
                $this->campoOculto( "cod_pessoa_fj", $this->cod_pessoa_fj );
                $this->campoOculto( "idpes_cad", $this->idpes_cad );
                $this->campoTexto( "fantasia", "Nome Fantasia", $this->fantasia, "50", "255", true );
                $this->campoTexto( "razao_social", "Raz&atilde;o Social", $this->razao_social, "50", "255", true );
                $this->campoTexto( "capital_social", "Capital Social", $this->capital_social, "50", "255" );
                $this->campoOculto( "id_federal", idFederal2int( $this->id_federal ) );
                if ( $this->id_federal ) {
                    $this->campoRotulo( "id_federal_", "CNPJ", $this->id_federal );
                }
                else {
                    $this->campoCnpj( "id_federal_", "CNPJ", "", false );
                }
                $objTipoLog   = new clsTipoLogradouro();
                $listaTipoLog = $objTipoLog->lista();
                $lista        = array( "" => "Selecione" );
                if ( $lista ) {
                    foreach ( $listaTipoLog as $tipoLog ) {
                        $lista[$tipoLog['idtlog']] = $tipoLog['descricao'];
                    }
                }
                $objUf        = new clsUf();
                $listauf      = $objUf->lista();
                $listaEstado  = array( "" => "Selecione" );
                if ( $listauf ) {
                    foreach ( $listauf as $uf ) {
                        $listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
                    }
                }
                $this->campoOculto( "idbai", $this->idbai );
                $this->campoOculto( "idlog", $this->idlog );

                if ( $this->idlog && $this->idbai ) {
                    $this->campoOculto( "cep", $this->cep );
                    $this->campoCep( "cep_", "CEP", int2CEP( $this->cep ), true, "-", "&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f( 'pesquisa_cep_lst.php', 'enderecos' )\" style=\"cursor: hand;\">", true );
                    $this->campoLista( "idtlog", "Tipo Logradouro", $lista, $this->idtlog, false, false, false, false, true );
                    $this->campoTextoInv( "logradouro", "Logradouro", $this->logradouro, "50", "255", true );
                    $this->campoTextoInv( "cidade", "Cidade", $this->cidade, "50", "255", true );
                    $this->campoTextoInv( "bairro", "Bairro", $this->bairro, "50", "255", true );
                    $this->campoLista( "sigla_uf", "Estado", $listaEstado, $this->sigla_uf, "", false, "", "", true );
                }
                else {
                    $this->campoOculto( "cep", $this->cep );
                    $this->campoCep( "cep_", "CEP", int2CEP( $this->cep ), true, "-", "&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f( 'pesquisa_cep_lst.php', 'enderecos' )\" style=\"cursor: hand;\">", false );
                    $this->campoLista( "idtlog", "Tipo Logradouro", $lista, $this->idtlog, false, false, false, false, false );
                    $this->campoTexto( "logradouro", "Logradouro", $this->logradouro, "50", "255", false );
                    $this->campoTexto( "cidade", "Cidade", $this->cidade, "50", "255", false );
                    $this->campoTexto( "bairro", "Bairro", $this->bairro, "50", "255", false );
                    $this->campoLista( "sigla_uf", "Estado", $listaEstado, $this->sigla_uf, "", false, "", "", false );
                }

                $this->campoTexto( "complemento", "Complemento", $this->complemento, "50", "50", false );
                $this->campoTexto( "numero", "Número", $this->numero, "10", "10", false );
                $this->campoTexto( "letra", "Letra", $this->letra, "1", "1", false );
                $this->campoNumero( "ddd_telefone_1", "DDD Telefone 1", $this->ddd_telefone_1, "3", "2", false );
                $this->campoNumero( "telefone_1", "Telefone 1", $this->telefone_1, "10", "15", false );
                $this->campoNumero( "ddd_telefone_2", "DDD Telefone 2",  $this->ddd_telefone_2, "3", "2", false );
                $this->campoNumero( "telefone_2", "Telefone", $this->telefone_2, "10", "15", false );
                $this->campoNumero( "ddd_telefone_mov", "DDD Celular", $this->ddd_telefone_mov, "3", "2", false );
                $this->campoNumero( "telefone_mov", "Celular", $this->telefone_mov, "10", "15", false );
                $this->campoNumero( "ddd_telefone_fax", "DDD Fax", $this->ddd_telefone_fax, "3", "2", false );
                $this->campoNumero( "telefone_fax", "Fax", $this->telefone_fax, "10", "15", false );
                $this->campoTexto( "url", "Site", $this->url, "50", "255", false );
                $this->campoTexto( "email", "E-mail", $this->email, "50", "255", false );
                $this->campoTexto( "insc_est", "Inscri&ccedil;&atilde;o Estadual", $this->insc_est, "20", "30", false );
            }
        }
    }

    function Novo()
    {
        $pessoa_logada = $this->pessoa_logada;
        $parametros = new clsParametrosPesquisas();
        if ( Session::get('campos') ) {
            $parametros->preencheAtributosComArray( Session::get('campos') );
        }
        if ( is_numeric( idFederal2int( $this->cep_ ) ) )
            $this->cep = idFederal2int( $this->cep_ );
        else
            $this->cep = idFederal2int( $this->cep );

        if ( $this->pessoa == "F" )
        {
            if ( $this->id_federal )
            {
                $this->id_federal = idFederal2int( $this->id_federal );
                $objCPF = new clsFisica( false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->id_federal );
                if ( $objCPF->detalhe() )
                {
                    $this->erros['id_federal'] = "CPF j&aacute; cadastrado.";
                    return false;
                }
            }
            $objPessoa            = new clsPessoa_( false, $this->nm_pessoa, $pessoaFj, $this->http, "F", false, false, $this->email );
            $idpes                = $objPessoa->cadastra();


            $this->data_nasc = dataToBanco($this->data_nasc);

            if ( is_numeric( $this->id_federal ) )
            {
                $this->id_federal = idFederal2Int( $this->id_federal );

                $objFisica            = new clsFisica( $idpes,
                                                       $this->data_nasc,
                                                       $this->sexo,
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
                                                       $this->id_federal);
                $objFisica->cadastra();
            }
            else
            {
                $objFisica            = new clsFisica( $idpes,
                                                       $this->data_nasc,
                                                       $this->sexo,
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
                                                       $this->ref_cod_sistema);
                $objFisica->cadastra();
            }

            $this->rg = preg_replace("/[^0-9]/", "", $this->rg);
            $ObjDocumento = new clsDocumento($objFisica->idpes,
                                             $this->rg,
                                             $this->data_emissao_rg,
                                             $this->uf_emissao_rg,
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
                                             $this->orgao_emissao_rg);

            $ObjDocumento->cadastra();

            $objTelefone          = new clsPessoaTelefone( $idpes, 1, $this->telefone_1, $this->ddd_telefone_1 );
            $objTelefone->cadastra();
            $objTelefone          = new clsPessoaTelefone( $idpes, 2, $this->telefone_2, $this->ddd_telefone_2 );
            $objTelefone->cadastra();
            $objTelefone          = new clsPessoaTelefone( $idpes, 3, $this->telefone_mov, $this->ddd_telefone_mov );
            $objTelefone->cadastra();
            $objTelefone          = new clsPessoaTelefone( $idpes, 4, $this->telefone_fax, $this->ddd_telefone_fax );
            $objTelefone->cadastra();


            $this->cod_pessoa_fj  = $idpes;
            $objEndereco          = new clsPessoaEndereco( $this->cod_pessoa_fj );
            $this->cep            = ( $this->cep );
            $objEndereco2         = new clsPessoaEndereco( $this->cod_pessoa_fj, $this->cep, $this->idlog, $this->idbai, $this->numero, $this->complemento, false, $this->letra, $this->bloco, $this->apartamento, $this->andar );


            if( $objEndereco->detalhe() && $this->cep && $this->idlog && $this->idbai ) {
                $objEndereco2->edita();
            }
            elseif( $this->cep && $this->idlog && $this->idbai ) {
                $objEndereco2->cadastra();
            }
            elseif( $objEndereco->detalhe() ) {
                $objEndereco2->exclui();
            }
            if ( is_numeric( $idpes ) ) {
                $obj_pessoa = new clsPessoaFj( $idpes );
                $pessoa     = $obj_pessoa->detalhe();
            }
            if ( is_numeric( $idpes ) ) {
                $obj_pessoa = new clsPessoaFj( $idpes );
                $pessoa = $obj_pessoa->lista_rapida( $idpes );
                $pessoa = $pessoa[0];

                $funcao  = " set_campo_pesquisa(";
                $virgula = "";
                $cont    = 0;

                foreach ( $parametros->getCampoNome() as $campo ) {
                    if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    $cont++;
                }
                if ( $parametros->getSubmit() )
                    $funcao .= "{$virgula} 'submit' )";
                else
                    $funcao .= " )";
                $this->executa_script = $funcao;

                /**
                 * alteracao para executar script
                 * em tabela dinamica
                 * procon
                 */
                if($_GET['tab_dinamica'] == 'procon')
                {
                    $script = "function passaPraTraz(nome, id)
                                {

                                    // reclamada
                                    window.parent.document.getElementById('reclamada[{$_GET['tab_dinamica_id']}]').value = nome;
                                    window.parent.document.getElementById('reclamada_id[{$_GET['tab_dinamica_id']}]').value = id;

                                    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));

                                }
                                passaPraTraz('{$pessoa['nome']}',$idpes);";

                    $this->executa_script = $script;
                }

                return true;
            }
        }
        elseif ( $this->pessoa == "J" )
        {
            if ( $this->id_federal ) {
                $this->id_federal = idFederal2int( $this->id_federal );
                $objCNPJ = new clsJuridica( false, $this->id_federal );
                if ( $objCNPJ->detalhe() ) {
                    $this->erros['id_federal'] = "CNPJ j&aacute; cadastrado.";
                    return false;
                }
            }
            $this->insc_est      = idFederal2int( $this->insc_est );
            $this->idpes_cad     = $this->pessoa_logada;
            $objPessoa           = new clsPessoa_( false, $this->razao_social, $this->idpes_cad, $this->url, "J", false, false, $this->email );
            $this->cod_pessoa_fj = $objPessoa->cadastra();
            $objJuridica         = new clsJuridica( $this->cod_pessoa_fj, $this->id_federal, $this->fantasia, $this->insc_est, $this->capital_social );
            $objJuridica->cadastra();

            if ( $this->telefone_1 ) {
                $this->telefone_1 = str_replace( "-", "", $this->telefone_1 );
                if ( is_numeric( $this->telefone_1 ) ) {
                    $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1 );
                    $objTelefone->cadastra();
                }
            }
            if ( $this->telefone_2 ) {
                $this->telefone_2 = str_replace( "-", "", $this->telefone_2 );
                if ( is_numeric( $this->telefone_2 ) ) {
                    $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2 );
                    $objTelefone->cadastra();
                }
            }
            if ( $this->telefone_mov ) {
                $this->telefone_mov = str_replace( "-", "", $this->telefone_mov );
                if ( is_numeric( $this->telefone_mov ) ) {
                    $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 3, $this->telefone_mov, $this->ddd_telefone_mov );
                    $objTelefone->cadastra();
                }
            }
            if ( $this->telefone_fax ) {
                $this->telefone_fax = str_replace( "-", "", $this->telefone_fax );
                if ( is_numeric( $this->telefone_fax ) ) {
                    $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 4, $this->telefone_fax, $this->ddd_telefone_fax );
                    $objTelefone->cadastra();
                }
            }
            if ( $this->cep && $this->idbai && $this->idlog ) {
                $this->cep = idFederal2Int( $this->cep );
                $objEndereco = new clsPessoaEndereco( $this->cod_pessoa_fj, $this->cep, $this->idlog, $this->idbai, $this->numero, $this->complemento, false, $this->letra );
                $objEndereco->cadastra();
            }
            if ( is_numeric( $this->cod_pessoa_fj ) ) {
                $obj_pessoa = new clsPessoaFj( $this->cod_pessoa_fj );
                $pessoa = $obj_pessoa->lista_rapida( $this->cod_pessoa_fj );
                $pessoa = $pessoa[0];

                $funcao  = " set_campo_pesquisa(";
                $virgula = "";
                $cont    = 0;

                foreach ( $parametros->getCampoNome() as $campo ) {
                    if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    $cont++;
                }
                if ( $parametros->getSubmit() )
                    $funcao .= "{$virgula} 'submit' )";
                else
                    $funcao .= " )";
                $this->executa_script = $funcao;

                /**
                 * alteracao para executar script
                 * em tabela dinamica
                 * procon
                 */
                if($_GET['tab_dinamica'] == 'procon')
                {
                    $script = "function passaPraTraz(nome, id)
                                {

                                    // reclamada
                                    window.parent.document.getElementById('reclamada[{$_GET['tab_dinamica_id']}]').value = nome;
                                    window.parent.document.getElementById('reclamada_id[{$_GET['tab_dinamica_id']}]').value = id;

                                    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));

                                }
                                passaPraTraz('{$pessoa['nome']}',$this->cod_pessoa_fj);";

                    $this->executa_script = $script;
                }
                return true;
            }
        }
        return false;
    }

    function Editar()
    {
        $pessoaFj = $this->pessoa_logada;
        $parametros = new clsParametrosPesquisas();
        if ( $this->cep_ )
            $this->cep = idFederal2int( $this->cep_ );
        if ( Session::get('campos') ) {
            $parametros->preencheAtributosComArray( Session::get('campos') );
        }
        if ( $_POST["pessoa"] == "F" )
        {
            if ( $this->id_federal )
            {
                $this->id_federal = idFederal2int( $this->id_federal );
                $objCPF = new clsFisica( false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->id_federal );
                $detCPF = $objCPF->detalhe();
                if ( $detCPF )
                {
                    if ( $detCPF["idpes"] != $this->cod_pessoa_fj )
                    {
                        $this->mensagem = "CPF j&aacute; cadastrado.";
                        $this->id_federal = false;
                        return false;
                    }
                }
            }

            $this->data_nasc = dataToBanco($this->data_nasc);

            $objPessoa = new clsPessoa_( $this->cod_pessoa_fj, $this->nm_pessoa, false, $this->p_http, false, $pessoaFj, date( "Y-m-d H:i:s", time() ), $this->email );
            $objPessoa->edita();
            if ( $this->id_federal )
            {
                $this->id_federal = idFederal2Int( $this->id_federal );

                $objFisica = new clsFisica( $this->cod_pessoa_fj, $this->data_nasc, $this->sexo, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, "NULL", $this->id_federal);
                $objFisica->edita();
            }
            else {
                $objFisica = new clsFisica( $this->cod_pessoa_fj, $this->data_nasc, $this->sexo, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->ref_cod_sistema, $this->id_federal );
                $objFisica->edita();
            }

            $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1 );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();
            $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2 );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();
            $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 3, $this->telefone_mov, $this->ddd_telefone_mov );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();
            $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 4, $this->telefone_fax, $this->ddd_telefone_fax );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();

            $objEndereco = new clsPessoaEndereco( $this->cod_pessoa_fj );
            $detEndereco = $objEndereco->detalhe();
            $objEndereco2 = new clsPessoaEndereco( $this->cod_pessoa_fj, $this->cep, $this->idlog, $this->idbai, $this->numero, $this->complemento, false, $this->letra, $this->bloco, $this->apartamento, $this->andar );

            if ( $detEndereco && $this->cep && $this->idlog && $this->idbai ) {
                $objEndereco2->edita();
            }
            if ( is_numeric( $this->cod_pessoa_fj ) ) {
                $obj_pessoa = new clsPessoaFj( $this->cod_pessoa_fj );
                $pessoa = $obj_pessoa->lista_rapida( $this->cod_pessoa_fj );
                $pessoa = $pessoa[0];
                $funcao  = " set_campo_pesquisa(";
                $virgula = "";
                $cont    = 0;

                foreach ( $parametros->getCampoNome() as $campo ) {
                    if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    $cont++;
                }
                if ( $parametros->getSubmit() )
                    $funcao .= "{$virgula} 'submit' )";
                else
                    $funcao .= " )";
                $this->executa_script = $funcao;
                /**
                 * alteracao para executar script
                 * em tabela dinamica
                 * procon
                 */
                if($_GET['tab_dinamica'] == 'procon')
                {
                    $script = "function passaPraTraz(nome, id)
                                {

                                    // reclamada
                                    window.parent.document.getElementById('reclamada[{$_GET['tab_dinamica_id']}]').value = nome;
                                    window.parent.document.getElementById('reclamada_id[{$_GET['tab_dinamica_id']}]').value = id;

                                    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));

                                }
                                passaPraTraz('{$pessoa['nome']}',$this->cod_pessoa_fj);";

                    $this->executa_script = $script;
                }
                return true;
            }
        }
        elseif ( $_POST["pessoa"] == "J" ) {
            if ( $this->id_federal ) {
                $this->id_federal = idFederal2int( $this->id_federal );
                $objCNPJ = new clsJuridica( false, $this->id_federal );
                $detCNPJ = $objCNPJ->detalhe();
                if ( $detCNPJ ) {
                    if ( $detCNPJ["idpes"] != $this->cod_pessoa_fj ) {
                        $this->mensagem = "CNPJ j&aacute; cadastrado.";
                        $this->id_federal = false;
                        return false;
                    }
                }
            }
            $this->id_federal = idFederal2int( $this->id_federal );
            $this->insc_est = idFederal2int( $this->insc_est );
            $objPessoa      = new clsPessoa_( $this->cod_pessoa_fj, $this->razao_social, $this->idpes_cad, $this->url, "J", false, false, $this->email );
            $objPessoa->edita();
            $objJuridica    = new clsJuridica( $this->cod_pessoa_fj, $this->id_federal, $this->fantasia, $this->insc_est, $this->capital_social );
            $objJuridica->edita();
            $objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1 );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();
            $objTelefone      = new clsPessoaTelefone( $this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2 );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();
            $objTelefone        = new clsPessoaTelefone( $this->cod_pessoa_fj, 3, $this->telefone_mov, $this->ddd_telefone_mov );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();
            $objTelefone        = new clsPessoaTelefone( $this->cod_pessoa_fj, 4, $this->telefone_fax, $this->ddd_telefone_fax );
            if ( $objTelefone->detalhe() )
                $objTelefone->edita();
            else
                $objTelefone->cadastra();

            $objEndereco = new clsPessoaEndereco( $this->cod_pessoa_fj );
            $detEndereco = $objEndereco->detalhe();
            $this->cep = $this->cep;
            $objEndereco2 = new clsPessoaEndereco( $this->cod_pessoa_fj, $this->cep, $this->idlog, $this->idbai, $this->numero, $this->complemento, false, $this->letra, $this->bloco, $this->apartamento, $this->andar );

            if ( $detEndereco && $this->cep && $this->idlog && $this->idbai ) {
                $objEndereco2->edita();
            }
            elseif ( $this->cep && $this->idlog && $this->idbai ) {
                $objEndereco2->cadastra();
            }
            if ( is_numeric( $this->cod_pessoa_fj ) ) {
                $obj_pessoa = new clsPessoaFj( $this->cod_pessoa_fj );
                $pessoa = $obj_pessoa->lista_rapida( $this->cod_pessoa_fj );
                $pessoa = $pessoa[0];

                $funcao  = " set_campo_pesquisa(";
                $virgula = "";
                $cont    = 0;
                foreach ( $parametros->getCampoNome() as $campo ) {
                    if ( $parametros->getCampoTipo( $cont ) == "text" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    elseif ( $parametros->getCampoTipo( $cont ) == "select" ) {
                        $funcao .= "{$virgula} '{$campo}', '{$pessoa[$parametros->getCampoIndice( $cont )]}', '{$pessoa[$parametros->getCampoValor( $cont )]}'";
                        $virgula = ",";
                    }
                    $cont++;
                }
                if ( $parametros->getSubmit() )
                    $funcao .= "{$virgula} 'submit' )";
                else
                    $funcao .= " )";
                $this->executa_script = $funcao;
                                /**
                 * alteracao para executar script
                 * em tabela dinamica
                 * procon
                 */
                if($_GET['tab_dinamica'] == 'procon')
                {
                    $script = "function passaPraTraz(nome, id)
                                {

                                    // reclamada
                                    window.parent.document.getElementById('reclamada[{$_GET['tab_dinamica_id']}]').value = nome;
                                    window.parent.document.getElementById('reclamada_id[{$_GET['tab_dinamica_id']}]').value = id;

                                    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));

                                }
                                passaPraTraz('{$pessoa['nome']}',$this->cod_pessoa_fj);";

                    $this->executa_script = $script;
                }
                return true;
            }
        }
        return false;
    }

    function Excluir()
    {
        return false;
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>
