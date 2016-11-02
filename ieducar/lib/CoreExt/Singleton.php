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
 * @package   CoreExt_Singleton
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * CoreExt_Singleton abstract class.
 *
 * Funciona como uma interface de atalho para minimizar a duplicação de código
 * para criar instâncias singleton. Internamente, entretanto, funciona como um
 * {@link http://martinfowler.com/eaaCatalog/registry.html Registry} já que
 * todas as suas subclasses estarão armazenadas em um array estático desta
 * classe.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://martinfowler.com/eaaCatalog/registry.html Registry pattern
 * @package   CoreExt_Singleton
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class CoreExt_Singleton
{
  /**
   * A instância singleton de CoreExt_Singleton
   * @var array
   */
  private static $_instance = array();

  /**
   * Construtor.
   */
  private function __construct()
  {
  }

  /**
   * Sobrescreva esse método para garantir que a subclasse possa criar um
   * singleton. Esta deve fazer uma chamada ao método _getInstance, passando
   * uma string que tenha como valor o nome da classe. Uma forma conveniente
   * de fazer isso é chamando _getInstance passando como parâmetro a constante
   * mágica __CLASS__.
   *
   * Exemplo:
   * <code>
   * <?php
   * ... // extends CoreExt_Singleton
   * public static function getInstance()
   * {
   *   return self::_getInstance(__CLASS__);
   * }
   * </code>
   *
   * @return CoreExt_Singleton
   */
  public static function getInstance()
  {
    require_once 'CoreExt/Exception.php';
    throw new CoreExt_Exception('É necessário sobrescrever o método "getInstance()" de CoreExt_Singleton.');
  }

  /**
   * Retorna uma instância singleton, instanciando-a quando necessário.
   *
   * @param  string $self  Nome da subclasse de CoreExt_Singleton que será instanciada
   * @return CoreExt_Singleton
   */
  protected static function _getInstance($self)
  {
    if (!isset(self::$_instance[$self])) {
      self::$_instance[$self] = new $self();
    }
    return self::$_instance[$self];
  }
}