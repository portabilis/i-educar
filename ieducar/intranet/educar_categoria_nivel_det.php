<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * Detalhes de um nível de categoria.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';


class clsIndexBase extends clsBase
{
  public function Formular() {
    $this->SetTitulo($this->_instituicao . 'Servidores - Detalhe Categoria Nível');
    $this->processoAp = "829";
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

    var $cod_categoria_nivel;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_categoria_nivel;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Categoria Nivel - Detalhe";
        $this->addBanner("imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet");

        $this->cod_categoria_nivel=$_GET["cod_categoria_nivel"];

        $tmp_obj = new clsPmieducarCategoriaNivel( $this->cod_categoria_nivel );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }


        if( $registro["cod_categoria_nivel"] )
        {
            $this->addDetalhe( array( "Categoria", "{$registro["cod_categoria_nivel"]}") );
        }
        if( $registro["nm_categoria_nivel"] )
        {
            $this->addDetalhe( array( "Nome Categoria", "{$registro["nm_categoria_nivel"]}") );
        }

        $tab_niveis = null;

        $obj_nivel = new clsPmieducarNivel();
        $lst_nivel = $obj_nivel->buscaSequenciaNivel($this->cod_categoria_nivel);

        if($lst_nivel)
        {
            $tab_niveis .= "<table cellspacing='0' cellpadding='0' width='200' border='0'>";

            $class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;
            $tab_niveis .= " <tr>
                                <td bgcolor='#ccdce6' align='center'>N&iacute;veis</td>
                                <td bgcolor='#ccdce6' align='center'>Subn&iacute;veis</td>
                            </tr>";
            foreach ($lst_nivel as $nivel)
            {

                $tab_niveis .= " <tr class='$class2' align='center'>
                                    <td align='left'>{$nivel['nm_nivel']}</td>
                                    <td align='center'><a style='color:#0ac336;' href='javascript:popless(\"{$nivel['cod_nivel']}\")'><i class='fa fa-plus-square' aria-hidden='true'></i></a></td>
                                </tr>";

                $class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;

            }
            $tab_niveis .=  "</table>";

            $this->addDetalhe(array("N&iacute;veis", "$tab_niveis"));
        }


        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3, null, true ) )
        {
            $this->url_novo = "educar_categoria_nivel_cad.php";
            $this->url_editar = "educar_categoria_nivel_cad.php?cod_categoria_nivel={$registro["cod_categoria_nivel"]}";
            $this->array_botao[] = 'Adicionar Níveis';
            $this->array_botao_url[] = "educar_nivel_cad.php?cod_categoria={$registro["cod_categoria_nivel"]}";
        }

        $this->url_cancelar = "educar_categoria_nivel_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhes da categoria/nível', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
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

<script type="text/javascript">
    function popless(nivel)
    {
        var campoCategoria = <?=$_GET["cod_categoria_nivel"];?>;
        pesquisa_valores_popless('educar_subniveis_cad.php?ref_cod_categoria='+campoCategoria+'&ref_cod_nivel='+nivel, '');
    }

</script>
