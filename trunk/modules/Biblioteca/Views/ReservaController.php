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

require_once 'Core/Controller/Page/ListController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/View/Helper/DynamicSelectMenus.php';

class ReservaController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = ''; #Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Reserva';
  protected $_formMap  = array();

  #TODO setar código processoAP, copiar da funcionalidade de reserva existente?
  protected $_processoAp = 0;


  protected function setVars()
  {
    #$this->ref_cod_cliente = $_GET['cliente_id'];
    #$this->ref_cod_instituicao = $_GET['instituicao_id'];
    #$this->ref_cod_escola = $_GET['escola_id'];

    #if ($this->ref_cod_cliente)
    #{
    #  $cliente = new clsPmieducarCliente();
    #  $cliente = $cliente->lista();
    #  $this->nm_cliente = $cliente[0]['nome_cliente'];
    #}
  }


  protected function setSelectionFields()
  {
    $dynamicSelectMenus = new Portabilis_View_Helper_DynamicSelectMenus($this);

    $dynamicSelectMenus->helperFor('instituicao');
    $dynamicSelectMenus->helperFor('escola');
    $dynamicSelectMenus->helperFor('biblioteca');
    $dynamicSelectMenus->helperFor('bibliotecaPesquisaCliente');
    $dynamicSelectMenus->helperFor('bibliotecaPesquisaObra');
  }


  public function Gerar()
  {
    Portabilis_View_Helper_Application::loadStylesheet($this, '/modules/Portabilis/Assets/Stylesheets/FrontendApi.css');

    $this->setVars();
    $this->setSelectionFields();

    $this->rodape = '';
    $this->largura = '100%';

    /*$resourceOptionsTable = "
    <table id='resource-options' class='styled horizontal-expand hide-on-search disable-on-apply-changes'>

      <tr>
        <td><label for=''>LabelName *</label></td>
        <td colspan='2'><input type='text' id='' name='' class='obrigatorio disable-on-search validates-value-is-numeric'></input></td>
      </tr>

    </table>";

    $this->appendOutput($resourceOptionsTable);*/

    Portabilis_View_Helper_Application::loadJavascript($this, 'scripts/jquery/jquery.form.js');

    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Portabilis/Assets/Javascripts/FrontendApi.js');

    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Biblioteca/Assets/Javascripts/reservaController.js');
  }
}
?>
