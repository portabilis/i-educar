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
 * CoreExt_Controller_Dispatcher_Strategy_FrontStrategy class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Controller_Dispatcher_Strategy_FrontStrategy
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
   * Não implementado.
   * @see CoreExt_Controller_Strategy_Interface#dispatch()
   */
  public function dispatch()
  {
    require_once 'CoreExt/Controller/Dispatcher/Exception.php';
    throw new CoreExt_Controller_Dispatcher_Exception('Método CoreExt_Controller_Strategy_FrontStrategy::dispatch() não implementado.');
  }
}