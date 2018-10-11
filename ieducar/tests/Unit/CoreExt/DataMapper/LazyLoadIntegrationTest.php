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

require_once __DIR__.'/../_stub/ParentEntityDataMapper.php';
require_once __DIR__.'/../_stub/ChildEntityDataMapper.php';

/**
 * CoreExt_DataMapper_LazyLoadIntegrationTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  IntegrationTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_DataMapper_LazyLoadIntegrationTest extends IntegrationBaseTest
{
  /**
   * Cria as tabelas parent e child.
   */
  public function __construct()
  {
    parent::__construct();
    CoreExt_DataMapper::setDefaultDbAdapter($this->getDbAdapter());
    CoreExt_ParentEntityDataMapperStub::createTable($this->getDbAdapter());
    CoreExt_ChildEntityDataMapperStub::createTable($this->getDbAdapter());
  }

  public function setUp()
  {
    parent::setUp();
    CoreExt_DataMapper::setDefaultDbAdapter($this->getDbAdapter());
  }

  public function getDataSet()
  {
    return $this->createXMLDataSet($this->getFixture('parent-child.xml'));
  }

  public function testRecuperaTodosOsRegistros()
  {
    $mapper = new CoreExt_ParentEntityDataMapperStub();
    $found = $mapper->findAll();

    $this->assertTablesEqual(
      $this->getDataSet()
           ->getTable('parent'),
      $this->getConnection()
           ->createDataSet()
           ->getTable('parent')
    );

    $this->assertTablesEqual(
      $this->getDataSet()
           ->getTable('child'),
      $this->getConnection()
           ->createDataSet()
           ->getTable('child')
    );
  }

  public function testLazyLoadUsandoDefinicaoDeDataMapper()
  {
    $definition = array(
      'class' => 'CoreExt_ChildEntityDataMapperStub',
      'file'  => 'CoreExt/_stub/ChildEntityDataMapper.php'
    );

    $parentMapper = new CoreExt_ParentEntityDataMapperStub();
    $parent = $parentMapper->find(1);
    $parent->setReference('filho', $definition);

    $this->assertEquals(1, $parent->filho->id);
    $this->assertEquals('Antunes Jr.', $parent->filho->nome);
  }

  /**
   * Uma referência NULL para CoreExt_Enum retorna NULL logo no início da
   * lógica de CoreExt_Entity::_loadReference().
   * @group CoreExt_Entity
   */
  public function testLazyLoadUsandoDefinicaoDeEnumComReferenciaNula()
  {
    $child = new CoreExt_ChildEntityStub(
      array('id' => 3, 'sexo' => 1, 'tipoSanguineo' => NULL)
    );
    $this->assertNull($child->tipoSanguineo);
  }

  /**
   * Referência 0 é perfeitamente válido para um CoreExt_Enum. Se não existir o
   * offset no Enum, retorna NULL
   * @group CoreExt_Entity
   */
  public function testLazyLoadUsandoDefinicaoDeEnumComReferenciaZero()
  {
    $child = new CoreExt_ChildEntityStub(
      array('id' => 3, 'sexo' => 1, 'tipoSanguineo' => 0)
    );
    $this->assertNull($child->tipoSanguineo);
  }

  /**
   * Uma referência NULL é válida para as referências que explicitam a chave
   * null = TRUE.
   * @group CoreExt_Entity
   */
  public function testLazyLoadUsandoDefinicaoDeDataMapperComReferenciaNula()
  {
    $parent = new CoreExt_ParentEntityStub(
      array('id' => 3, 'nome' => 'Paul M.', 'filho' => NULL)
    );
    $this->assertNull($parent->filho);
  }

  /**
   * Referência 0 em DataMapper força o retorno de NULL. Isso é feito em
   * razão do HTML não suportar um valor NULL e por conta dos validadores
   * client-side legados do i-Educar não considerarem "" (string vazia) um
   * valor válido para submit.
   * @group CoreExt_Entity
   */
  public function testLazyLoadUsandoDefinicaoDeDataMapperComReferenciaZero()
  {
    $parent = new CoreExt_ParentEntityStub(
      array('id' => 3, 'nome' => 'Paul M.', 'filho' => 0)
    );
    $this->assertNull($parent->filho);
  }

  public function testInsereRegistros()
  {
    $child = new CoreExt_ChildEntityStub(array('nome' => 'Nascimento Jr.'));
    $childMapper = new CoreExt_ChildEntityDataMapperStub();
    $childMapper->save($child);

    $parent = new CoreExt_ParentEntityStub(array('nome' => 'Fernando Nascimento', 'filho' => 3));
    $parentMapper = new CoreExt_ParentEntityDataMapperStub();
    $parentMapper->save($parent);

    $parent = $parentMapper->find(3);
    $child  = $childMapper->find(3);

    $this->assertEquals($child, $parent->filho);
  }

  /**
   * Testa se um CoreExt_Entity retornado por um CoreExt_DataMapper configura
   * a reference e o atributo, com um valor referência e a instância,
   * respectivamente.
   * @group Overload
   */
  public function testRegistroRecuperadoConfiguraReferenceParaLazyLoadPosterior()
  {
    $parentMapper = new CoreExt_ParentEntityDataMapperStub();
    $parent = $parentMapper->find(1);
    $this->assertEquals(1, $parent->get('filho'));
    $this->assertInstanceOf('CoreExt_ChildEntityStub', $parent->filho);
  }
}