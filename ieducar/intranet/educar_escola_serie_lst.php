<?php

/**
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
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escola S&eacute;rie');
    $this->processoAp = '585';
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;

  var $ref_cod_curso;
  var $ref_cod_instituicao;
  var $ref_ref_cod_serie;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Escola S&eacute;rie - Listagem';

    foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
      $this->$var = ( $val === '' ) ? null: $val;

    $this->addBanner( 'imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet' );

    $lista_busca = array(
      'S&eacute;rie',
      'Curso'
    );

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
    if ($nivel_usuario == 1)
    {
      $lista_busca[] = 'Escola';
      $lista_busca[] = 'Institui&ccedil;&atilde;o';
    }
    else if ($nivel_usuario == 2)
    {
      $lista_busca[] = 'Escola';
    }
    $this->addCabecalhos($lista_busca);

    $get_escola = true;
//    $get_escola_curso = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    include('include/pmieducar/educar_campo_lista.php');

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET["pagina_{$this->nome}"] ) ?
      $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite :
      0;

    $obj_escola_serie = new clsPmieducarEscolaSerie();
    $obj_escola_serie->setOrderby('nm_serie ASC');
    $obj_escola_serie->setLimite($this->limite, $this->offset);

    $lista = $obj_escola_serie->lista(
      $this->ref_cod_escola,
      $this->ref_ref_cod_serie,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      NULL,
      NULL,
      NULL,
      NULL,
      $this->ref_cod_instituicao,
      $this->ref_cod_curso
    );

    $total = $obj_escola_serie->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
        $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
        $nm_serie = $det_ref_cod_serie['nm_serie'];

        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $lista_busca = array(
          "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_serie}</a>",
          "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_escola}</a>";
          $lista_busca[] = "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }
        elseif ($nivel_usuario == 2) {
          $lista_busca[] = "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_escola}</a>";
        }

        $this->addLinhas($lista_busca);
      }
    }
    $this->addPaginador2("educar_escola_serie_lst.php", $total, $_GET,
      $this->nome, $this->limite);

    if ($obj_permissao->permissao_cadastra(585, $this->pessoa_logada, 7)) {
      $this->acao = "go(\"educar_escola_serie_cad.php\")";
      $this->nome_acao = "Novo";
    }

    $this->largura = "100%";
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
}
</script>