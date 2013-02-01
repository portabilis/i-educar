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

require_once 'CoreExt/Controller/Abstract.php';

/**
 * CoreExt_Controller_Front class.
 *
 * Essa é uma implementação simples do design pattern {@link http://martinfowler.com/eaaCatalog/frontController.html front controller},
 * que tem como objetivo manusear e encaminhar a requisição para uma classe
 * que se responsabilize pelo processamento do recurso solicitado.
 *
 * Apesar de ser um front controller, o encaminhamento para uma classe
 * {@link http://en.wikipedia.org/wiki/Command_pattern command} não está
 * implementado.
 *
 * Entretanto, está disponível o encaminhamento para uma classe que implemente
 * o pattern {@link http://martinfowler.com/eaaCatalog/pageController.html page controller},
 * ou seja, qualquer classe que implemente a interface
 * CoreExt_Controller_Page_Interface.
 *
 * O processo de encaminhamento (dispatching), é definido por uma classe
 * {@link http://en.wikipedia.org/wiki/Strategy_pattern strategy}.
 *
 * Algumas opções afetam o comportamento dessa classe. As opções disponíveis
 * para configurar uma instância da classe são:
 * - basepath: diretório em que os implementadores de command e page controller
 *   serão procurados
 * - controller_dir: determina o nome do diretório em que os controllers deverão
 *   estar salvos
 * - controller_type: tipo de controller a ser instanciado. Uma instância de
 *   CoreExt_Controller_Front pode usar apenas um tipo por processo de
 *   dispatch() e o valor dessa opção determina qual strategy de dispatch será
 *   utilizada (CoreExt_Controller_Strategy).
 *
 * Por padrão, os valores de controller_dir e controller_type são definidos para
 * 'Views' e 2, respectivamente. Isso significa que a estratégia de page
 * controller será utilizada durante a chamada ao método dispatch().
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Controller_Front extends CoreExt_Controller_Abstract
{
  /**
   * Opções para definição de qual tipo de controller utilizar durante a
   * execução de dispatch().
   * @var int
   */
  const CONTROLLER_FRONT = 1;
  const CONTROLLER_PAGE  = 2;

  /**
   * A instância singleton de CoreExt_Controller_Interface.
   * @var CoreExt_Controller_Interface|NULL
   */
  protected static $_instance = NULL;

  /**
   * Opções de configuração geral da classe.
   * @var array
   */
  protected $_options = array(
    'basepath'        => NULL,
    'controller_type' => self::CONTROLLER_PAGE,
    'controller_dir'  => 'Views'
  );

  /**
   * Contém os valores padrão da configuração.
   * @var array
   */
  protected $_defaultOptions = array();

  /**
   * Uma instância de CoreExt_View_Abstract
   * @var CoreExt_View_Abstract
   */
  protected $_view = NULL;

  /**
   * Construtor singleton.
   */
  protected function __construct()
  {
    $this->_defaultOptions = $this->getOptions();
  }

  /**
   * Retorna a instância singleton.
   * @return CoreExt_Controller_Front
   */
  public static function getInstance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Recupera os valores de configuração original da instância.
   * @return CoreExt_Configurable Provê interface fluída
   */
  public function resetOptions()
  {
    $this->setOptions($this->_defaultOptions);
    return $this;
  }

  /**
   * Encaminha a execução para o objeto CoreExt_Dispatcher_Interface apropriado.
   * @return CoreExt_Controller_Interface Provê interface fluída
   * @see CoreExt_Controller_Interface#dispatch()
   */
  public function dispatch()
  {
    $this->_getControllerStrategy()->dispatch();
    return $this;
  }

  /**
   * Retorna o conteúdo gerado pelo controller.
   * @return string
   */
  public function getViewContents()
  {
    return $this->getView()->getContents();
  }

  /**
   * Setter.
   * @param CoreExt_View_Abstract $view
   * @return CoreExt_Controller_Interface Provê interface fluída
   */
  public function setView(CoreExt_View_Abstract $view)
  {
    $this->_view = $view;
    return $this;
  }

  /**
   * Getter para uma instância de CoreExt_View_Abstract.
   *
   * Instância via lazy initialization uma instância de CoreExt_View caso
   * nenhuma seja explicitamente atribuída a instância atual.
   *
   * @return CoreExt_View_Abstract
   */
  public function getView()
  {
    if (is_null($this->_view)) {
      require_once 'CoreExt/View.php';
      $this->setView(new CoreExt_View());
    }
    return $this->_view;
  }

  /**
   * Getter para uma instância de CoreExt_Controller_Dispatcher_Interface.
   *
   * Instância via lazy initialization uma instância de
   * CoreExt_Controller_Dispatcher caso nenhuma seja explicitamente
   * atribuída a instância atual.
   *
   * @return CoreExt_Controller_Dispatcher_Interface
   */
  public function getDispatcher()
  {
    if (is_null($this->_dispatcher)) {
      $this->setDispatcher($this->_getControllerStrategy());
    }
    return $this->_dispatcher;
  }

  /**
   * Getter para a estratégia de controller, definida em runtime.
   * @return CoreExt_Controller_Strategy
   */
  protected function _getControllerStrategy()
  {
    switch($this->getOption('controller_type')) {
      case 1:
        require_once 'CoreExt/Controller/Dispatcher/Strategy/FrontStrategy.php';
        $strategy = 'CoreExt_Controller_Dispatcher_Strategy_FrontStrategy';
        break;
      case 2:
        require_once 'CoreExt/Controller/Dispatcher/Strategy/PageStrategy.php';
        $strategy = 'CoreExt_Controller_Dispatcher_Strategy_PageStrategy';
        break;
    }
    return new $strategy($this);
  }
}