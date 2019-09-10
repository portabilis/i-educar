<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/public/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Pais" );
        $this->processoAp = "753";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $idpais;
    var $nome;
    var $geom;

    function Gerar()
    {
        $this->titulo = "Pais - Detalhe";


        $this->idpais=$_GET["idpais"];

        $tmp_obj = new clsPublicPais( $this->idpais );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('public_pais_lst.php');
        }

        if( $registro["nome"] )
        {
            $this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
        }
        if( $registro["geom"] )
        {
            $this->addDetalhe( array( "Geom", "{$registro["geom"]}") );
        }

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(753, $this->pessoa_logada,7,null,true))
        {
            $this->url_novo = "public_pais_cad.php";
            $this->url_editar = "public_pais_cad.php?idpais={$registro["idpais"]}";
        }

        $this->url_cancelar = "public_pais_lst.php";
        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_enderecamento_index.php"    => "Endereçamento",
             ""                                  => "Detalhe do pa&iacute;s"
        ));
        $this->enviaLocalizacao($localizacao->montar());
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
