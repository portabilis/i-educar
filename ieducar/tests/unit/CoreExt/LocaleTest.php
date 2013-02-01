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
 * @package     CoreExt_Locale
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/tests/unit/CoreExt/EnumTest.php 770 2009-11-24T18:31:56.633773Z eriksen  $
 */

require_once 'CoreExt/Locale.php';

/**
 * CoreExt_LocaleTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Locale
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_LocaleTest extends UnitBaseTest
{
  protected $_instance = NULL;

  protected function setUp()
  {
    $this->_instance = CoreExt_Locale::getInstance();
    $this->_instance->resetLocale();
  }

  public function testFloatComOLocaleDefault()
  {
    $float = 3.5;
    $this->assertEquals('3.5', (string) $float);
  }

  public function testLocaleNaoDisponivelCaiEmFallback()
  {
    $mock = $this->setExcludedMethods(array('setLocale'))
                   ->getCleanMock('CoreExt_Locale');

    $mock->expects($this->any())
         ->method('_setLocale')
         ->will($this->onConsecutiveCalls(NULL, 'ASCII', 'pt_BR.UTF-8'));

    $mock->setLocale('pt_BR');
  }

  public function testFloatComUmLocaleQueUsaVirgulaParaSepararDecimais()
  {
    $this->_instance->setCulture('pt_BR')->setLocale();
    $float = 3.5;
    $this->assertEquals('3,5', (string) $float);
  }

  public function testResetDeLocale()
  {
    $this->_instance->setLocale('pt_BR');
    $float = 3.5;
    $this->assertEquals('3,5', (string) $float);
    $this->_instance->resetLocale();
    $this->assertEquals('3.5', (string) $float);
  }

  public function testInformacaoDeNumericosDoLocale()
  {
    $cultureInfo = $this->_instance->getCultureInfo();
    $this->assertEquals(18, count($cultureInfo));
    $this->assertEquals('.', $this->_instance->getCultureInfo('decimal_point'));

    $this->_instance->setLocale('pt_BR');
    $this->assertEquals(',', $this->_instance->getCultureInfo('decimal_point'));
  }
}