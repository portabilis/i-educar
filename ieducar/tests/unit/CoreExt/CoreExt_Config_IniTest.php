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
 * @package     CoreExt
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Config/Ini.class.php';

/**
 * CoreExt_Config_IniTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Config_IniTest extends UnitBaseTest
{
  public function testParsedIni()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar.ini');
    $this->assertNotNull($ini->app);
  }

  public function testChangeEnviroment()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar.ini');
    $this->assertEquals(FALSE, (bool) $ini->php->display_errors);

    $ini->changeEnviroment('development');
    $this->assertEquals(TRUE, (bool) $ini->php->display_errors);

    $ini->changeEnviroment('testing');
    $this->assertEquals(TRUE, (bool) $ini->php->display_errors);

    $ini->changeEnviroment();
    $this->assertEquals(FALSE, (bool) $ini->php->display_errors);
  }

  /**
   * @expectedException Exception
   */
  public function testInvalidIniFile()
  {
    // Tentando carregar configuração do blackhole!
    $ini = new CoreExt_Config_Ini('/dev/null');
  }

  /**
   * @expectedException Exception
   */
  public function testSectionExtendsMoreThanOne()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar-extends-broken.ini');
  }

  /**
   * @expectedException Exception
   */
  public function testIniSyntaxError()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar-syntax-broken.ini');
  }

  /**
   * @expectedException Exception
   */
  public function testSectionInheritanceNotExist()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar-inheritance-broken.ini');
  }
}