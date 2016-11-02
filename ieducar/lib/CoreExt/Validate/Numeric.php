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
 * @package   CoreExt_Validate
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Validate/Abstract.php';
require_once 'CoreExt/Locale.php';

/**
 * CoreExt_Validate_Numeric class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Validate_Numeric extends CoreExt_Validate_Abstract
{
  /**
   * @see CoreExt_Validate_Abstract#_getDefaultOptions()
   */
  protected function _getDefaultOptions()
  {
    return array(
      'min'       => NULL,
      'max'       => NULL,
      'trim'      => FALSE,
      'invalid'   => 'O valor "@value" não é um tipo numérico',
      'min_error' => '"@value" é menor que o valor mínimo permitido (@min)',
      'max_error' => '"@value" é maior que o valor máximo permitido (@max)',
    );
  }

  /**
   * @see CoreExt_DataMapper#_getFindStatment($pkey) Sobre a conversão com floatval()
   * @see CoreExt_Validate_Abstract#_validate($value)
   */
  protected function _validate($value)
  {
    if (FALSE === $this->getOption('required') && is_null($value)) {
      return TRUE;
    }

    if (!is_numeric($value)) {
      throw new Exception($this->_getErrorMessage('invalid', array('@value' => $value)));
    }

    // Converte usando floatval para evitar problemas com range do tipo int.
    $value = floatval($value);

    if ($this->_hasOption('min') &&
      $value < floatval($this->getOption('min'))) {
      throw new Exception($this->_getErrorMessage('min_error', array(
        '@value' => $value, '@min' => $this->getOption('min')
      )));
    }

    if ($this->_hasOption('max') &&
      $value > floatval($this->getOption('max'))) {
      throw new Exception($this->_getErrorMessage('max_error', array(
        '@value' => $value, '@max' => $this->getOption('max')
      )));
    }

    return TRUE;
  }

  /**
   * Realiza um sanitização de acordo com o locale, para permitir que valores
   * flutuantes ou números de precisão arbitrária utilizem a pontuação sem
   * localização.
   *
   * @see CoreExt_Validate_Abstract#_sanitize($value)
   */
  protected function _sanitize($value)
  {
    $locale = CoreExt_Locale::getInstance();
    $decimalPoint = $locale->getCultureInfo('decimal_point');

    // Verifica se possui o ponto decimal do locale e substitui para o
    // padrão do locale en_US (ponto ".")
    if (FALSE !== strstr($value, $decimalPoint)) {
      $value = strtr($value, $decimalPoint, '.');
      $value = floatval($value);
    }

    return parent::_sanitize($value);
  }
}