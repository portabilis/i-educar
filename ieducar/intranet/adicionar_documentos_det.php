<?php

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/Geral.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Documentos" );
    }
}

class indice extends clsDetalhe
{
    function Gerar()
    {
        $this->titulo = "Documentos";
        $this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet", false );

        $this->idpes = $this->pessoa_logada;

        $objDocumento = new clsDocumento($idpes);
        $detalheDocumento = $objDocumento->detalhe();

        list($idpes, $rg, $data_exp_rg, $sigla_uf_exp_rg, $tipo_cert_civil, $num_termo, $num_livro, $num_folha, $data_emissao_cert_civil, $sigla_uf_cert_civil, $cartorio_cert_civil, $num_cart_trabalho, $serie_cart_trabalho, $data_emissao_cart_trabalho, $sigla_uf_cart_trabalho, $num_tit_eleitor, $zona_tit_eleitor, $secao_tit_eleitor, $idorg_exp_rg) = $objDocumento->detalhe();

        $this->addDetalhe( array("RG", $detalheDocumento['rg'] ) );
        $this->addDetalhe( array("Data Expedição", date('d/m/Y',strtotime(substr($data_exp_rg,0,19)) ) ) );
        $this->addDetalhe( array("Órgão Expedição", $sigla_uf_exp_rg ) );
        $this->addDetalhe( array("Certificado Civil", $tipo_cert_civil ) );
        $this->addDetalhe( array("Termo", $num_termo ) );
        $this->addDetalhe( array("Livro", $num_livro ) );
        $this->addDetalhe( array("Folha", $num_folha ) );
        $this->addDetalhe( array("Emissão Certificado Civil", $data_emissao_cert_civil) );
        $this->addDetalhe( array("Sigla Certificado Civil", $sigla_uf_cert_civil ) );
        $this->addDetalhe( array("Cartório", $cartorio_cert_civil ) );
        $this->addDetalhe( array("Carteira trabalho", $num_cart_trabalho ) );
        $this->addDetalhe( array("série Carteira Trabalho", $serie_cart_trabalho ) );
        $this->addDetalhe( array("Emissão Carteira Trabalho", $data_emissao_cart_trabalho ) );
        $this->addDetalhe( array("Sigla Carteira de Trabalho", $sigla_uf_cart_trabalho ) );
        $this->addDetalhe( array("Título Eleitor", $num_tit_eleitor ) );
        $this->addDetalhe( array("Zona", $zona_tit_eleitor ) );
        $this->addDetalhe( array("Seção", $secao_tit_eleitor ) );
        $this->addDetalhe( array("Órgão Expedição", $idorg_exp_rg) );

        $this->url_novo = "adicionar_documentos_cad.php";
        $this->url_editar = "adicionar_documentos_cad.php?idpes={$idpes}";
        $this->url_cancelar = "meusdados.php";

        $this->largura = "100%";
    }
}

    function Novo()
    {
        $objDocumento = new clsDocumento($this->rg, $this->data_exp_rg, $this->sigla_uf_exp_rg, $this->tipo_cert_civil, $this->num_termo, $this->num_livro, $this->num_folha, $this->data_emissao_cert_civil, $this->sigla_uf_cert_civil, $this->cartorio_cert_civil, $this->num_cart_trabalho, $this->serie_cart_trabalho, $this->data_emissao_cart_trabalho, $this->sigla_uf_cart_trabalho, $this->num_tit_eleitor, $this->zona_tit_eleitor, $this->secao_tit_eleitor, $this->idorg_exp_rg );
        if( $objDocumento->cadastra() )
        {
            echo "<script>document.location='meusdados.php';</script>";
            return true;
        }

        return false;
    }

    function Editar()
    {
        $ObjDocumento = new clsDocumento($this->rg, $this->data_exp_rg, $this->sigla_uf_exp_rg, $this->tipo_cert_civil, $this->num_termo, $this->num_livro, $this->num_folha, $this->data_emissao_cert_civil, $this->sigla_uf_cert_civil, $this->cartorio_cert_civil, $this->num_cart_trabalho, $this->serie_cart_trabalho, $this->data_emissao_cart_trabalho, $this->sigla_uf_cart_trabalho, $this->num_tit_eleitor, $this->zona_tit_eleitor, $this->secao_tit_eleitor, $this->idorg_exp_rg);
        if( $ObjDocumento->edita() )
        {
            echo "<script>document.location='meusdados.php';</script>";
            return true;
        }

        return false;
    }

    function Excluir()
    {
        $ObjDocumento = new clsDocumento($this->idpes);
        $Objcallback->exclui();
        echo "<script>document.location='meusdados.php';</script>";
        return true;
    }

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();

?>
