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
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Validate/Numeric.php';
require_once 'CoreExt/Locale.php';

/**
 * CoreExt_Validate_NumericTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Validate_NumericTest extends UnitBaseTest
{
  protected $_validator = NULL;

  protected function setUp()
  {
    $this->_validator = new CoreExt_Validate_Numeric();
  }

  /**
   * @expectedException Exception
   */
  public function testValorStringVaziaLancaExcecao()
  {
    $this->_validator->isValid('');
  }

  /**
   * @expectedException Exception
   */
  public function testValorStringEspacoLancaExcecao()
  {
    // São três espaço ascii 20
    $this->_validator->isValid('   ');
  }

  /**
   * @expectedException Exception
   */
  public function testValorNullLancaExcecao()
  {
    $this->_validator->isValid(NULL);
  }

  /**
   * @expectedException Exception
   */
  public function testValorNaoNumericoLancaExcecao()
  {
    $this->_validator->isValid('zero');
  }

  public function testValorNullNaoLancaExcecaoSeRequiredForFalse()
  {
    $this->_validator->setOptions(array('required' => FALSE));
    $this->_validator->isValid(NULL);
  }

  public function testValorNumericoSemConfigurarOValidador()
  {
    $this->assertTrue($this->_validator->isValid(0));
    $this->assertTrue($this->_validator->isValid(1.5));
    $this->assertTrue($this->_validator->isValid(-1.5));
  }

  public function testValoresDentroDeUmRangeConfiguradoNoValidador()
  {
    $this->_validator->setOptions(array('min' => -50, 'max' => 50));
    $this->assertTrue($this->_validator->isValid(50));
    $this->assertTrue($this->_validator->isValid(50));
    $this->assertTrue($this->_validator->isValid(50.00));
    $this->assertTrue($this->_validator->isValid(-50.00));
    $this->assertTrue($this->_validator->isValid(49.9999));
    $this->assertTrue($this->_validator->isValid(-49.9999));
  }

  /**
   * @expectedException Exception
   */
  public function testValorMenorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('min' => 0));
    $this->_validator->isValid(-1);
  }

  /**
   * @expectedException Exception
   */
  public function testValorPontoFlutuanteMenorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('min' => 0));
    $this->_validator->isValid(-1.5);
  }

  /**
   * @expectedException Exception
   */
  public function testValorMaiorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('max' => 0));
    $this->_validator->isValid(1);
  }

  /**
   * @expectedException Exception
   */
  public function testValorPontoFlutuanteMaiorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('max' => 0));
    $this->_validator->isValid(1.5);
  }

  /**
   * @group CoreExt_Locale
   */
  public function testValorNumericoSemConfigurarOValidadorUsandoLocaleComSeparadorDecimalDiferenteDePonto()
  {
    $locale = CoreExt_Locale::getInstance();
    $locale->setCulture('pt_BR');

    $this->assertTrue($this->_validator->isValid('0,0'));
    $this->assertTrue($this->_validator->isValid('1,5'));
    $this->assertTrue($this->_validator->isValid('-1.5'));
  }

  /**
   * @group CoreExt_Locale
   */
  public function testValorNumericoSemConfigurarOValidadorUsandoLocaleComSeparadorPontoParaDecimal()
  {
    $locale = CoreExt_Locale::getInstance();
    $locale->setCulture('en_US');

    $this->assertTrue($this->_validator->isValid('0.0'));
    $this->assertTrue($this->_validator->isValid('1.5'));
    $this->assertTrue($this->_validator->isValid('-1.5'));
  }
}