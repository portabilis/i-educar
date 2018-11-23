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

require_once __DIR__.'/../_stub/EntityCompoundDataMapper.php';

/**
 * CoreExt_DataMapper_IntegrationCompoundPkeyTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  IntegrationTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_DataMapper_IntegrationCompoundPkeyTest extends IntegrationBaseTest
{
  /**
   * Cria a tabela do objeto CoreExt_DataMapper para testes.
   */
  public function __construct()
  {
    parent::__construct();
    CoreExt_EntityCompoundDataMapperStub::createTable($this->getDbAdapter());
  }

  protected function setUp()
  {
    parent::setUp();
    CoreExt_DataMapper::resetDefaultDbAdapter();
  }

  public function getDataSet()
  {
    return $this->createXMLDataSet($this->getFixture('matricula.xml'));
  }

  public function testRecuperaRegistroUnico()
  {
    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->getDbAdapter());
    $found = $mapper->find(array(1, 1));

    $expected = new CoreExt_EntityCompoundStub(array(
      'pessoa' => 1,
      'curso'  => 1,
      'confirmado' => TRUE
    ));

    // Marca como se tivesse sido carregado, para garantir a comparação
    $expected->markOld();

    $this->assertEquals($expected, $found);
    $this->assertFalse($found->isNew());
  }

  public function testCadastraNovoRegistroComChaveComposta()
  {
    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->getDbAdapter());

    $entity = new CoreExt_EntityCompoundStub(array(
      'pessoa' => 1,
      'curso'  => 3,
      'confirmado' => TRUE
    ));

    $mapper->save($entity);

    $this->assertTablesEqual(
      $this->createXMLDataSet($this->getFixture('matricula-depois-salvo.xml'))
           ->getTable('matricula'),
      $this->getConnection()
           ->createDataSet()
           ->getTable('matricula')
    );
  }

  public function testAtualizaRegistroComChaveComposta()
  {
    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->getDbAdapter());

    $entity = new CoreExt_EntityCompoundStub(array(
      'pessoa' => 1,
      'curso' => 2,
      'confirmado' => TRUE
    ));

    // Marca como se tivesse sido carregado, para forçar CoreExt_DataMapper
    // a usar o INSERT.
    $entity->markOld();

    $mapper->save($entity);

    $this->assertTablesEqual(
      $this->createXMLDataSet($this->getFixture('matricula-depois-atualizado.xml'))
           ->getTable('matricula'),
      $this->getConnection()
           ->createDataSet()
           ->getTable('matricula')
    );
  }

  public function testApagaRegistroComChaveComposta()
  {
    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->getDbAdapter());

    $entity = new CoreExt_EntityCompoundStub(array(
      'pessoa' => 1,
      'curso' => 2
    ));

    $mapper->delete($entity);

    $this->assertTablesEqual(
      $this->createXMLDataSet($this->getFixture('matricula-depois-removido.xml'))
           ->getTable('matricula'),
      $this->getConnection()
           ->createDataSet()
           ->getTable('matricula')
    );

    // Apaga usando array com os valores da chave
    $mapper->delete(array('pessoa' => 1, 'curso' => 1));
    $this->assertEquals(0, $this->getConnection()->createDataSet()
                                ->getTable('matricula')->getRowCount());
  }
}