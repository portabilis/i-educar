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
require_once 'lib/Portabilis/View/Helper/Inputs.php';

class ReservaController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = ''; #Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Reserva';
  protected $_formMap  = array();

  #TODO setar código processoAP, copiar da funcionalidade de reserva existente?
  protected $_processoAp = 0;

  protected function setSelectionFields() {
    $inputsHelper = new Portabilis_View_Helper_Inputs($this);

    $inputsHelper->dynamicInput('instituicao');
    $inputsHelper->dynamicInput('escola');
    $inputsHelper->dynamicInput('biblioteca');
    $inputsHelper->dynamicInput('bibliotecaPesquisaCliente');
    $inputsHelper->dynamicInput('bibliotecaPesquisaObra');
  }


  public function Gerar() {
    $styles = array('/modules/Portabilis/Assets/Stylesheets/FrontendApi.css',
                    '/modules/Portabilis/Assets/Stylesheets/Utils.css');

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

    $this->setSelectionFields();

    $this->rodape  = '';
    $this->largura = '100%';

    $scripts = array('scripts/jquery/jquery.form.js',
                     '/modules/Portabilis/Assets/Javascripts/Utils.js',
                     '/modules/Portabilis/Assets/Javascripts/Frontend/Process.js',
                     '/modules/Biblioteca/Assets/Javascripts/reservaController.js');

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
  }
}
?>
