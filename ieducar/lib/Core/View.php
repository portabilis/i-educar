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
 * @package   Core_View
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';

/**
 * Core_View class.
 *
 * Provê métodos getters/setters e alguns métodos sobrescritos para facilitar
 * a geração de páginas usando CoreExt_Controller_Page_Interface.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_View
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Core_View extends clsBase
{
  /**
   * Uma instância de CoreExt_Controller_Page_Interface.
   * @var CoreExt_Controller_Page_Interface
   */
  protected $_pageController = NULL;

  /**
   * Construtor.
   * @param Core_Controller_Page_Interface $instance
   */
  public function __construct(Core_Controller_Page_Interface $instance)
  {
    parent::__construct();
    $this->_setPageController($instance);
  }

  /**
   * Setter.
   * @param Core_Controller_Page_Interface $instance
   * @return Core_View Provê interface fluída
   */
  protected function _setPageController(Core_Controller_Page_Interface $instance)
  {
    $this->_pageController = $instance;
    return $this;
  }

  /**
   * Getter.
   * @return CoreExt_Controller_Page_Interface
   */
  protected function _getPageController()
  {
    return $this->_pageController;
  }

  /**
   * Setter
   * @param string $titulo
   * @return Core_View Provê interface fluída
   */
  public function setTitulo($titulo)
  {
    parent::SetTitulo($titulo);
    return $this;
  }

  /**
   * Getter.
   * @return string
   */
  public function getTitulo()
  {
    return $this->titulo;
  }

  /**
   * Setter.
   * @param int $processo
   * @return Core_View Provê interface fluída
   */
  public function setProcessoAp($processo)
  {
    $this->processoAp = (int) $processo;
    return $this;
  }

  /**
   * Getter.
   * @return int
   */
  public function getProcessoAp()
  {
    return $this->processoAp;
  }

  /**
   * Configura algumas variáveis de instância usando o container global
   * $coreExt.
   *
   * @global $coreExt
   * @see clsBase#Formular()
   */
  public function Formular()
  {
    global $coreExt;
    $instituicao = $coreExt['Config']->app->template->vars->instituicao;

    $this->setTitulo($instituicao . ' | ' . $this->_getPageController()->getBaseTitulo())
         ->setProcessoAp($this->_getPageController()->getBaseProcessoAp());
  }

  /**
   * Executa o método de geração de HTML para a classe.
   * @param Core_View $instance
   */
  public static function generate($instance)
  {
    $viewBase = new self($instance);
    $viewBase->addForm($instance);
    $viewBase->MakeAll();
  }
}