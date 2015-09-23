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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Controller/Dispatcher/Abstract.php';
require_once 'CoreExt/Controller/Dispatcher/Strategy/Interface.php';

/**
 * CoreExt_Controller_Strategy_PageStrategy class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Controller_Dispatcher_Strategy_PageStrategy
  extends CoreExt_Controller_Dispatcher_Abstract
  implements CoreExt_Controller_Dispatcher_Strategy_Interface
{
  /**
   * Instância de CoreExt_Controller_Interface.
   * @var CoreExt_Controller_Interface
   */
  protected $_controller = NULL;

  /**
   * Construtor.
   * @see CoreExt_Controller_Strategy_Interface#__construct($controller)
   */
  public function __construct(CoreExt_Controller_Interface $controller)
  {
    $this->setController($controller);
  }

  /**
   * @see CoreExt_Controller_Strategy_Interface#setController($controller)
   */
  public function setController(CoreExt_Controller_Interface $controller)
  {
    $this->_controller = $controller;
    return $this;
  }

  /**
   * @see CoreExt_Controller_Strategy_Interface#getController()
   */
  public function getController()
  {
    return $this->_controller;
  }

  /**
   * Determina qual page controller irá assumir a requisição.
   *
   * Determina basicamente o caminho no sistema de arquivos baseado nas
   * informações recebidas pela requisição e pelas opções de configuração.
   *
   * É importante ressaltar que uma ação no contexto do Page Controller
   * significa uma instância de CoreExt_Controller_Page_Interface em si.
   *
   * Um controller nesse contexto pode ser pensado como um módulo auto-contido.
   *
   * Exemplo:
   * <code>
   * Um requisição HTTP para a URL:
   * http://www.example.com/notas/listar
   *
   * Com CoreExt_Controller_Interface configurado da seguinte forma:
   * basepath = /var/www/ieducar/modules
   * controller_dir = controllers
   *
   * Iria mapear para o arquivo:
   * /var/www/ieducar/modules/notas/controllers/ListarController.php
   * </code>
   *
   * @global DS Constante para DIRECTORY_SEPARATOR
   * @see    CoreExt_Controller_Strategy_Interface#dispatch()
   * @todo   Funções de controle de buffer não funcionam por conta de chamadas
   *         a die() e exit() nas classes clsDetalhe, clsCadastro e clsListagem.
   * @throws CoreExt_Exception_FileNotFoundException
   * @return bool
   */
  public function dispatch()
  {
    $this->setRequest($this->getController()->getRequest());

    $controller     = $this->getControllerName();
    $pageController = ucwords($this->getActionName()) . 'Controller';
    $basepath       = $this->getController()->getOption('basepath');
    $controllerDir  = $this->getController()->getOption('controller_dir');
    $controllerType = $this->getController()->getOption('controller_type');

    $controllerFile = array($basepath, $controller, $controllerDir, $pageController);
    $controllerFile = sprintf('%s.php', implode(DS, $controllerFile));

    if (!file_exists($controllerFile)) {
      require_once 'CoreExt/Exception/FileNotFoundException.php';
      throw new CoreExt_Exception_FileNotFoundException('Nenhuma classe CoreExt_Controller_Page_Interface para o controller informado no caminho: "' . $controllerFile . '"');
    }

    require_once $controllerFile;
    $pageController = new $pageController();

    // Injeta as instâncias CoreExt_Dispatcher_Interface, CoreExt_Request_Interface
    // CoreExt_Session no page controller
    $pageController->setDispatcher($this);
    $pageController->setRequest($this->getController()->getRequest());
    $pageController->setSession($this->getController()->getSession());

    ob_start();
    $pageController->generate($pageController);
    $this->getController()->getView()->setContents(ob_get_contents());
    ob_end_clean();

    return TRUE;
  }
}