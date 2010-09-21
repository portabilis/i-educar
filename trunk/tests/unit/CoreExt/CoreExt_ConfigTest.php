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

require_once 'CoreExt/Config.class.php';

/**
 * CoreExt_ConfigTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_ConfigTest extends UnitBaseTest
{
  public function testConfigHasValueFromArray()
  {
    $arr = array(
      'app' => array(
        'database' => array(
          'dbname'   => 'ieducardb',
          'username' => 'ieducaruser',
          'password' => '12345678'
        )
      ),
      'version' => 'Development'
    );

    $config = new CoreExt_Config($arr);
    $this->assertEquals('ieducardb', $config->app->database->dbname);
    $this->assertEquals('Development', $config->version);
  }

  public function testHasOneItem()
  {
    $arr = array(
      'app' => array('database' => '')
    );

    $config = new CoreExt_Config($arr);
    $this->assertEquals(1, $config->count());
  }

  public function testHasTwoItems()
  {
    $arr = array(
      'app' => array('database' => '', 'template' => ''),
      'php' => ''
    );

    $config = new CoreExt_Config($arr);
    $this->assertEquals(2, $config->count());
    $this->assertEquals(2, $config->app->count());
  }

  /**
   * @expectedException Exception
   */
  public function testGetNotExistNotProvidingDefaultValue()
  {
    $arr = array('app' => array('database' => array('dbname' => 'ieducardb')));

    $config = new CoreExt_Config($arr);
    $hostname = $config->get($config->app->database->hostname);
    $this->assertEquals($hostname, '127.0.0.1');
  }

  public function testGetNotExistProvidingDefaultValue()
  {
    $arr = array('app' => array('database' => array('dbname' => 'ieducardb')));

    $config = new CoreExt_Config($arr);
    $hostname = $config->get($config->app->database->hostname, '127.0.0.1');
    $this->assertEquals($hostname, '127.0.0.1');
  }

  public function testGetExistProvidingDefaultValue()
  {
    $arr = array('app' => array('database' => array('dbname' => 'ieducardb')));

    $config = new CoreExt_Config($arr);
    $hostname = $config->get($config->app->database->dbname, '127.0.0.1');
    $this->assertEquals($hostname, 'ieducardb');
  }

  public function testObjectIterates()
  {
    $arr = array(
      'index1' => 1,
      'index2' => 2
    );

    $config = new CoreExt_Config($arr);

    $this->assertEquals(1, $config->current());

    $config->next();
    $this->assertEquals(2, $config->current());

    foreach ($config as $key => $val) {}

    $config->rewind();
    $this->assertEquals(1, $config->current());
  }

  public function testTransformObjectInArray()
  {
    $arr = array(6, 3, 3);

    $config = new CoreExt_Config($arr);

    $this->assertEquals($arr, $config->toArray());
  }
}