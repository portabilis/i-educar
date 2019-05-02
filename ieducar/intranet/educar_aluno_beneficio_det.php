<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Benef&iacute;cio Aluno" );
        $this->processoAp = "581";
        $this->addEstilo("localizacaoSistema");
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

    var $cod_aluno_beneficio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_beneficio;
    var $desc_beneficio;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Aluno Beneficio - Detalhe";
        

        $this->cod_aluno_beneficio=$_GET["cod_aluno_beneficio"];

        $tmp_obj = new clsPmieducarAlunoBeneficio( $this->cod_aluno_beneficio );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_aluno_beneficio_lst.php');
        }

        if( $registro["cod_aluno_beneficio"] )
        {
            $this->addDetalhe( array( "C&oacute;digo Benef&iacute;cio", "{$registro["cod_aluno_beneficio"]}") );
        }
        if( $registro["nm_beneficio"] )
        {
            $this->addDetalhe( array( "Benef&iacute;cio", "{$registro["nm_beneficio"]}") );
        }
        if( $registro["desc_beneficio"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", nl2br("{$registro["desc_beneficio"]}")) );
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(581, $this->pessoa_logada,3))
        {
            $this->url_novo = "educar_aluno_beneficio_cad.php";
            $this->url_editar = "educar_aluno_beneficio_cad.php?cod_aluno_beneficio={$registro["cod_aluno_beneficio"]}";
        }
        //**
        $this->url_cancelar = "educar_aluno_beneficio_lst.php";
        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""                                  => "Detalhe do benef&iacute;cio de alunos"
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
