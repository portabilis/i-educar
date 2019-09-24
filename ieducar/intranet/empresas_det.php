<?php

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Empresas" );
        $this->processoAp = 41;
    }
}

class indice extends clsDetalhe
{
    function Gerar()
    {
        $this->titulo = "Detalhe da empresa";


        $cod_empresa = @$_GET['cod_empresa'];

        $objPessoaJuridica = new clsPessoaJuridica();
        list ($cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $http, $email, $ins_est, $tipo_pessoa, $razao_social, $capital_social, $ins_mun, $idtlog) = $objPessoaJuridica->queryRapida($cod_empresa, "idpes","fantasia","cnpj","logradouro","cep","bairro","cidade", "ddd_1","fone_1","ddd_2","fone_2","ddd_mov","fone_mov","ddd_fax","fone_fax","url", "email","insc_estadual","tipo","nome","insc_municipal", "idtlog");
        $endereco = "$idtlog $endereco";
        $db = new clsBanco();

        $this->addDetalhe( array("Raz&atilde;o Social", $razao_social) );
        $this->addDetalhe( array("Nome Fantasia", $nm_pessoa) );
        $this->addDetalhe( array("CNPJ", int2CNPJ($id_federal)) );
        $this->addDetalhe( array("Endere&ccedil;o", $endereco) );
        $this->addDetalhe( array("CEP", $cep) );
        $this->addDetalhe( array("Bairro", $nm_bairro) );
        $this->addDetalhe( array("Cidade", $cidade) );

        $this->addDetalhe( array("Telefone 1", "({$ddd_telefone_1}) {$telefone_1}") );
        $this->addDetalhe( array("Telefone 2", "({$ddd_telefone_2}) {$telefone_2}") );
        $this->addDetalhe( array("Celular", "({$ddd_telefone_mov}) {$telefone_mov}") );
        $this->addDetalhe( array("Fax", "({$ddd_telefone_fax}) {$telefone_fax}") );

        $this->addDetalhe( array("Site", $http) );
        $this->addDetalhe( array("E-mail", $email) );

        if( ! $ins_est ) $ins_est = "isento";
        $this->addDetalhe( array("Inscri&ccedil;&atilde;o Estadual", $ins_est) );
        $this->addDetalhe( array("Capital Social", $capital_social) );

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(41, $this->pessoa_logada,7,null,true))
        {
            $this->url_novo = "empresas_cad.php";
            $this->url_editar = "empresas_cad.php?idpes={$cod_empresa}";
        }

        $this->url_cancelar = "empresas_lst.php";

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_pessoas_index.php"          => "Pessoas",
         ""                                  => "Detalhe da pessoa jur&iacute;dica"
    ));
    $this->enviaLocalizacao($localizacao->montar());
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>
