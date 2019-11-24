<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Ocorr&ecirc;ncia Disciplinar" );
        $this->processoAp = "580";
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

    var $cod_tipo_ocorrencia_disciplinar;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $max_ocorrencias;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Tipo Ocorr&ecirc;ncia Disciplinar - Detalhe";


        $this->cod_tipo_ocorrencia_disciplinar=$_GET["cod_tipo_ocorrencia_disciplinar"];

        $tmp_obj = new clsPmieducarTipoOcorrenciaDisciplinar( $this->cod_tipo_ocorrencia_disciplinar );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
        }

            $obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
            $obj_instituicao_det = $obj_instituicao->detalhe();
            $registro["ref_cod_instituicao"] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }
        if( $registro["nm_tipo"] )
        {
            $this->addDetalhe( array( "Tipo Ocorr&ecirc;ncia Disciplinar", "{$registro["nm_tipo"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }
        if( $registro["max_ocorrencias"] )
        {
            $this->addDetalhe( array( "M&aacute;ximo Ocorr&ecirc;ncias", "{$registro["max_ocorrencias"]}") );
        }

        if( $obj_permissao->permissao_cadastra( 580, $this->pessoa_logada,3 ) )
        {
            $this->url_novo = "educar_tipo_ocorrencia_disciplinar_cad.php";
            $this->url_editar = "educar_tipo_ocorrencia_disciplinar_cad.php?cod_tipo_ocorrencia_disciplinar={$registro["cod_tipo_ocorrencia_disciplinar"]}";
        }
        $this->url_cancelar = "educar_tipo_ocorrencia_disciplinar_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do tipo de ocorrência disciplinar', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
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
