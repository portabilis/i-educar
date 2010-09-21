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
 * @package   CoreExt_Locale
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Singleton.php';

/**
 * CoreExt_Locale class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://br2.php.net/setlocale Documentação da função setlocale()
 * @package   CoreExt_Locale
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Verificar se existem implicações em sistemas operacionais que
 *            especificam um encoding junto a locale string.
 * @todo      Utilizar um encoding de locale compatível com o encoding de
 *            escaping de CoreExt_View, quando este for implementado.
 * @version   @@package_version@@
 */
class CoreExt_Locale extends CoreExt_Singleton
{
  /**
   * @var string
   */
  protected $_culture = 'en_US';

  /**
   * Culture padrão para en_US, para evitar problemas com cálculos com
   * números de precisão arbitrária.
   * @var string
   */
  protected $_defaultCulture = 'en_US';

  /**
   * Cache de informações sobre um culture.
   * @var array
   */
  protected $_cultureInfo = array();

  /**
   * Culture configurada atualmente. Informacional.
   * @var string
   */
  public $actualCulture = 'en_US';

  /**
   * @see CoreExt_Singleton#getInstance()
   */
  public static function getInstance()
  {
    $instance = self::_getInstance(__CLASS__);
    $instance->setLocale($instance->getCulture());
    return $instance;
  }

  /**
   * Setter.
   *
   * @param string $culture
   * @return CoreExt_Locale Provê interface fluída
   */
  public function setCulture($culture)
  {
    $this->_culture = $culture;
    return $this;
  }

  /**
   * Getter.
   * @return string
   */
  public function getCulture()
  {
    return $this->_culture;
  }

  /**
   * Setter.
   *
   * Configura o locale para uma cultura especifica ou usa o valor corrente
   * da classe.
   *
   * Tenta usar um locale ISO-8859-1 (por ser padrão do banco de dados e da
   * aplicação). Como fallback, usa o locale genérico C e um UTF-8 para
   * LC_NUMERIC.
   *
   * Como não existe consistência na nomenclarura dos encodings entre os
   * sistemas operacionais, tenta variações como ISO-8859-1 e ISO_8859_1.
   *
   * @link http://linux.die.net/man/3/setlocale Linux setlocale(3) man page
   * @param string|NULL $culture
   */
  public function setLocale($culture = NULL)
  {
    if (is_null($culture)) {
      $culture = $this->getCulture();
    }

    // Nos velhos tempos, o Linux vinha com locales ISO-8859-1 (European LATIN1)
    $actualCulture = $this->_setLocale(LC_ALL, array($culture . '.ISO-8859-1',
      $culture . '.iso-8859-1', $culture . '.ISO88591', $culture . '.iso88591',
      $culture . '.ISO8859-1', $culture . '.iso8859-1', $culture . 'ISO_8859_1',
      $culture . '.iso_8859_1', $culture . '.ISO8859_1', $culture . '.iso8859_1'));

    // Fallback. Caso não encontre um locale ISO-8859-1, usa um C (ASCII-like)
    // e um UTF-8 somente para numéricos. No final, tudo é manuseado em C
    // pelas funções de formatação de string, e como usam o locale C, será
    // compatível com a definição ISO-8859-1.
    if (FALSE == $actualCulture) {
      $actualCulture = array();
      $actualCulture['LC_ALL']     = $this->_setlocale(LC_ALL, array('C'));
      $actualCulture['LC_NUMERIC'] = $this->_setlocale(LC_NUMERIC, array($culture.'.UTF-8',
        $culture . '.UTF8', $culture . '.utf-8', $culture . '.utf8',
        $culture . '.UTF_8', $culture . '.utf_8'));
    }

    $this->actualCulture = $actualCulture;

    // Cache de informações do culture
    if (!isset($this->_cultureInfo[$culture])) {
      $this->_cultureInfo[$culture] = localeconv();
    }
  }

  /**
   * Chama a função setlocale().
   *
   * @param  string $category
   * @param  array  $locale
   * @return NULL|string  Retorna NULL em caso de erro
   */
  protected function _setLocale($category, array $locale)
  {
    return setlocale($category, $locale);
  }

  /**
   * Reseta o locale para en_US.
   */
  public function resetLocale()
  {
    $this->setLocale($this->_defaultCulture);
  }

  /**
   *
   * @param string|NULL $index
   * @return array|string
   */
  public function getCultureInfo($index = NULL)
  {
    $info = localeconv();
    if (NULL != $index && isset($info[$index])) {
      $info = $info[$index];
    }
    return $info;
  }
}