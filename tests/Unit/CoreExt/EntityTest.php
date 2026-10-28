<?php

uses(UnitBaseTest::class);

test('nova instancia e marcada como new', function () {
    $entity = new CoreExt_EntityStub;
    $this->assertTrue($entity->isNew());
});

test('seta atributo do componente', function () {
    $esperado = 'fooBar';
    $entity = new CoreExt_EntityStub;
    $entity->nome = $esperado;
    $this->assertEquals($esperado, $entity->nome);
});

test('print da instancia', function () {
    $this->outputBuffer(true);
    echo new CoreExt_EntityStub;
    $output = $this->outputBuffer(false);
    $this->assertEquals('CoreExt_EntityStub', $output);
});

test('setar atributo que nao existe lanca excecao nao verificada', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $entity = new CoreExt_EntityStub;
    $entity->foo = 'bar';
});

test('atributo esta setado', function () {
    $entity = new CoreExt_EntityStub;
    $this->assertFalse(isset($entity->nome));
    $entity->nome = 'fooBar';
    $this->assertTrue(isset($entity->nome));
});

test('atributo foi destruido', function () {
    $entity = new CoreExt_EntityStub;
    $entity->nome = 'fooBar';
    $this->assertTrue(isset($entity->nome));
    unset($entity->nome);
    $this->assertFalse(isset($entity->nome));
});

test('campo identidade e considerado nulo para nao numericos', function () {
    $entity = new CoreExt_EntityStub;
    $entity->id = '';
    $this->assertNull($entity->id);
    $entity->id = '0';
    $this->assertNull($entity->id);
    $entity->id = 0;
    $this->assertNull($entity->id);
    $entity->id = null;
    $this->assertNull($entity->id);
});

test('auto conversao para booleano', function () {
    $entity = new CoreExt_EntityStub;

    $entity->doador = 'f';
    $this->assertSame(false, $entity->doador);
    $entity->doador = 0;
    $this->assertSame(false, $entity->doador);

    $entity->doador = 't';
    $this->assertSame(true, $entity->doador);
    $entity->doador = 1;
    $this->assertSame(true, $entity->doador);
});

test('nova instancia e nula', function () {
    $entity = new CoreExt_EntityStub;
    $this->assertTrue($entity->isNull());
});

test('instancia com algum atributo nao nulo tornase nao nula', function () {
    $entity = new CoreExt_EntityStub;
    $entity->nome = 'fooBar';
    $this->assertFalse($entity->isNull());
});

test('auto conversao tipos numericos', function () {
    $entity = new CoreExt_ChildEntityStub;
    $entity->peso = '12,5';
    $this->assertIsFloat($entity->peso);
});

test('seta atributos na instanciacao', function () {
    $data = [
        'id' => 1,
        'nome' => 'fooBar',
        'estadoCivil' => 'solteiro',
        'doador' => true,
    ];
    $entity = new CoreExt_EntityStub($data);
    $this->assertEquals($data, $entity->toArray());
});

test('seta data mapper', function () {
    $expected = new CoreExt_EntityDataMapperStub;

    $entity = new CoreExt_EntityStub;
    $entity->setDataMapper($expected);

    $this->assertSame($expected, $entity->getDataMapper());
});

test('configura validador para atributo', function () {
    $entity = new CoreExt_EntityStub;
    $entity->setValidator('estadoCivil', new CoreExt_Validate_String);
    $this->assertInstanceOf('CoreExt_Validate_String', $entity->getValidator('estadoCivil'));
})->group('CoreExt_Validate_Validatable');

test('configurar validador para atributo inexistente lanca excecao', function () {
    $this->expectException(Exception::class);
    $entity = new CoreExt_EntityStub;
    $entity->setValidator('fooAttr', new CoreExt_Validate_String);
})->group('CoreExt_Validate_Validatable');

test('atributos da instancia sao validos', function () {
    $data = [
        'nome' => 'fooBar',
    ];
    $entity = new CoreExt_EntityStub($data);
    $this->assertTrue($entity->isValid('nome'), 'Failed asserting isValid() for "nome" attribute.');
    $this->assertTrue($entity->isValid(), 'Failed asserting isValid() class\' attributes.');
    $this->assertFalse($entity->hasError('nome'));
    $this->assertFalse($entity->hasErrors());
})->group('CoreExt_Validate_Validatable');

test('validacao geral retorna false se um atributo for invalido', function () {
    $data = [
        'nome' => '',
    ];
    $entity = new CoreExt_EntityStub($data);
    $this->assertFalse($entity->isValid());
    $this->assertEquals('Obrigatório.', $entity->getError('nome'));
    $this->assertTrue($entity->hasErrors());
})->group('CoreExt_Validate_Validatable');

/**
 * Testa com instância de CoreExt_Entity que contenha referências DataMapper.
 */
test('validacao sanitiza valor de atributo com referencias data mapper', function () {
    $data = [
        'id' => 1,
        'nome' => ' FooBar Jr ',
        'sexo' => 1,
        'tipoSanguineo' => 1,
        'peso' => '12,5',
    ];
    $child = new CoreExt_ChildEntityStub($data);

    $data = [
        'nome' => ' FooBar ',
        'filho' => $child,
    ];
    $entity = new CoreExt_ParentEntityStub($data);

    // Atribui validadores para os atributos
    $entity->setValidator('nome', new CoreExt_Validate_String);
    $entity->setValidator('filho', new CoreExt_Validate_Choice(['choices' => [1, 2]]));

    // Valida e verifica pelos valores
    $entity->isValid();
    $this->assertEquals(1, $entity->get('filho'));
    $this->assertEquals('FooBar', $entity->nome);
})->group('Overload')->group('CoreExt_Validate_Validatable');

/**
 * Testa com instância de CoreExt_Entity que contenha referências Enum.
 */
test('validacao sanitiza valor de atributo com referencias enum', function () {
    $data = [
        'nome' => 'fooBar ',
        'sexo' => 1,
        'tipoSanguineo' => 1,
        'peso' => '12,5',
    ];
    $entity = new CoreExt_ChildEntityStub($data);

    // Atribui validadores para os atributos
    $entity->setValidator('nome', new CoreExt_Validate_String);
    $entity->setValidator('sexo', new CoreExt_Validate_Choice(['choices' => [1, 2]]));
    $entity->setValidator('tipoSanguineo', new CoreExt_Validate_Choice(['choices' => [1, 2]]));
    $entity->setValidator('peso', new CoreExt_Validate_Numeric);

    // Valida e verifica pelos valores
    $this->assertTrue($entity->isValid());
    $this->assertEquals('fooBar', $entity->nome);
    $this->assertIsFloat($entity->peso);
})->group('CoreExt_Validate_Validatable');

test('cria um validador dependendo do valor de um atribudo da instancia', function () {
    $entity = new CoreExt_EntityStub(['nome' => 'fooBar', 'estadoCivil' => '']);

    // Validador condicional
    $validator = $entity->validateIfEquals(
        'nome',
        'fooBar',
        'CoreExt_Validate_String',
        ['min' => 1, 'max' => 5],
        ['required' => false]
    );

    // Retornou o validador do If
    $entity->setValidator('estadoCivil', $validator);
    $this->assertFalse($entity->isValid('estadoCivil'), 'Falhou na asserção de validateIfEquals() para caso If.');

    // Validador condicional
    $entity->nome = 'barFoo';
    $validator = $entity->validateIfEquals(
        'nome',
        'fooBar',
        'CoreExt_Validate_String',
        ['min' => 1, 'max' => 5],
        ['required' => false]
    );

    // Retornou o validador do Else
    $entity->setValidator('estadoCivil', $validator);
    $this->assertTrue($entity->isValid('estadoCivil'), 'Falhou na asserção de validateIfEquals() para caso Else.');
})->group('CoreExt_Validate_Validatable');

test('metodo de criacao de validador sensivel ao caso lanca excecao quando classe nao e subclasse de coreext validate abstract', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $entity = new CoreExt_EntityStub;
    $entity->validateIfEquals(
        'nome',
        '',
        'CoreExt_Validate_Abstract',
        [],
        []
    );
})->group('CoreExt_Validate_Validatable');

test('transforma entity em array de valores', function () {
    $data = [
        'id' => 1,
        'nome' => 'fooBar',
        'estadoCivil' => 'solteiro',
        'doador' => true,
    ];
    $entity = new CoreExt_EntityStub($data);
    $array = $entity->filterAttr('id');
    $this->assertEquals([1], array_values($array));
});

test('transforma entity em array associativo', function () {
    $data = [
        'id' => 1,
        'nome' => 'fooBar',
        'estadoCivil' => 'solteiro',
        'doador' => true,
    ];
    $entity = new CoreExt_EntityStub($data);

    // Interface de instância
    $array = $entity->filterAttr('id', 'nome');
    $this->assertEquals([1 => 'fooBar'], $array);

    // Interface estática
    $array = CoreExt_Entity::entityFilterAttr($entity, 'id', 'nome');
    $this->assertEquals([1 => 'fooBar'], $array);
});

test('transforma entities em array associativo', function () {
    $data1 = $data2 = [
        'id' => 1,
        'nome' => 'fooBar',
        'estadoCivil' => 'solteiro',
        'doador' => true,
    ];

    $data2['id'] = 2;
    $data2['nome'] = 'barFoo';

    $entity1 = new CoreExt_EntityStub($data1);
    $entity2 = new CoreExt_EntityStub($data2);

    $entities = [$entity1, $entity2];

    $array = CoreExt_EntityStub::entityFilterAttr($entities, 'id', 'nome');

    $this->assertEquals([1 => 'fooBar', 2 => 'barFoo'], $array);
});

test('setter de referencia para atributo inexistente lanca excecao', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $expected = new CoreExt_ChildEntityStub;
    $expected->setReference('foo', []);
});

test('setter de referencia lanca excecao caso array de definicao contenha configuracao inexistente', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $expected = new CoreExt_ChildEntityStub;
    $expected->setReference('nome', ['autoload' => true]);
});

test('setter de referencia lanca excecao para classe coreext data mapper invalida', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $expected = new CoreExt_ChildEntityStub;
    $expected->setReference('nome', ['class' => new stdClass]);
});

test('setter de referencia overload atribui valor para a referencia quando e inteiro', function () {
    $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => 1]);
    $this->assertEquals(1, $entity->get('filho'));
});

test('setter de referencia overload atribui valor null para a referencia quando ela e nullable', function () {
    $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => null]);
    $this->assertNull($entity->filho);
    $this->assertNull($entity->get('filho'));
});

test('setter de referencia overload atribui valor para a referencia e no atributo quando uma instancia de core ext entity com field identity e passada', function () {
    $child = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'FooBar Jr']);
    $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => $child]);
    $this->assertEquals($child, $entity->filho);
    $this->assertEquals(1, $entity->get('filho'));
})->group('Overload');

test('entity recuperada pelo data mapper e marcada como velha', function () {
    $entity = new CoreExt_EntityStub(['nome' => 'fooBar']);
    $entity->markOld();

    $mapper = $this->getCleanMock('CoreExt_EntityDataMapperStub');
    $mapper->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($entity);

    $this->assertFalse($mapper->find(1)->isNew());
});

test('referencia instancia a classe atribuida automaticamente', function () {
    $parent = new CoreExt_ParentEntityStub(['filho' => 1]);
    $child = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'fooBar', 'sexo' => 1]);

    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($child);

    $parent->setReferenceClass('filho', $filhoMapper);

    $enum = CoreExt_EnumSexStub::getInstance();
    $expected = $enum[CoreExt_EnumSexStub::MALE];

    $this->assertEquals($child, $parent->filho);
    $this->assertEquals($expected, $child->sexo);
});

test('instancia de class nao especifica e armazenada no array static da classe', function () {
    CoreExt_EntityStub::addClassToStorage(
        'StdClassExtStub',
        null,
        __DIR__ . '/_stub/StdClassExt.php'
    );
    $this->assertInstanceOf('StdClassExtStub', CoreExt_EntityStub::getClassFromStorage('StdClassExtStub'));
});

test('instancia de class nao especifica armazena instancia especifica', function () {
    $obj = new stdClass;
    $oid = spl_object_hash($obj);
    CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
    $this->assertInstanceOf('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
    $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
});

test('instancia de class nao especifica pode ser reposta por nova instancia', function () {
    // Só para facilitar.
    for ($i = 0; $i <= 1; $i++) {
        $obj = new stdClass;
        $obj->i = $i;
        $oid = spl_object_hash($obj);
        CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
        $this->assertInstanceOf('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
        $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
    }
    CoreExt_EntityStub::addClassToStorage('stdClass');
    $this->assertInstanceOf('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
    $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
});

test('instancia de class e sobresrita por instancias sticky', function () {
    $oid1 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass, null, true);
    $oid1 = spl_object_hash($oid1);

    $oid2 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass, null, true);
    $oid2 = spl_object_hash($oid2);

    $this->assertNotEquals($oid2, $oid1);
});

test('instancia de class nao especifica retorna null quando nao encontra uma instancia da classe', function () {
    $this->assertNull(CoreExt_EntityStub::getClassFromStorage('fooBar'));
});

test('instancia de class nao especifica lanca excecao quando instancia passada nao tem a mesma assinatura do nome de classe informado', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    CoreExt_EntityStub::addClassToStorage('fooBar', new stdClass);
});

test('lazy load para referencia numerica a instancia entity', function () {
    $data = [
        'nome' => 'fooBar',
        'filho' => 1,
    ];

    $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($expected);

    $parent = new CoreExt_ParentEntityStub($data);
    $parent->setReference('filho', ['value' => 1, 'class' => $filhoMapper]);

    $this->assertInstanceOf('CoreExt_ChildEntityStub', $parent->filho);
    $this->assertEquals(1, $parent->filho->id);
});

test('lazy load nao carrega instancia entity para validacao', function () {
    $data = [
        'nome' => 'fooBar',
    ];

    $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->never())
        ->method('find');

    $parent = new CoreExt_ParentEntityStub($data);
    $parent->setReference('filho', ['value' => 1, 'class' => $filhoMapper]);

    $this->assertTrue($parent->isValid());
})->group('LazyLoad')->group('CoreExt_Validate_Validatable');

test('lazy load para referencia objeto a instancia entity nao causa lazy load', function () {
    $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

    $data = [
        'id' => 1,
        'nome' => 'fooBar',
        'filho' => $expected,
    ];

    // O método "find" não deve ser chamado pois "child" já existe como atributo
    // de "parent"
    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->never())
        ->method('find');

    // Configurando com mock, para ter certeza que não irá tentar carregar
    // "child"
    $parent = new CoreExt_ParentEntityStub($data);
    $parent->setReference('filho', ['value' => 1, 'class' => $filhoMapper]);

    $this->assertInstanceOf('CoreExt_ChildEntityStub', $parent->filho);
    $this->assertEquals(1, $parent->filho->id);
    $this->assertEquals('barFoo', $parent->filho->nome);

    // toArray() causa chamadas lazy load. Garantindo que não ocorra.
    $this->assertEquals($data, $parent->toArray());

    // toDataArray() retorna sempre a referência numérica.
    $data['filho'] = 1;
    $this->assertEquals($data, $parent->toDataArray());
});

test('lazy load nao e realizado quando referencia igual a0 ou nulo', function () {
    $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

    $data = [
        'id' => 1,
        'nome' => 'fooBar',
    ];

    // O método "find" não deve ser chamado pois "child" já existe como atributo
    // de "parent"
    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->never())
        ->method('find');

    // Configurando com mock, para ter certeza que não irá tentar carregar
    // "child"
    $parent = new CoreExt_ParentEntityStub($data);

    $this->assertNull($parent->filho);
});

test('lazy load parametro que e referencia lanca excecao quando nao e do tipo integer ou coreext entity', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $expected = new stdClass;

    $data = [
        'id' => 1,
        'nome' => 'fooBar',
        'filho' => $expected,
    ];

    $parent = new CoreExt_ParentEntityStub($data);
});

test('lazy load de referencia a um tipo coreext enum', function () {
    $enum = CoreExt_EnumSexStub::getInstance();

    $child1 = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);
    $child2 = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

    $child1->setReference('sexo', ['value' => 1, 'class' => $enum]);
    $this->assertEquals('masculino', $child1->sexo);

    $child2->setReference('sexo', ['value' => 2, 'class' => $enum]);
    $this->assertEquals('feminino', $child2->sexo);
});
