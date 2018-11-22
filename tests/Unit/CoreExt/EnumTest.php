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
 * @package     CoreExt_Enum
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once __DIR__.'/_stub/Enum1.php';
require_once __DIR__.'/_stub/Enum2.php';
require_once __DIR__.'/_stub/EnumCoffee.php';
require_once __DIR__.'/_stub/EnumString.php';

/**
 * CoreExt_EnumTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Enum
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_EnumTest extends PHPUnit\Framework\TestCase
{
  public function testRetornaTodosOsValoresDoEnum()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(array(1), $enum->getKeys());
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(array(2), $enum->getKeys());
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals(array(0, 1, 2), $enum->getKeys());
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals(array('red'), $enum->getKeys());
  }

  public function testItemDeEnumRetornaDescricao()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(1, $enum->getValue(CoreExt_Enum1Stub::ONE));
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(2, $enum->getValue(CoreExt_Enum2Stub::TWO));
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals('Mocha', $enum->getValue(CoreExt_EnumCoffeeStub::MOCHA));
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals('#FF0000', $enum->getValue(CoreExt_EnumStringStub::RED));
  }

  public function testEnumAcessadosComoArray()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(1, $enum[CoreExt_Enum1Stub::ONE]);
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(2, $enum[CoreExt_Enum2Stub::TWO]);
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals('Mocha', $enum[CoreExt_EnumCoffeeStub::MOCHA]);
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals('#FF0000', $enum[CoreExt_EnumStringStub::RED]);
  }

  public function testEnumAcessosDiversosComoArray()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertTrue(isset($enum[CoreExt_Enum1Stub::ONE]));

    $this->assertEquals(array(1), $enum->getValues());
    $this->assertEquals(array(1), $enum->getKeys());
    $this->assertEquals(array(1 => 1), $enum->getEnums());
    $this->assertEquals(1, $enum->getKey(CoreExt_Enum1Stub::ONE));

    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertTrue(isset($enum[CoreExt_EnumStringStub::RED]));

    $this->assertEquals(array('#FF0000'), $enum->getValues());
    $this->assertEquals(array('red'), $enum->getKeys());
    $this->assertEquals(array('red' => '#FF0000'), $enum->getEnums());
    $this->assertEquals('red', $enum->getKey('#FF0000'));
  }

  /**
   * @expectedException CoreExt_Exception
   */
  public function testEnumEApenasLeitura()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $enum['foo'] = 'bar';
  }

  /**
   * @expectedException CoreExt_Exception
   */
  public function testEnumNaoPermiteRemoverEntrada()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    unset($enum['foo']);
  }
}