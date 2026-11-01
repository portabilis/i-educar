<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->_db = $this->getDbMock();
});

test('db adapter lanca excecao quando nao e do tipo esperado', function () {
    $this->expectException(TypeError::class);
    $db = new stdClass;
    $mapper = new CoreExt_EntityDataMapperStub($db);
});

test('retorna instancia entity', function () {
    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $instance = $mapper->createNewEntityInstance();

    $this->assertInstanceOf('CoreExt_Entity', $instance);
});

test('carrega todos os registros', function () {
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
        ->willReturnOnConsecutiveCalls(true, true, false);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturnOnConsecutiveCalls($options1, $options2);

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->findAll();

    $this->assertEquals($expected[0], $found[0]);
    $this->assertEquals($expected[1], $found[1]);
});

test('carrega todos os registros selecionando colunas', function () {
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
        ->willReturnOnConsecutiveCalls(true, true, false);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturnOnConsecutiveCalls($options1, $options2);

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->findAll(['nome']);

    $this->assertEquals($expected[0], $found[0]);
    $this->assertEquals($expected[1], $found[1]);
});

test('mapeia atributo atraves do mapa quando nao existe atributo correspondente', function () {
    $common = ['nome' => 'Adolf Lutz'];
    $options = $returnedOptions = $common;
    $options['estadoCivil'] = 'solteiro';
    $returnedOptions['estado_civil'] = 'solteiro';

    $expected = new CoreExt_EntityStub($options);
    $expected->markOld();

    $this->_db->expects($this->any())
        ->method('ProximoRegistro')
        ->willReturnOnConsecutiveCalls(true, false);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturnOnConsecutiveCalls($returnedOptions);

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->findAll();

    $this->assertEquals($expected, $found[0]);
});

test('recupera registro unico', function () {
    $expectedOptions = [
        'id' => 1,
        'nome' => 'Henry Nobel',
        'estadoCivil' => 'solteiro',
    ];

    $expected = new CoreExt_EntityStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->any())
        ->method('ProximoRegistro')
        ->willReturn(true, false);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn($expectedOptions);

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->find(1);

    $this->assertEquals($expected, $found);
});

test('recupera registro unico com chave composta', function () {
    $expectedOptions = [
        'pessoa' => 1,
        'curso' => 1,
        'confirmado' => true,
    ];

    $expected = new CoreExt_EntityCompoundStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->once())
        ->method('ProximoRegistro')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn($expectedOptions);

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $found = $mapper->find([1, 1]);

    $this->assertEquals($expected, $found);
});

test('recupera registro unico com chave composta identificando apenas uma das chaves', function () {
    $expectedOptions = [
        'pessoa' => 1,
        'curso' => 1,
        'confirmado' => true,
    ];

    $expected = new CoreExt_EntityCompoundStub($expectedOptions);
    $expected->markOld();

    $this->_db->expects($this->once())
        ->method('ProximoRegistro')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn($expectedOptions);

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);
    $found = $mapper->find(['pessoa' => 1]);

    $this->assertEquals($expected, $found);
});

test('recupera registro retorna float', function () {
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
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn($expectedOptions);

    $mapper = new CoreExt_ChildEntityDataMapperStub($this->_db);
    $found = $mapper->find(1);

    $this->assertEquals(12.300, $expected->peso);
});

test('registro nao existente lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_db->expects($this->once())
        ->method('ProximoRegistro')
        ->willReturn(false);

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);
    $found = $mapper->find(1);

    $this->assertEquals($expected, $found);
});

test('insere novo registro', function () {
    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn([]);

    $entity = new CoreExt_EntityStub;
    $entity->nome = 'Fernando Nascimento';
    $entity->estadoCivil = 'casado';
    $entity->markOld();

    $_SESSION['id_pessoa'] = 1;

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);

    $this->assertTrue($mapper->save($entity));
});

test('insere novo registro com chave composta', function () {
    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn([]);

    $entity = new CoreExt_EntityCompoundStub;
    $entity->pessoa = 1;
    $entity->curso = 1;
    $entity->confirmado = false;
    $entity->markOld();

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

    $this->assertTrue($mapper->save($entity));
});

test('insere novo registro com chave composta com uma nula lanca excecao', function () {
    $entity = new CoreExt_EntityCompoundStub;
    $entity->pessoa = 1;
    $entity->confirmado = false;
    $entity->markOld();

    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn([]);

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

    $this->assertTrue($mapper->save($entity));
});

test('atualiza registro', function () {
    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn([]);

    $entity = new CoreExt_EntityStub;
    $entity->id = 1;
    $entity->nome = 'Fernando Nascimento';
    $entity->estadoCivil = 'casado';
    $entity->markOld();

    $mapper = new CoreExt_EntityDataMapperStub($this->_db);

    $this->assertTrue($mapper->save($entity));
});

test('atualiza registro com chave composta', function () {
    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturn(true);

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn([]);

    $entity = new CoreExt_EntityCompoundStub;
    $entity->pessoa = 1;
    $entity->curso = 1;
    $entity->confirmado = true;
    $entity->markOld();

    $mapper = new CoreExt_EntityCompoundDataMapperStub($this->_db);

    $this->assertTrue($mapper->save($entity));
});

test('apaga registro passando instancia de entity', function () {
    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturnOnConsecutiveCalls((true));

    $this->_db->expects($this->any())
        ->method('Tupla')
        ->willReturn([]);

    $entity = new CoreExt_EntityStub;
    $mapper = new CoreExt_EntityDataMapperStub($this->_db);

    $this->assertTrue($mapper->delete($entity));
});

test('apaga registro passando valor inteiro', function () {
    $this->_db->expects($this->any())
        ->method('Consulta')
        ->willReturn(true);

    $entity = new CoreExt_EntityStub;
    $mapper = new CoreExt_EntityDataMapperStub($this->_db);

    $this->assertTrue($mapper->delete($entity));
});
