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
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id: /ieducar/branches/1.1.0-dev/ieducar/tests/unit/CoreExt/EntityTest.php 587 2009-10-15T22:47:32.301900Z eriksencosta  $
 */

require_once 'CoreExt/_stub/EntityDataMapper.php';
require_once 'CoreExt/_stub/EntityCompoundDataMapper.php';

/**
 * CoreExt_DataMapperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_DataMapperTest extends UnitBaseTest
{
  /**
   * Mock de clsBanco.
   * @var PHPUnit_Framework_MockObject_MockObject
   */
  protected $_db = NULL;

  protected function setUp()
  {
    $this->_db = $this->getDbMock();
  }

  /**
   * @expectedException Exception
   */
  public function testDbAdapterLancaExcecaoQuandoNaoEDoTipoEsperado()
  {
    $db = new stdClass();
    $mapper = new CoreExt_EntityDataMapperStub($db);
  }

  public function testRetornaInstanciaEntity()
  {
    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $instance = $mapper->createNewEntityInstance();
    $this->assertType('CoreExt_Entity', $instance);
  }

  public function testCarregaTodosOsRegistros()
  {
    $options1 = $options2 = array('estadoCivil' => NULL);
    $options1['nome'] = 'Cícero Pompeu de Toledo';
    $options2['nome'] = 'Cesar Filho';

    $expected = array(
      new CoreExt_EntityStub($options1),
      new CoreExt_EntityStub($options2)
    );

    // Marca como se tivesse sido carregado, para garantir a comparação
    $expected[0]->markOld();
    $expected[1]->markOld();

    // Na terceira chamada, irá retornar false para interromper o loop while
    $this->_db->expects($this->any())
         ->method('ProximoRegistro')
         ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->onConsecutiveCalls($options1, $options2));

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->findAll();

    $this->assertEquals($expected[0], $found[0]);
    $this->assertEquals($expected[1], $found[1]);
  }

  public function testCarregaTodosOsRegistrosSelecionandoColunas()
  {
    $options1 = $options2 = array();
    $options1['nome'] = 'Cícero Pompeu de Toledo';
    $options2['nome'] = 'Cesar Filho';

    $expected = array(
      new CoreExt_EntityStub($options1),
      new CoreExt_EntityStub($options2)
    );

    // Marca como se tivesse sido carregado, para garantir a comparação
    $expected[0]->markOld();
    $expected[1]->markOld();

    // Na terceira chamada, irá retornar false para interromper o loop while
    $this->_db->expects($this->any())
         ->method('ProximoRegistro')
         ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->onConsecutiveCalls($options1, $options2));

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->findAll(array('nome'));

    $this->assertEquals($expected[0], $found[0]);
    $this->assertEquals($expected[1], $found[1]);
  }

  public function testMapeiaAtributoAtravesDoMapaQuandoNaoExisteAtributoCorrespondente()
  {
    $common = array('nome' => 'Adolf Lutz');
    $options = $returnedOptions = $common;
    $options['estadoCivil'] = 'solteiro';
    $returnedOptions['estado_civil'] = 'solteiro';

    $expected = new CoreExt_EntityStub($options);
    $expected->markOld();

    $this->_db->expects($this->any())
         ->method('ProximoRegistro')
         ->will($this->onConsecutiveCalls(TRUE, FALSE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->onConsecutiveCalls($returnedOptions));

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->findAll();

    $this->assertEquals($expected, $found[0]);
  }

  public function testRecuperaRegistroUnico()
  {
    $expectedOptions = array(
      'id' => 1,
      'nome' => 'Henry Nobel',
      'estadoCivil' => 'solteiro'
    );

    $expected = new CoreExt_EntityStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->any())
         ->method('ProximoRegistro')
         ->will($this->returnValue(TRUE, FALSE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->returnValue($expectedOptions));

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->find(1);

    $this->assertEquals($expected, $found);
  }

  public function testRecuperaRegistroUnicoComChaveComposta()
  {
    $expectedOptions = array(
      'pessoa' => 1,
      'curso'  => 1,
      'confirmado' => TRUE
    );

    $expected = new CoreExt_EntityCompoundStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->once())
         ->method('ProximoRegistro')
         ->will($this->returnValue(TRUE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->returnValue($expectedOptions));

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $found = $mapper->find(array(1, 1));

    $this->assertEquals($expected, $found);
  }

  public function testRecuperaRegistroUnicoComChaveCompostaIdentificandoApenasUmaDasChaves()
  {
    $expectedOptions = array(
      'pessoa' => 1,
      'curso'  => 1,
      'confirmado' => TRUE
    );

    $expected = new CoreExt_EntityCompoundStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->once())
         ->method('ProximoRegistro')
         ->will($this->returnValue(TRUE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->returnValue($expectedOptions));

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $found = $mapper->find(array('pessoa' => 1));

    $this->assertEquals($expected, $found);
  }

  /**
   * @group CoreExt_Locale
   */
  public function testRecuperaRegistroRetornaFloat()
  {
    $expectedOptions = array(
      'id' => 1,
      'nome' => 'Antunes Jr.',
      'sexo' => 1,
      'tipoSanguineo' => 4,
      'peso' => 12.300
    );

    $expected = new CoreExt_ChildEntityStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->once())
         ->method('ProximoRegistro')
         ->will($this->returnValue(TRUE));

    $this->_db->expects($this->any())
         ->method('Tupla')
         ->will($this->returnValue($expectedOptions));

    $mapper = new CoreExt_ChildEntityDataMapperStub($this->_db);
    $found = $mapper->find(1);

    $this->assertEquals(12.300, $expected->peso);
  }

  /**
   * @expectedException Exception
   */
  public function testRegistroNaoExistenteLancaExcecao()
  {
    $this->_db->expects($this->once())
         ->method('ProximoRegistro')
         ->will($this->returnValue(FALSE));

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->find(1);

    $this->assertEquals($expected, $found);
  }

  public function testInsereNovoRegistro()
  {
    $this->_db->expects($this->once())
         ->method('Consulta')
         ->will($this->returnValue(TRUE));

    $entity = new CoreExt_EntityStub();
    $entity->nome = 'Fernando Nascimento';
    $entity->estadoCivil = 'casado';

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $this->assertTrue($mapper->save($entity));
  }

  public function testInsereNovoRegistroComChaveComposta()
  {
    $this->_db->expects($this->once())
         ->method('Consulta')
         ->will($this->returnValue(TRUE));

    $entity = new CoreExt_EntityCompoundStub();
    $entity->pessoa = 1;
    $entity->curso  = 1;
    $entity->confirmado = FALSE;

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $this->assertTrue($mapper->save($entity));
  }

  /**
   * @expectedException CoreExt_DataMapper_Exception
   */
  public function testInsereNovoRegistroComChaveCompostaComUmaNulaLancaExcecao()
  {
    $entity = new CoreExt_EntityCompoundStub();
    $entity->pessoa = 1;
    $entity->confirmado = FALSE;

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $this->assertTrue($mapper->save($entity));
  }

  public function testAtualizaRegistro()
  {
    $this->_db->expects($this->once())
         ->method('Consulta')
         ->will($this->returnValue(TRUE));

    $entity = new CoreExt_EntityStub();
    $entity->id = 1;
    $entity->nome = 'Fernando Nascimento';
    $entity->estadoCivil = 'casado';

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $this->assertTrue($mapper->save($entity));
  }

  public function testAtualizaRegistroComChaveComposta()
  {
    $this->_db->expects($this->once())
         ->method('Consulta')
         ->will($this->returnValue(TRUE));

    $entity = new CoreExt_EntityCompoundStub();
    $entity->pessoa = 1;
    $entity->curso  = 1;
    $entity->confirmado = TRUE;

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $this->assertTrue($mapper->save($entity));
  }

  public function testApagaRegistroPassandoInstanciaDeEntity()
  {
    $this->_db->expects($this->once())
         ->method('Consulta')
         ->will($this->returnValue(TRUE));

    $entity = new CoreExt_EntityStub();
    $entity->id = 1;

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $this->assertTrue($mapper->delete($entity));
  }

  public function testApagaRegistroPassandoValorInteiro()
  {
    $this->_db->expects($this->once())
         ->method('Consulta')
         ->will($this->returnValue(TRUE));

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $this->assertTrue($mapper->delete(1));
  }
}