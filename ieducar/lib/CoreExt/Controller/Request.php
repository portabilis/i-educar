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

require_once 'CoreExt/Controller/Request/Interface.php';

/**
 * CoreExt_Controller_Request class.
 *
 * Classe de gerenciamento de dados de uma requisição HTTP.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Controller_Request implements CoreExt_Controller_Request_Interface
{
  /**
   * Opções de configuração geral da classe.
   * @var array
   */
  protected $_options = array(
    'baseurl' => NULL
  );

  /**
   * Construtor.
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    $this->setOptions($options);
  }

  /**
   * @see CoreExt_Configurable#setOptions($options)
   */
  public function setOptions(array $options = array())
  {
    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      throw new InvalidArgumentException(
        sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    $this->_options = array_merge($this->getOptions(), $options);
    return $this;
  }

  /**
   * @see CoreExt_Configurable#getOptions()
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Verifica se uma opção está setada.
   *
   * @param string $key
   * @return bool
   */
  protected function _hasOption($key)
  {
    return array_key_exists($key, $this->_options);
  }

  /**
   * Retorna um valor de opção de configuração ou NULL caso a opção não esteja
   * setada.
   *
   * @param string $key
   * @return mixed|NULL
   */
  protected function _getOption($key)
  {
    return $this->_hasOption($key) ? $this->_options[$key] : NULL;
  }

  /**
   * Implementação do método mágico __get().
   *
   * @param  string $key
   * @return mixed
   */
  public function __get($key)
  {
    switch (true) {
      case isset($_GET[$key]):
        return $_GET[$key];
      case isset($_POST[$key]):
        return $_POST[$key];
      case isset($_COOKIE[$key]):
        return $_COOKIE[$key];
      case isset($_SERVER[$key]):
        return $_SERVER[$key];
      default:
        break;
    }
    return NULL;
  }

  /**
   * Getter para as variáveis de requisição.
   * @param string $key
   * @return mixed
   */
  public function get($key)
  {
    return $this->__get($key);
  }

  /**
   * Implementação do método mágico __isset().
   *
   * @link   http://php.net/manual/en/language.oop5.overloading.php
   * @param  string $key
   * @return bool
   */
  public function __isset($key)
  {
    $val = $this->$key;
    return isset($val);
  }

  /**
   * Setter para a opção de configuração baseurl.
   * @param string $url
   * @return CoreExt_Controller_Request_Interface Provê interface fluída
   */
  public function setBaseurl($url)
  {
    $this->setOptions(array('baseurl' => $url));
    return $this;
  }

  /**
   * Getter para a opção de configuração baseurl.
   *
   * Caso a opção não esteja configurada, determina um valor baseado na
   * variável $_SERVER['REQUEST_URI'] da requisição, usando apenas os
   * componentes scheme e path da URL. Veja {@link http://php.net/parse_url}
   * para mais informações sobre os componentes de uma URL.
   *
   * @return string
   */
  public function getBaseurl()
  {
    if (is_null($this->_getOption('baseurl'))) {
      require_once 'CoreExt/View/Helper/UrlHelper.php';
      $url = CoreExt_View_Helper_UrlHelper::url(
        $this->get('REQUEST_URI'),
        array('absolute' => TRUE, 'components' => CoreExt_View_Helper_UrlHelper::URL_HOST)
      );
      $this->setBaseurl($url);
    }
    return $this->_getOption('baseurl');
  }
}