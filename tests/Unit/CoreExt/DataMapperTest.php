<?php

require_once __DIR__ . '/_stub/EntityDataMapper.php';
require_once __DIR__ . '/_stub/EntityCompoundDataMapper.php';

class CoreExt_DataMapperTest extends UnitBaseTest
{
    /**
     * Mock de clsBanco.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_db = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_db = $this->getDbMock();
    }

    /**
     * @expectedException TypeError
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

        $this->assertInstanceOf('CoreExt_Entity', $instance);
    }

    public function testCarregaTodosOsRegistros()
    {
        $options1 = $options2 = ['estadoCivil' => null];
        $options1['nome'] = 'Cícero Pompeu de Toledo';
        $options2['nome'] = 'Cesar Filho';

        $expected = [
            new CoreExt_EntityStub($options1),
            new CoreExt_EntityStub($options2)
        ];

        // Marca como se tivesse sido carregado, para garantir a comparação
        $expected[0]->markOld();
        $expected[1]->markOld();

        // Na terceira chamada, irá retornar false para interromper o loop while
        $this->_db->expects($this->any())
            ->method('ProximoRegistro')
            ->will($this->onConsecutiveCalls(true, true, false));

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
        $options1 = $options2 = [];
        $options1['nome'] = 'Cícero Pompeu de Toledo';
        $options2['nome'] = 'Cesar Filho';

        $expected = [
            new CoreExt_EntityStub($options1),
            new CoreExt_EntityStub($options2)
        ];

        // Marca como se tivesse sido carregado, para garantir a comparação
        $expected[0]->markOld();
        $expected[1]->markOld();

        // Na terceira chamada, irá retornar false para interromper o loop while
        $this->_db->expects($this->any())
            ->method('ProximoRegistro')
            ->will($this->onConsecutiveCalls(true, true, false));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->onConsecutiveCalls($options1, $options2));

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);
        $found = $mapper->findAll(['nome']);

        $this->assertEquals($expected[0], $found[0]);
        $this->assertEquals($expected[1], $found[1]);
    }

    public function testMapeiaAtributoAtravesDoMapaQuandoNaoExisteAtributoCorrespondente()
    {
        $common = ['nome' => 'Adolf Lutz'];
        $options = $returnedOptions = $common;
        $options['estadoCivil'] = 'solteiro';
        $returnedOptions['estado_civil'] = 'solteiro';

        $expected = new CoreExt_EntityStub($options);
        $expected->markOld();

        $this->_db->expects($this->any())
            ->method('ProximoRegistro')
            ->will($this->onConsecutiveCalls(true, false));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->onConsecutiveCalls($returnedOptions));

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);
        $found = $mapper->findAll();

        $this->assertEquals($expected, $found[0]);
    }

    public function testRecuperaRegistroUnico()
    {
        $expectedOptions = [
            'id' => 1,
            'nome' => 'Henry Nobel',
            'estadoCivil' => 'solteiro'
        ];

        $expected = new CoreExt_EntityStub($expectedOptions);
        $expected->markOld();

        $this->_db->expects($this->any())
            ->method('ProximoRegistro')
            ->will($this->returnValue(true, false));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue($expectedOptions));

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);
        $found = $mapper->find(1);

        $this->assertEquals($expected, $found);
    }

    public function testRecuperaRegistroUnicoComChaveComposta()
    {
        $expectedOptions = [
            'pessoa' => 1,
            'curso' => 1,
            'confirmado' => true
        ];

        $expected = new CoreExt_EntityCompoundStub($expectedOptions);
        $expected->markOld();

        $this->_db->expects($this->once())
            ->method('ProximoRegistro')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue($expectedOptions));

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
        $found = $mapper->find([1, 1]);

        $this->assertEquals($expected, $found);
    }

    public function testRecuperaRegistroUnicoComChaveCompostaIdentificandoApenasUmaDasChaves()
    {
        $expectedOptions = [
            'pessoa' => 1,
            'curso' => 1,
            'confirmado' => true
        ];

        $expected = new CoreExt_EntityCompoundStub($expectedOptions);
        $expected->markOld();

        $this->_db->expects($this->once())
            ->method('ProximoRegistro')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue($expectedOptions));

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
        $found = $mapper->find(['pessoa' => 1]);

        $this->assertEquals($expected, $found);
    }

    public function testRecuperaRegistroRetornaFloat()
    {
        $expectedOptions = [
            'id' => 1,
            'nome' => 'Antunes Jr.',
            'sexo' => 1,
            'tipoSanguineo' => 4,
            'peso' => 12.300
        ];

        $expected = new CoreExt_ChildEntityStub($expectedOptions);
        $expected->markOld();

        $this->_db->expects($this->once())
            ->method('ProximoRegistro')
            ->will($this->returnValue(true));

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
            ->will($this->returnValue(false));

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);
        $found = $mapper->find(1);

        $this->assertEquals($expected, $found);
    }

    public function testInsereNovoRegistro()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityStub();
        $entity->nome = 'Fernando Nascimento';
        $entity->estadoCivil = 'casado';
        $entity->markOld();

        $_SESSION['id_pessoa'] = 1;

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function testInsereNovoRegistroComChaveComposta()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityCompoundStub();
        $entity->pessoa = 1;
        $entity->curso = 1;
        $entity->confirmado = false;
        $entity->markOld();

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function testInsereNovoRegistroComChaveCompostaComUmaNulaLancaExcecao()
    {
        $entity = new CoreExt_EntityCompoundStub();
        $entity->pessoa = 1;
        $entity->confirmado = false;
        $entity->markOld();

        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function testAtualizaRegistro()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityStub();
        $entity->id = 1;
        $entity->nome = 'Fernando Nascimento';
        $entity->estadoCivil = 'casado';
        $entity->markOld();

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function testAtualizaRegistroComChaveComposta()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityCompoundStub();
        $entity->pessoa = 1;
        $entity->curso = 1;
        $entity->confirmado = true;
        $entity->markOld();

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function testApagaRegistroPassandoInstanciaDeEntity()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->onConsecutiveCalls(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityStub();
        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->delete($entity));
    }

    public function testApagaRegistroPassandoValorInteiro()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $entity = new CoreExt_EntityStub();
        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->delete($entity));
    }
}
