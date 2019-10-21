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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Componentes da série');
    $this->processoAp = '9998859';
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

  var $cod_serie;
  var $ref_cod_curso;
  var $nm_serie;

  var $ref_cod_instituicao;

  function Gerar()
  {
    $this->titulo = "Componentes da série";

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $lista_busca = array(
      'S&eacute;rie',
      'Curso'
    );

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      $lista_busca[] = 'Instituição';
    }

    $this->addCabecalhos($lista_busca);

    $this->inputsHelper()->dynamic(array('instituicao', 'curso', 'serie'), array('required' => false));

    // Paginador
    $this->limite = 10;
    $this->offset = $_GET["pagina_{$this->nome}"] ?
      $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;

    $obj_serie = new clsPmieducarSerie();
    $obj_serie->setOrderby("nm_serie ASC");
    $obj_serie->setLimite($this->limite, $this->offset);

    $lista = $obj_serie->listaSeriesComComponentesVinculados($this->ref_cod_serie,
                                                             $this->ref_cod_curso,
                                                             $this->ref_cod_instituicao,
                                                             1);

    $total = $obj_serie->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        // Pega detalhes de foreign_keys
        $obj_ref_cod_curso = new clsPmieducarCurso($registro["ref_cod_curso"]);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];

        $obj_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
        $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

        $lista_busca = array(
          "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro["cod_serie"]}\">{$registro["nm_serie"]}</a>",
          "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro["cod_serie"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro["cod_serie"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }

        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2("educar_componentes_serie_lst.php", $total, $_GET, $this->nome, $this->limite);

    if ($obj_permissoes->permissao_cadastra(9998859, $this->pessoa_logada, 3)) {
      $this->acao = "go(\"educar_componentes_serie_cad.php\")";
      $this->nome_acao = "Novo";
    }

    $this->largura = "100%";

    $this->breadcrumb('Componentes da série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

    $scripts = array('/modules/Cadastro/Assets/Javascripts/ComponentesSerieFiltros.js');

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
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
