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

/**
 * CoreExt_Validate_Choice class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Validate_Choice extends CoreExt_Validate_Abstract
{
  /**
   * @see CoreExt_Validate_Abstract#_getDefaultOptions()
   */
  protected function _getDefaultOptions()
  {
    $options = array(
      'choices'  => array(),
      'multiple' => FALSE,
      'trim'     => FALSE,
      'choice_error'   => 'A opção "@value" não existe.',
    );

    $options['multiple_error'] = array(
      'singular' => $options['choice_error'],
      'plural'   => 'As opções "@value" não existem.'
    );

    return $options;
  }

  /**
   * @see CoreExt_Validate_Abstract#_validate($value)
   */
  protected function _validate($value)
  {
    if ($this->_hasOption('choices')) {
      $value   = $this->_getStringArray($value);
      $choices = $this->_getStringArray($this->getOption('choices'));

      if ($this->_hasOption('multiple') && FALSE == $this->getOption('multiple')) {
        if (in_array($value, $choices, TRUE)) {
          return TRUE;
        }
        throw new Exception($this->_getErrorMessage('choice_error', array('@value' => $this->getSanitizedValue())));
      }
      else {
        if (in_array($value, array($choices), TRUE)) {
          return TRUE;
        }
        throw new Exception($this->_getErrorMessage(
          'multiple_error',
          array('@value' => array_diff($value, $this->getOption('choices'))))
        );
      }
    }
    return TRUE;
  }

  /**
   * Retorna um array de strings ou um valor numérico como string.
   * @param array|numeric $value
   * @return array|string
   */
  protected function _getStringArray($value)
  {
    if (is_array($value)) {
      $return = array();
      foreach ($value as $v) {
        $return[] = (string) $v;
      }
      return $return;
    }
    return (string) $value;
  }
}