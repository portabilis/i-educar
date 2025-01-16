<?php

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

    public function test_db_adapter_lanca_excecao_quando_nao_e_do_tipo_esperado()
    {
        $this->expectException(\TypeError::class);
        $db = new stdClass;
        $mapper = new CoreExt_EntityDataMapperStub($db);
    }

    public function test_retorna_instancia_entity()
    {
        $mapper = new CoreExt_EntityDataMapperStub($this->_db);
        $instance = $mapper->createNewEntityInstance();

        $this->assertInstanceOf('CoreExt_Entity', $instance);
    }

    public function test_carrega_todos_os_registros()
    {
        $options1 = $options2 = ['estadoCivil' => null];
        $options1['nome'] = 'Cícero Pompeu de Toledo';
        $options2['nome'] = 'Cesar Filho';

        $expected = [
            new CoreExt_EntityStub($options1),
            new CoreExt_EntityStub($options2),
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

    public function test_carrega_todos_os_registros_selecionando_colunas()
    {
        $options1 = $options2 = [];
        $options1['nome'] = 'Cícero Pompeu de Toledo';
        $options2['nome'] = 'Cesar Filho';

        $expected = [
            new CoreExt_EntityStub($options1),
            new CoreExt_EntityStub($options2),
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

    public function test_mapeia_atributo_atraves_do_mapa_quando_nao_existe_atributo_correspondente()
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

    public function test_recupera_registro_unico()
    {
        $expectedOptions = [
            'id' => 1,
            'nome' => 'Henry Nobel',
            'estadoCivil' => 'solteiro',
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

    public function test_recupera_registro_unico_com_chave_composta()
    {
        $expectedOptions = [
            'pessoa' => 1,
            'curso' => 1,
            'confirmado' => true,
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

    public function test_recupera_registro_unico_com_chave_composta_identificando_apenas_uma_das_chaves()
    {
        $expectedOptions = [
            'pessoa' => 1,
            'curso' => 1,
            'confirmado' => true,
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

    public function test_recupera_registro_retorna_float()
    {
        $expectedOptions = [
            'id' => 1,
            'nome' => 'Antunes Jr.',
            'sexo' => 1,
            'tipoSanguineo' => 4,
            'peso' => 12.300,
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

    public function test_registro_nao_existente_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_db->expects($this->once())
            ->method('ProximoRegistro')
            ->will($this->returnValue(false));

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);
        $found = $mapper->find(1);

        $this->assertEquals($expected, $found);
    }

    public function test_insere_novo_registro()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityStub;
        $entity->nome = 'Fernando Nascimento';
        $entity->estadoCivil = 'casado';
        $entity->markOld();

        $_SESSION['id_pessoa'] = 1;

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function test_insere_novo_registro_com_chave_composta()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityCompoundStub;
        $entity->pessoa = 1;
        $entity->curso = 1;
        $entity->confirmado = false;
        $entity->markOld();

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function test_insere_novo_registro_com_chave_composta_com_uma_nula_lanca_excecao()
    {
        $entity = new CoreExt_EntityCompoundStub;
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

    public function test_atualiza_registro()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityStub;
        $entity->id = 1;
        $entity->nome = 'Fernando Nascimento';
        $entity->estadoCivil = 'casado';
        $entity->markOld();

        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function test_atualiza_registro_com_chave_composta()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityCompoundStub;
        $entity->pessoa = 1;
        $entity->curso = 1;
        $entity->confirmado = true;
        $entity->markOld();

        $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

        $this->assertTrue($mapper->save($entity));
    }

    public function test_apaga_registro_passando_instancia_de_entity()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->onConsecutiveCalls(true));

        $this->_db->expects($this->any())
            ->method('Tupla')
            ->will($this->returnValue([]));

        $entity = new CoreExt_EntityStub;
        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->delete($entity));
    }

    public function test_apaga_registro_passando_valor_inteiro()
    {
        $this->_db->expects($this->any())
            ->method('Consulta')
            ->will($this->returnValue(true));

        $entity = new CoreExt_EntityStub;
        $mapper = new CoreExt_EntityDataMapperStub($this->_db);

        $this->assertTrue($mapper->delete($entity));
    }
}
