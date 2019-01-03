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
 * @package     CoreExt_DataMapper
 * @subpackage  IntegrationTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once __DIR__.'/../_stub/EntityDataMapper.php';

/**
 * CoreExt_DataMapper_StaticAdapterTest class.
 *
 * Classe com testes para assegurar que a interface de configuração de adapter
 * de banco de dados estática de CoreExt_DataMapper funciona.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  IntegrationTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_DataMapper_StaticAdapterTest extends IntegrationBaseTest
{
  protected function setUp(): void
  {
    parent::setUp();
    CoreExt_DataMapper::setDefaultDbAdapter($this->getDbAdapter());
  }

  protected function tearDown(): void
  {
    parent::tearDown();
    CoreExt_DataMapper::resetDefaultDbAdapter();
  }

  public function getSetUpOperation()
  {
    return PHPUnit_Extensions_Database_Operation_Factory::NONE();
  }

  /**
   * Esse método precisa ser sobrescrito mas a utilidade dele nesse teste é
   * irrelevante.
   */
  public function getDataSet()
  {
    return $this->createXMLDataSet($this->getFixture('pessoa.xml'));
  }

  public function testAdapterParaNovaInstanciaDeDataMapperEOStatic()
  {
    $entityMapper = new CoreExt_EntityDataMapperStub();
    $this->assertSame($this->getDbAdapter(), $entityMapper->getDbAdapter());

    $entityMapper = new CoreExt_EntityDataMapperStub();
    $this->assertSame($this->getDbAdapter(), $entityMapper->getDbAdapter());
  }

  public function testAdapterNoConstrutorSobrescreveOAdapterStaticPadrao()
  {
    $db = new CustomPdo('sqlite::memory:');
    $entityMapper = new CoreExt_EntityDataMapperStub($db);
    $this->assertSame($db, $entityMapper->getDbAdapter());
  }

  public function testResetarAdapterFazComQueODataMapperInstancieUmNovoAdapter()
  {
    CoreExt_EntityDataMapperStub::resetDefaultDbAdapter();
    $entityMapper = new CoreExt_EntityDataMapperStub();
    $this->assertNotSame($this->getDbAdapter(), $entityMapper->getDbAdapter());
  }
}