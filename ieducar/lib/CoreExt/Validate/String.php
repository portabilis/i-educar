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
 * CoreExt_Validate_String class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Validate_String extends CoreExt_Validate_Abstract
{
  /**
   * @see CoreExt_Validate_Abstract#_getDefaultOptions()
   */
  protected function _getDefaultOptions()
  {
    return array(
      'min' => NULL,
      'max' => NULL,
      'min_error' => '"@value" é muito curto (@min caracteres no mínimo)',
      'max_error' => '"@value" é muito longo (@max caracteres no máximo)',
    );
  }

  /**
   * @see CoreExt_Validate_Abstract#_validate($value)
   */
  protected function _validate($value)
  {
    $length = strlen($value);

    if ($this->_hasOption('min') && $length < $this->getOption('min')) {
      throw new Exception($this->_getErrorMessage('min_error',
        array('@value' => $this->getSanitizedValue(), '@min' => $this->getOption('min')))
      );
    }

    if ($this->_hasOption('max') && $length > $this->getOption('max')) {
      throw new Exception($this->_getErrorMessage('max_error',
        array('@value' => $this->getSanitizedValue(), '@max' => $this->getOption('max')))
      );
    }

    return TRUE;
  }

  /**
   * @see CoreExt_Validate_Abstract#_sanitize($value)
   */
  protected function _sanitize($value)
  {
    return (string) parent::_sanitize($value);
  }
}