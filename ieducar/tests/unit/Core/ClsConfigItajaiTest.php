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
 * @package     Core
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'clsConfigItajai.inc.php';


/**
 * clsConfigItajaiTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Core
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.0.1
 * @version     @@package_version@@
 */
class ClsConfigItajai extends UnitBaseTest
{
  protected $config = NULL;

  protected function setUp()
  {
    $this->config = new clsConfig();
  }

  public function testConfigInstituicao()
  {
    $this->assertEquals('i-Educar - ', $this->config->_instituicao);
  }

  public function testArrayConfigHasEmailsAdministradores()
  {
    $this->assertTrue((bool) count($this->config->arrayConfig['ArrStrEmailsAdministradores']));
  }

  public function testArrayCheckEmailAdministradores()
  {
    $this->assertEquals('seu.email@example.com',
      $this->config->arrayConfig['ArrStrEmailsAdministradores'][0]);
  }

  public function testArrayConfigDirectoryTemplates()
  {
    $this->assertEquals('templates/', $this->config->arrayConfig['strDirTemplates']);
  }

  public function testArrayConfigIntSegundosQuerySql()
  {
    $this->assertEquals(3, $this->config->arrayConfig['intSegundosQuerySQL']);
  }

  public function testArrayConfigIntSegundosPagina()
  {
    $this->assertEquals(5, $this->config->arrayConfig['intSegundosProcessaPagina']);
  }
}