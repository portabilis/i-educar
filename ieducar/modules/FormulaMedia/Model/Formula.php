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

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'FormulaMedia/Model/TipoFormula.php';
require_once 'FormulaMedia/Validate/Formula.php';

/**
 * FormulaMedia_Model_Formula class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class FormulaMedia_Model_Formula extends CoreExt_Entity
{
  /**
   * Tokens válidos para uma fórmula.
   *
   * - Se: soma das notas de todas as etapas
   * - Et: total de etapas
   * - E1 a E10: nota na etapa En (fica limitado a 10 etapas)
   * - /: divisão
   * - *: multiplicação
   * - x: alias para *
   * - (: abre parêntese
   * - ): fecha parêntese
   *
   * @var array
   */
  protected $_tokens = array(
    'Se', 'Et', 'Rc',
    'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10',
    '/', '*', 'x', '+',
    '(', ')'
  );

  /**
   * Tokens que pode ser substituídas por valores numéricos.
   * @var array
   */
  protected $_tokenNumerics = array(
    'Se', 'Et', 'Rc',
    'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10'
  );

  /**
   * Atributos do model.
   * @var array
   */
  protected $_data = array(
    'instituicao'  => NULL,
    'nome'         => NULL,
    'formulaMedia' => NULL,
    'tipoFormula'  => NULL
  );

  /**
   * Referências.
   * @var array
   */
  protected $_references = array(
    'tipoFormula' => array(
      'value' => FormulaMedia_Model_TipoFormula::MEDIA_FINAL,
      'class' => 'FormulaMedia_Model_TipoFormula',
      'file'  => 'FormulaMedia/Model/TipoFormula.php'
    )
  );

  /**
   * Retorna as tokens permitidas para uma fórmula.
   * @return array
   */
  public function getTokens()
  {
    return $this->_tokens;
  }

  /**
   * Verifica se uma token pode receber um valor numérico.
   *
   * @param string $token
   * @return bool
   */
  public function isNumericToken($token)
  {
    return in_array($token, $this->_tokenNumerics);
  }

  /**
   * Substitui as tokens numéricas de uma fórmula, através de um array
   * associativo.
   *
   * <code>
   * <?php
   * $values = array(
   *   'E1' => 5,
   *   'E2' => 7,
   *   'E3' => 8,
   *   'E4' => 10,
   *   'Et' => 4,
   *   'Rc' => 0,
   *   'Se' => 30
   * );
   *
   * $formula = $formulaModel->replaceTokens($formulaModel->formulaMedia, $values);
   * </code>
   *
   * @param  string  $formula
   * @param  array   $values
   * @return string
   */
  public function replaceTokens($formula, $values = array())
  {
    $formula = $this->replaceAliasTokens($formula);

    $patterns = array();
    foreach ($values as $key => $value) {
      if ($this->isNumericToken($key)) {
        // Usa @ como delimitador para evitar problemas com o sinal de divisão
        $patterns[$key] = '@' . $key . '@';
      }
    }

    // Usa locale en_US para evitar problemas com pontos flutuantes
    $this->getLocale()->resetLocale();

    // Substitui os tokens
    $replaced = preg_replace($patterns, $values, $formula);

    // Retorna ao locale anterior
    $this->getLocale()->setLocale();

    return $replaced;
  }

  /**
   * Troca os tokens de alias pelos usados durante a execução da fórmula.
   * @param string $formula
   * @return string
   */
  public function replaceAliasTokens($formula)
  {
    return preg_replace(array('/\(/', '/\)/', '/x/'), array(' ( ', ' ) ', '*'), $formula);
  }

  /**
   *
   * @param array $values
   * @return NULL|numeric
   */
  public function execFormulaMedia(array $values = array())
  {
    $formula = $this->replaceTokens($this->formulaMedia, $values);
    return $this->_exec($formula);
  }

  /**
   * Executa um código de fórmula com eval.
   * @param string $code
   * @return NULL|numeric
   */
  protected function _exec($code)
  {
    $result = NULL;
    eval("?><?php \$result = " . $code . "; ?>");
    return $result;
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());
    $tipoFormula  = FormulaMedia_Model_TipoFormula::getInstance();

    // Se for de recuperação, inclui a token "Rc" como permitida.
    $formulaValidatorOptions = array();
    if (FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO == $this->get('tipoFormula')) {
      $formulaValidatorOptions = array('excludeToken' => NULL);
    }

    return array(
      'instituicao' => new CoreExt_Validate_Choice(array('choices' => $instituicoes)),
      'nome' => new CoreExt_Validate_String(array('min' => 5, 'max' => 50)),
      'formulaMedia' => new FormulaMedia_Validate_Formula($formulaValidatorOptions),
      'tipoFormula' => new CoreExt_Validate_Choice(array('choices' => $tipoFormula->getKeys()))
    );
  }

  /**
   * @see CoreExt_Entity#__toString()
   */
  public function __toString()
  {
    return $this->nome;
  }
}