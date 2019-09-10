<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/public/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Uf" );
        $this->processoAp = "754";
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

    var $sigla_uf;
    var $nome;
    var $geom;
    var $idpais;

    function Gerar()
    {
        $this->titulo = "Uf - Detalhe";


        $this->sigla_uf=$_GET["sigla_uf"];

        $tmp_obj = new clsPublicUf( $this->sigla_uf );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('public_uf_lst.php');
        }

        if( class_exists( "clsPais" ) )
        {
            $obj_idpais = new clsPais( $registro["idpais"] );
            $det_idpais = $obj_idpais->detalhe();
            $registro["idpais"] = $det_idpais["nome"];
        }
        else
        {
            $registro["idpais"] = "Erro na geracao";
            echo "<!--\nErro\nClasse nao existente: clsPais\n-->";
        }


        if( $registro["sigla_uf"] )
        {
            $this->addDetalhe( array( "Sigla Uf", "{$registro["sigla_uf"]}") );
        }
        if( $registro["nome"] )
        {
            $this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
        }
        if( $registro["geom"] )
        {
            $this->addDetalhe( array( "Geom", "{$registro["geom"]}") );
        }
        if( $registro["idpais"] )
        {
            $this->addDetalhe( array( "Pais", "{$registro["idpais"]}") );
        }
        if( $registro["cod_ibge"] )
        {
            $this->addDetalhe( array( "C&oacute;digo INEP", "{$registro["cod_ibge"]}") );
        }

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(754, $this->pessoa_logada,7,null,true))
        {
            $this->url_novo = "public_uf_cad.php";
            $this->url_editar = "public_uf_cad.php?sigla_uf={$registro["sigla_uf"]}";
        }

        $this->url_cancelar = "public_uf_lst.php";
        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_enderecamento_index.php"    => "Endereçamento",
             ""                                  => "Detalhe da UF"
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
