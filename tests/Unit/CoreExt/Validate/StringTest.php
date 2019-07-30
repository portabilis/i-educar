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

require_once 'CoreExt/Validate/String.php';

/**
 * CoreExt_Validate_StringTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Validate_StringTest extends PHPUnit\Framework\TestCase
{
  protected $_validator = NULL;

  protected function setUp(): void
  {
    $this->_validator = new CoreExt_Validate_String();
  }

  /**
   * @expectedException Exception
   */
  public function testStringSomenteEspacoLancaExcecaoPorSerObrigatorio()
  {
    // São três espaços ascii 20.
    $this->assertTrue($this->_validator->isValid('   '));
  }

  public function testStringSemAlterarConfiguracaoBasica()
  {
    $this->assertTrue($this->_validator->isValid('abc'));
  }

  /**
   * @expectedException Exception
   */
  public function testStringMenorQueOTamanhoMinimoLancaExcecao()
  {
    $this->_validator->setOptions(array('min' => 5));
    $this->assertTrue($this->_validator->isValid('Foo'));
  }

  /**
   * @expectedException Exception
   */
  public function testAlfaStringQueOTamanhoMaximoLancaExcecao()
  {
    $this->_validator->setOptions(array('max' => 2));
    $this->assertTrue($this->_validator->isValid('Foo'));
  }
}
