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
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'FormulaMedia/Validate/Formula.php';
require_once 'FormulaMedia/Validate/Exception.php';

/**
 * ValidateFormulaTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ValidateFormulaTest extends UnitBaseTest
{
  public function testFormulaValida()
  {
    $formula = 'Se / Et';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  public function testFormulaValidaUsandoAliasDeMultiplicacao()
  {
    $formula = 'Se x 0.99 / Et';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  public function testFormulaValidaComNumericos()
  {
    $formula = 'Se * 0.5 / Et';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  /**
   * @expectedException Exception
   */
  public function testFormulaInvalidaQuandoUtilizaTokenNaoPermitido()
  {
    $formula = 'Rc * 0.4 + Se * 0.6';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  public function testFormulaValidaUsandoParenteses()
  {
    $formula = '(Rc * 0.4) + (Se * 0.6)';
    $validator = new FormulaMedia_Validate_Formula(array('excludeToken' => NULL));
    $this->assertTrue($validator->isValid($formula));
  }

  /**
   * @expectedException Error
   */
  public function testFormulaInvalidaPorErroDeSintaxe()
  {
    $formula = '(Rc * 0.4) + (Se * 0.6) ()';
    $validator = new FormulaMedia_Validate_Formula(array('excludeToken' => NULL));
    $this->assertTrue($validator->isValid($formula));
  }
}