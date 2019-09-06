<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Biblioteca
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Portabilis/Controller/Page/ListController.php';

class EmprestimoController extends Portabilis_Controller_Page_ListController
{
  protected $_dataMapper = '';
  protected $_titulo     = 'Emprestimo';
  protected $_formMap    = array();
  protected $_processoAp = 610;

  protected function _preRender(){
    $pessoa_logada = $this->pessoa_logada;

    $obj_permissao = new clsPermissoes();
    $obj_permissao->permissao_cadastra(610, $pessoa_logada, 7, '/intranet/educar_biblioteca_index.php');

    parent::_preRender();

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "Biblioteca",
         ""                                  => "Empr&eacute;stimo de exemplares"             
    ));
    $this->enviaLocalizacao($localizacao->montar(), true);     
  }  

  public function Gerar() {
    // inputs
    $this->inputsHelper()->dynamic('instituicao', array('id' => 'instituicao_id'));
    $this->inputsHelper()->dynamic('escola',      array('id' => 'escola_id'));
    $this->inputsHelper()->dynamic('biblioteca',  array('id' => 'biblioteca_id'));
    $this->campoNumero('tombo_exemplar', 'Tombo exemplar', '', 13, 13, true);

    $helperOptions = array('hiddenInputOptions' => array('id' => 'cliente_id'));
    $this->inputsHelper()->dynamic('bibliotecaPesquisaCliente', array(), $helperOptions);

    // assets
    $this->loadResourceAssets($this->getDispatcher());
  }
}
?>
