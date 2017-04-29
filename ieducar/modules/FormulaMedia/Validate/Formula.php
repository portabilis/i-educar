<?php

/**
 * i-Educar - Sistema de gestדo escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaם
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa י software livre; vocך pode redistribuם-lo e/ou modificב-lo
 * sob os termos da Licenחa Pתblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versדo 2 da Licenחa, como (a seu critיrio)
 * qualquer versדo posterior.
 *
 * Este programa י distribuם­do na expectativa de que seja תtil, porיm, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implם­cita de COMERCIABILIDADE OU
 * ADEQUAֳַO A UMA FINALIDADE ESPECֽFICA. Consulte a Licenחa Pתblica Geral
 * do GNU para mais detalhes.
 *
 * Vocך deve ter recebido uma cףpia da Licenחa Pתblica Geral do GNU junto
 * com este programa; se nדo, escreva para a Free Software Foundation, Inc., no
 * endereחo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixדo <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Arquivo disponםvel desde a versדo 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Validate/Abstract.php';
require_once 'FormulaMedia/Model/Formula.php';

/**
 * FormulaMedia_Validate_Formula class.
 *
 * @author      Eriksen Costa Paixדo <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponםvel desde a versדo 1.1.0
 * @version     @@package_version@@
 */
class FormulaMedia_Validate_Formula extends CoreExt_Validate_Abstract
{
  /**
   * Referךncia para instגncia da classe FormulaMedia_Model_Formula do model.
   * @var FormulaMedia_Model_Formula
   */
  protected static $_model = NULL;

  /**
   * Por padrדo, exclui o tokens de nota de recuperaחדo.
   *
   * @see CoreExt_Validate_Abstract#_getDefaultOptions()
   */
  protected function _getDefaultOptions()
  {
    return array('excludeToken' => array('Rc'));
  }

  /**
   * @see CoreExt_Validate_Abstract#_validate()
   * @throws Exception|FormulaMedia_Validate_Exception
   */
  protected function _validate($value)
  {
    // Instancia
    if (is_null(self::$_model)) {
      self::$_model = new FormulaMedia_Model_Formula();
    }

    // Adiciona espaחos entre os parךnteses
    $value = self::$_model->replaceAliasTokens($value);

    $tokensAvailable = $this->_getTokens();
    $valueTokens     = explode(' ', $value);
    $missingTokens   = array();
    $numericTokens   = array();

    // Verifica se alguma token nדo permitida foi utilizada
    foreach ($valueTokens as $tk) {
      if ('' == ($tk = trim($tk))) {
        continue;
      }

      if (!in_array($tk, $tokensAvailable)) {
        if (!is_numeric($tk)) {
          $missingTokens[] = $tk;
        }
      }
      elseif (self::$_model->isNumericToken($tk)) {
        // Se for uma token numיrica, atribui um nתmero 1 para usar na fףrmula
        // e avaliar se nדo lanחa um erro no PHP
        $numericTokens[$tk] = 1;
      }
    }

    if (0 < count($missingTokens)) {
      throw new Exception('As variáveis ou símbolos não são permitidos: ' . implode(', ', $missingTokens));
    }

    // Verifica se a fףrmula י parseada corretamente pelo PHP
    $formula = self::$_model->replaceTokens($value, $numericTokens);

    /*
     * Eval, com surpressדo de erro para evitar interrupחדo do script. Se
     * retornar algum valor diferente de NULL, assume como erro de sintaxe.
     */
    $evaled = @eval('?><?php $result = ' . $formula . '; ?>');
    if (!is_null($evaled)) {
      require_once 'FormulaMedia/Validate/Exception.php';
      throw new FormulaMedia_Validate_Exception('A fórmula apresenta erros.'
                . ' Verifique algum parêntese faltante ou um sinal de operação'
                . ' matemática sem um operando.');
    }

    return TRUE;
  }

  /**
   * Retorna as tokens disponםveis para o validador. Uma token pode ser
   * excluםda usando a opחדo excludeToken.
   *
   * @return array
   */
  protected function _getTokens()
  {
    $tokens = self::$_model->getTokens();
    $tokensAvailable = array();

    if ($this->_hasOption('excludeToken') &&
        is_array($this->getOption('excludeToken')) &&
        0 < count($this->getOption('excludeToken'))
    ) {
      $excludeToken = $this->getOption('excludeToken');
      foreach ($tokens as $token) {
        if (!in_array($token, $excludeToken)) {
          $tokensAvailable[] = $token;
        }
      }
    }
    else {
      $tokensAvailable = $tokens;
    }

    return $tokensAvailable;
  }
}
