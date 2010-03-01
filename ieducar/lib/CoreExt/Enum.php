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
 * @package   CoreExt_Enum
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Singleton.php';

/**
 * CoreExt_Enum abstract class.
 *
 * Provê uma interface simples de criação de instâncias semelhantes a um
 * Enum do Java.
 *
 * As semelhanças são poucas mas a intenção é a de dar uma forma direta de
 * criar tipos enumerados. Para isso, basta subclassificar essa classe e prover
 * valores para o array $_data. Adicionalmente, prover constantes que ajudaram
 * ao usuario da classe a facilmente acessar os valores dos enumerados é uma
 * sugestão.
 *
 * O stub de teste CoreExt_Enum1Stub é um exemplo de como criar tipos
 * enumerados.
 *
 * Essa classe implementa também a interface ArrayAccess de SPL, provendo acesso
 * fácil aos valores do enumerado em uma forma de array:
 *
 * <code>
 * <?php
 * $enum = new Enum();
 * print $enum[Enum::ONE];
 * </code>
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://br2.php.net/manual/en/class.arrayaccess.php ArrayAccess interface
 * @package   CoreExt_Singleton
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Verificar se é substituível pela implementação de Enum disponível
 *            em spl_types do Pecl {@link http://www.php.net/manual/en/splenum.construct.php SplEnum}.
 * @version   @@package_version@@
 */
abstract class CoreExt_Enum extends CoreExt_Singleton implements ArrayAccess
{
  /**
   * Array que emula um enum.
   * @var array
   */
  protected $_data = array();

  /**
   * Retorna o valor para um dado índice de CoreExt_Enum.
   * @param  string|int $key
   * @return mixed
   */
  public function getValue($key)
  {
    return $this->_data[$key];
  }

  /**
   * Retorna todos os valores de CoreExt_Enum.
   * @return array
   */
  public function getValues()
  {
    return array_values($this->_data);
  }

  /**
   * Retorna o valor da índice para um determinado valor.
   * @param  mixed $value
   * @return int|string
   */
  public function getKey($value)
  {
    return array_search($value, $this->_data);
  }

  /**
   * Retorna todos os índices de CoreExt_Enum.
   * @return array
   */
  public function getKeys()
  {
    return array_keys($this->_data);
  }

  /**
   * Retorna o array de enums.
   * @return array
   */
  public function getEnums()
  {
    return $this->_data;
  }

  /**
   * Implementa offsetExists da interface ArrayAccess.
   * @link   http://br2.php.net/manual/en/arrayaccess.offsetexists.php
   * @param  string|int $offset
   * @return bool
   */
  public function offsetExists($offset)
  {
    return isset($this->_data[$offset]);
  }

  /**
   * Implementa offsetUnset da interface ArrayAccess.
   * @link  http://br2.php.net/manual/en/arrayaccess.offsetunset.php
   * @throws CoreExt_Exception
   */
  public function offsetUnset($offset)
  {
    require_once 'CoreExt/Exception.php';
    throw new CoreExt_Exception('Um "' . get_class($this) . '" é um objeto read-only.');
  }

  /**
   * Implementa offsetSet da interface ArrayAccess.
   *
   * Uma objeto CoreExt_Enum é apenas leitura.
   *
   * @link   http://br2.php.net/manual/en/arrayaccess.offsetset.php
   * @param  string|int $offset
   * @param  mixed $value
   * @throws CoreExt_Exception
   */
  public function offsetSet($offset, $value)
  {
    require_once 'CoreExt/Exception.php';
    throw new CoreExt_Exception('Um "' . get_class($this) . '" é um objeto read-only.');
  }

  /**
   * Implementa offsetGet da interface ArrayAccess.
   *
   * @link   http://br2.php.net/manual/en/arrayaccess.offsetget.php
   * @param  string|int $offset
   * @return mixed
   */
  public function offsetGet($offset)
  {
    return $this->_data[$offset];
  }
}