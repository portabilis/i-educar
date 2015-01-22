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
* @author      Lucas D'Avila <lucas@ieducativa.com.br>
* @category    i-Educar
* @license     @@license@@
* @package     CoreExt_Validate
* @since       Arquivo disponível desde a versão 1.1.0
* @version     $Id$
*/

require_once 'CoreExt/Validate/Abstract.php';

/**
* CoreExt_Validate_Date class.
*
* @author      Lucas D'Avila <lucas@ieducativa.com.br>
* @category    i-Educar
* @license     @@license@@
* @package     CoreExt_Validate
* @since       Classe disponível desde a versão 1.1.0
* @version     @@package_version@@
*/
class CoreExt_Validate_Date extends CoreExt_Validate_Abstract
{
  /**
  * @see CoreExt_Validate_Abstract#_getDefaultOptions()
  */
  protected function _getDefaultOptions()
  {
    return array(
      'invalid' => 'Data inválida.'
    );
  }

  /**
  * @see CoreExt_DataMapper#_getFindStatment($pkey) Sobre a conversão com floatval()
  * @see CoreExt_Validate_Abstract#_validate($value)
  */
  protected function _validate($value)
  {
    $hasSlash = strpos($value, '/') !== false;

    if ($hasSlash) {
      list($day, $month, $year) = explode('/', $value);
    } else {
      list($year, $month, $day) = explode('-', $value);
    }

    if (! checkdate($month, $day, $year)) {
      throw new Exception($this->_getErrorMessage('invalid'));
    }

    return true;
  }

  /**
  * Mensagem padrão para erros de valor obrigatório.
  * @var string
  */
  protected $_requiredMessage = 'Informe uma data.';
}
