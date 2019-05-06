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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo de ambiente" );
        $this->processoAp = "572";
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

    var $cod_infra_comodo_funcao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_funcao;
    var $desc_funcao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_escola;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Tipo de ambiente  - Detalhe";


        $this->cod_infra_comodo_funcao=$_GET["cod_infra_comodo_funcao"];

//      $tmp_obj = new clsPmieducarInfraComodoFuncao( $this->cod_infra_comodo_funcao );
//      $registro = $tmp_obj->detalhe();
        $obj = new clsPmieducarInfraComodoFuncao();
        $lst  = $obj->lista( $this->cod_infra_comodo_funcao );
        if (is_array($lst))
        {
            $registro = array_shift($lst);
        }

        if( ! $registro )
        {
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        if( class_exists( "clsPmieducarInstituicao" ) )
        {
            $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
            $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
            $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
        }
        else
        {
            $registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
            echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
        }
        if( class_exists( "clsPmieducarEscola" ) )
        {
            $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $nm_escola = $det_ref_cod_escola["nome"];
        }
        else
        {
            $registro["ref_cod_escola"] = "Erro na geracao";
            echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
        }

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }
        if ($nivel_usuario == 1 || $nivel_usuario == 2)
        {
            if( $nm_escola )
            {
                $this->addDetalhe( array( "Escola", "{$nm_escola}") );
            }
        }
        if( $registro["cod_infra_comodo_funcao"] )
        {
            $this->addDetalhe( array( "Código tipo de ambiente", "{$registro["cod_infra_comodo_funcao"]}") );
        }
        if( $registro["nm_funcao"] )
        {
            $this->addDetalhe( array( "Tipo de ambiente", "{$registro["nm_funcao"]}") );
        }
        if( $registro["desc_funcao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o do tipo", "{$registro["desc_funcao"]}") );
        }

        $obj_permissao = new clsPermissoes();
        if( $obj_permissao->permissao_cadastra( 572, $this->pessoa_logada,7 ) ) {
            $this->url_novo = "educar_infra_comodo_funcao_cad.php";
            $this->url_editar = "educar_infra_comodo_funcao_cad.php?cod_infra_comodo_funcao={$registro["cod_infra_comodo_funcao"]}";
        }

        $this->url_cancelar = "educar_infra_comodo_funcao_lst.php";
        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""                                  => "Detalhe do tipo de ambiente"
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
