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

require_once __DIR__.'/../_stub/Validate.php';
require_once 'CoreExt/Validate/String.php';

/**
 * CoreExt_ValidateTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_ValidateTest extends PHPUnit\Framework\TestCase
{
  protected $_validator = NULL;

  protected function setUp(): void
  {
    $this->_validator = new CoreExt_ValidateStub();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_validator->setOptions(array('invalidOption' => TRUE));
  }

  public function testConfiguraOpcaoDoValidator()
  {
    $this->_validator->setOptions(array('required' => FALSE));

    $options = $this->_validator->getOptions();
    $this->assertFalse($options['required']);

    $this->assertFalse($this->_validator->getOption('required'));
  }

  /**
   * @expectedException Exception
   */
  public function testValorStringSomenteEspacoRequerido()
  {
    // Um espaço ASCII
    $this->assertTrue($this->_validator->isValid(' '));
  }

  /**
   * @expectedException Exception
   */
  public function testValorNuloLancaExcecao()
  {
    $this->assertTrue($this->_validator->isValid(NULL));
  }

  /**
   * @expectedException Exception
   */
  public function testValorArrayVazioLancaExcecao()
  {
    $this->assertTrue($this->_validator->isValid(array()));
  }

  public function testValorNaoObrigatorioComConfiguracaoNaInstanciacao()
  {
    $validator = new CoreExt_Validate_String(array('required' => FALSE));
    $this->assertTrue($validator->isValid(''));
  }

  public function testValorNaoObrigatorioComConfiguracaoViaMetodo()
  {
    $this->_validator->setOptions(array('required' => FALSE));
    $this->assertTrue($this->_validator->isValid(''));
  }
}
