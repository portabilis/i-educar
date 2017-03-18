<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Locale
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Singleton.php';

/**
 * CoreExt_Locale class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://br2.php.net/setlocale Documenta��o da fun��o setlocale()
 * @package   CoreExt_Locale
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @todo      Verificar se existem implica��es em sistemas operacionais que
 *            especificam um encoding junto a locale string.
 * @todo      Utilizar um encoding de locale compat�vel com o encoding de
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
   * Culture padr�o para en_US, para evitar problemas com c�lculos com
   * n�meros de precis�o arbitr�ria.
   * @var string
   */
  protected $_defaultCulture = 'en_US';

  /**
   * Cache de informa��es sobre um culture.
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
   * @return CoreExt_Locale Prov� interface flu�da
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
   * Tenta usar um locale  (por ser padr�o do banco de dados e da
   * aplica��o). Como fallback, usa o locale gen�rico C e um UTF-8 para
   * LC_NUMERIC.
   *
   * Como n�o existe consist�ncia na nomenclarura dos encodings entre os
   * sistemas operacionais, tenta varia��es como  e ISO_8859_1.
   *
   * @link http://linux.die.net/man/3/setlocale Linux setlocale(3) man page
   * @param string|NULL $culture
   */
  public function setLocale($culture = NULL)
  {
    if (is_null($culture)) {
      $culture = $this->getCulture();
    }

    // Nos velhos tempos, o Linux vinha com locales  (European LATIN1)
    $actualCulture = $this->_setLocale(LC_ALL, array($culture . '.',
      $culture . '.', $culture . '.ISO88591', $culture . '.iso88591',
      $culture . '.ISO8859-1', $culture . '.iso8859-1', $culture . 'ISO_8859_1',
      $culture . '.iso_8859_1', $culture . '.ISO8859_1', $culture . '.iso8859_1'));

    // Fallback. Caso n�o encontre um locale , usa um C (ASCII-like)
    // e um UTF-8 somente para num�ricos. No final, tudo � manuseado em C
    // pelas fun��es de formata��o de string, e como usam o locale C, ser�
    // compat�vel com a defini��o .
    if (FALSE == $actualCulture) {
      $actualCulture = array();
      $actualCulture['LC_ALL']     = $this->_setlocale(LC_ALL, array('C'));
      $actualCulture['LC_NUMERIC'] = $this->_setlocale(LC_NUMERIC, array($culture.'.UTF-8',
        $culture . '.UTF8', $culture . '.utf-8', $culture . '.utf8',
        $culture . '.UTF_8', $culture . '.utf_8'));
    }

    $this->actualCulture = $actualCulture;

    // Cache de informa��es do culture
    if (!isset($this->_cultureInfo[$culture])) {
      $this->_cultureInfo[$culture] = localeconv();
    }
  }

  /**
   * Chama a fun��o setlocale().
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