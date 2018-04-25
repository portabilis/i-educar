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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Validate/Abstract.php';
require_once 'FormulaMedia/Model/Formula.php';

/**
 * FormulaMedia_Validate_Formula class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class FormulaMedia_Validate_Formula extends CoreExt_Validate_Abstract
{
  /**
   * Referência para instância da classe FormulaMedia_Model_Formula do model.
   * @var FormulaMedia_Model_Formula
   */
  protected static $_model = NULL;

  /**
   * Por padrão, exclui o tokens de nota de recuperação.
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

    // Adiciona espaços entre os parênteses
    $value = self::$_model->replaceAliasTokens($value);

    $tokensAvailable = $this->_getTokens();
    $valueTokens     = explode(' ', $value);
    $missingTokens   = array();
    $numericTokens   = array();

    // Verifica se alguma token não permitida foi utilizada
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
        // Se for uma token numérica, atribui um número 1 para usar na fórmula
        // e avaliar se não lança um erro no PHP
        $numericTokens[$tk] = 1;
      }
    }

    if (0 < count($missingTokens)) {
      throw new Exception('As variáveis ou símbolos não são permitidos: ' . implode(', ', $missingTokens));
    }

    // Verifica se a fórmula é parseada corretamente pelo PHP
    $formula = self::$_model->replaceTokens($value, $numericTokens);

    /*
     * Eval, com surpressão de erro para evitar interrupção do script. Se
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
   * Retorna as tokens disponíveis para o validador. Uma token pode ser
   * excluída usando a opção excludeToken.
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