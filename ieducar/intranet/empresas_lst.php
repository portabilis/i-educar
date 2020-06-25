<?php

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Empresas!" );
        $this->processoAp = 41;
    }
}

class indice extends clsListagem
{
    function Gerar()
    {
        $this->titulo = "Empresas";


        $this->addCabecalhos( array( "Razão Social", "Nome Fantasia" ) );

        $this->campoTexto( "fantasia", "Nome Fantasia",  $_GET['nm_pessoa'], "50", "255", true );
        $this->campoTexto( "razao_social", "Razão Social",  $_GET['razao_social'], "50", "255", true );
        $this->campoCnpj( "id_federal", "CNPJ",  $_GET['id_federal'], "50", "255", true );

        // Paginador
        $limite = 10;
        $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

        $par_nome = false;
        $par_razao = false;
        $par_cnpj = false;
        $opcoes = false;
        if ($_GET['fantasia'])
        {
            $par_fantasia = $_GET['fantasia'];
        }
        if ($_GET['razao_social'])
        {
            $par_razao = $_GET['razao_social'];

            $objPessoaFJ = new clsPessoaFj();
            $lista = $objPessoaFJ->lista($par_razao);
            if($lista)
            foreach ($lista as $pessoa) {
                $opcoes[] = $pessoa['idpes'];
            }
        }
        if ($_GET['id_federal'])
        {
            $par_cnpj =  idFederal2Int( $_GET['id_federal'] );
        }

        $objPessoa = new clsPessoaJuridica();
        $db = new clsBanco();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $objPessoa->codUsuario = $this->pessoa_logada;
        }

        $empresas = $objPessoa->lista( $par_cnpj, $par_fantasia, false, $iniciolimit, $limite, "fantasia asc",$opcoes );
        if($empresas)
        {
            foreach ( $empresas AS $empresa )
            {
                $total = $empresa['total'];
                $cod_empresa = $empresa['idpes'];
                $razao_social = $db->escapeString($empresa['nome']);
                $nome_fantasia = $db->escapeString($empresa['fantasia']);
                $this->addLinhas( array( "<a href='empresas_det.php?cod_empresa={$cod_empresa}'><img src='imagens/noticia.jpg' border=0>$razao_social</a>", "<a href='empresas_det.php?cod_empresa={$cod_empresa}'>{$nome_fantasia}</a>" ) );
            }
        }
        // Paginador
        $this->addPaginador2( " empresas_lst.php", $total, $_GET, $this->nome, $limite );

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(41, $this->pessoa_logada,7,null,true))
        {
            $this->acao = "go(\"empresas_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_pessoas_index.php"          => "Pessoas",
         ""                                  => "Listagem de pessoas jur&iacute;dicas"
    ));
    $this->enviaLocalizacao($localizacao->montar());
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
