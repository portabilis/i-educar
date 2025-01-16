<?php

class CoreExt_EntityTest extends UnitBaseTest
{
    public function test_nova_instancia_e_marcada_como_new()
    {
        $entity = new CoreExt_EntityStub;
        $this->assertTrue($entity->isNew());
    }

    public function test_seta_atributo_do_componente()
    {
        $esperado = 'fooBar';
        $entity = new CoreExt_EntityStub;
        $entity->nome = $esperado;
        $this->assertEquals($esperado, $entity->nome);
    }

    public function test_print_da_instancia()
    {
        $this->outputBuffer(true);
        echo new CoreExt_EntityStub;
        $output = $this->outputBuffer(false);
        $this->assertEquals('CoreExt_EntityStub', $output);
    }

    public function test_setar_atributo_que_nao_existe_lanca_excecao_nao_verificada()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $entity = new CoreExt_EntityStub;
        $entity->foo = 'bar';
    }

    public function test_atributo_esta_setado()
    {
        $entity = new CoreExt_EntityStub;
        $this->assertFalse(isset($entity->nome));
        $entity->nome = 'fooBar';
        $this->assertTrue(isset($entity->nome));
    }

    public function test_atributo_foi_destruido()
    {
        $entity = new CoreExt_EntityStub;
        $entity->nome = 'fooBar';
        $this->assertTrue(isset($entity->nome));
        unset($entity->nome);
        $this->assertFalse(isset($entity->nome));
    }

    public function test_campo_identidade_e_considerado_nulo_para_nao_numericos()
    {
        $entity = new CoreExt_EntityStub;
        $entity->id = '';
        $this->assertNull($entity->id);
        $entity->id = '0';
        $this->assertNull($entity->id);
        $entity->id = 0;
        $this->assertNull($entity->id);
        $entity->id = null;
        $this->assertNull($entity->id);
    }

    public function test_auto_conversao_para_booleano()
    {
        $entity = new CoreExt_EntityStub;

        $entity->doador = 'f';
        $this->assertSame(false, $entity->doador);
        $entity->doador = 0;
        $this->assertSame(false, $entity->doador);

        $entity->doador = 't';
        $this->assertSame(true, $entity->doador);
        $entity->doador = 1;
        $this->assertSame(true, $entity->doador);
    }

    public function test_nova_instancia_e_nula()
    {
        $entity = new CoreExt_EntityStub;
        $this->assertTrue($entity->isNull());
    }

    public function test_instancia_com_algum_atributo_nao_nulo_tornase_nao_nula()
    {
        $entity = new CoreExt_EntityStub;
        $entity->nome = 'fooBar';
        $this->assertFalse($entity->isNull());
    }

    public function test_auto_conversao_tipos_numericos()
    {
        $entity = new CoreExt_ChildEntityStub;
        $entity->peso = '12,5';
        $this->assertIsFloat($entity->peso);
    }

    public function test_seta_atributos_na_instanciacao()
    {
        $data = [
            'id' => 1,
            'nome' => 'fooBar',
            'estadoCivil' => 'solteiro',
            'doador' => true,
        ];
        $entity = new CoreExt_EntityStub($data);
        $this->assertEquals($data, $entity->toArray());
    }

    public function test_seta_data_mapper()
    {
        $expected = new CoreExt_EntityDataMapperStub;

        $entity = new CoreExt_EntityStub;
        $entity->setDataMapper($expected);

        $this->assertSame($expected, $entity->getDataMapper());
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function test_configura_validador_para_atributo()
    {
        $entity = new CoreExt_EntityStub;
        $entity->setValidator('estadoCivil', new CoreExt_Validate_String);
        $this->assertInstanceOf('CoreExt_Validate_String', $entity->getValidator('estadoCivil'));
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function test_configurar_validador_para_atributo_inexistente_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $entity = new CoreExt_EntityStub;
        $entity->setValidator('fooAttr', new CoreExt_Validate_String);
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function test_atributos_da_instancia_sao_validos()
    {
        $data = [
            'nome' => 'fooBar',
        ];
        $entity = new CoreExt_EntityStub($data);
        $this->assertTrue($entity->isValid('nome'), 'Failed asserting isValid() for "nome" attribute.');
        $this->assertTrue($entity->isValid(), 'Failed asserting isValid() class\' attributes.');
        $this->assertFalse($entity->hasError('nome'));
        $this->assertFalse($entity->hasErrors());
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function test_validacao_geral_retorna_false_se_um_atributo_for_invalido()
    {
        $data = [
            'nome' => '',
        ];
        $entity = new CoreExt_EntityStub($data);
        $this->assertFalse($entity->isValid());
        $this->assertEquals('Obrigatório.', $entity->getError('nome'));
        $this->assertTrue($entity->hasErrors());
    }

    /**
     * Testa com instância de CoreExt_Entity que contenha referências DataMapper.
     *
     * @group Overload
     * @group CoreExt_Validate_Validatable
     */
    public function test_validacao_sanitiza_valor_de_atributo_com_referencias_data_mapper()
    {
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
    }

    /**
     * Testa com instância de CoreExt_Entity que contenha referências Enum.
     *
     * @group CoreExt_Validate_Validatable
     */
    public function test_validacao_sanitiza_valor_de_atributo_com_referencias_enum()
    {
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
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function test_cria_um_validador_dependendo_do_valor_de_um_atribudo_da_instancia()
    {
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
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function test_metodo_de_criacao_de_validador_sensivel_ao_caso_lanca_excecao_quando_classe_nao_e_subclasse_de_coreext_validate_abstract()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $entity = new CoreExt_EntityStub;
        $entity->validateIfEquals(
            'nome',
            '',
            'CoreExt_Validate_Abstract',
            [],
            []
        );
    }

    public function test_transforma_entity_em_array_de_valores()
    {
        $data = [
            'id' => 1,
            'nome' => 'fooBar',
            'estadoCivil' => 'solteiro',
            'doador' => true,
        ];
        $entity = new CoreExt_EntityStub($data);
        $array = $entity->filterAttr('id');
        $this->assertEquals([1], array_values($array));
    }

    public function test_transforma_entity_em_array_associativo()
    {
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
    }

    public function test_transforma_entities_em_array_associativo()
    {
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
    }

    public function test_setter_de_referencia_para_atributo_inexistente_lanca_excecao()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $expected = new CoreExt_ChildEntityStub;
        $expected->setReference('foo', []);
    }

    public function test_setter_de_referencia_lanca_excecao_caso_array_de_definicao_contenha_configuracao_inexistente()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $expected = new CoreExt_ChildEntityStub;
        $expected->setReference('nome', ['autoload' => true]);
    }

    public function test_setter_de_referencia_lanca_excecao_para_classe_coreext_data_mapper_invalida()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $expected = new CoreExt_ChildEntityStub;
        $expected->setReference('nome', ['class' => new stdClass]);
    }

    public function test_setter_de_referencia_overload_atribui_valor_para_a_referencia_quando_e_inteiro()
    {
        $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => 1]);
        $this->assertEquals(1, $entity->get('filho'));
    }

    public function test_setter_de_referencia_overload_atribui_valor_null_para_a_referencia_quando_ela_e_nullable()
    {
        $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => null]);
        $this->assertNull($entity->filho);
        $this->assertNull($entity->get('filho'));
    }

    /**
     * @group Overload
     */
    public function test_setter_de_referencia_overload_atribui_valor_para_a_referencia_e_no_atributo_quando_uma_instancia_de_core_ext_entity_com_field_identity_e_passada()
    {
        $child = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'FooBar Jr']);
        $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => $child]);
        $this->assertEquals($child, $entity->filho);
        $this->assertEquals(1, $entity->get('filho'));
    }

    public function test_entity_recuperada_pelo_data_mapper_e_marcada_como_velha()
    {
        $entity = new CoreExt_EntityStub(['nome' => 'fooBar']);
        $entity->markOld();

        $mapper = $this->getCleanMock('CoreExt_EntityDataMapperStub');
        $mapper->expects($this->once())
            ->method('find')
            ->with(1)
            ->will($this->returnValue($entity));

        $this->assertFalse($mapper->find(1)->isNew());
    }

    public function test_referencia_instancia_a_classe_atribuida_automaticamente()
    {
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
    }

    public function test_instancia_de_class_nao_especifica_e_armazenada_no_array_static_da_classe()
    {
        CoreExt_EntityStub::addClassToStorage(
            'StdClassExtStub',
            null,
            __DIR__ . '/_stub/StdClassExt.php'
        );
        $this->assertInstanceOf('StdClassExtStub', CoreExt_EntityStub::getClassFromStorage('StdClassExtStub'));
    }

    public function test_instancia_de_class_nao_especifica_armazena_instancia_especifica()
    {
        $obj = new stdClass;
        $oid = spl_object_hash($obj);
        CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
        $this->assertInstanceOf('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
        $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
    }

    public function test_instancia_de_class_nao_especifica_pode_ser_reposta_por_nova_instancia()
    {
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
    }

    public function test_instancia_de_class_e_sobresrita_por_instancias_sticky()
    {
        $oid1 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass, null, true);
        $oid1 = spl_object_hash($oid1);

        $oid2 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass, null, true);
        $oid2 = spl_object_hash($oid2);

        $this->assertNotEquals($oid2, $oid1);
    }

    public function test_instancia_de_class_nao_especifica_retorna_null_quando_nao_encontra_uma_instancia_da_classe()
    {
        $this->assertNull(CoreExt_EntityStub::getClassFromStorage('fooBar'));
    }

    public function test_instancia_de_class_nao_especifica_lanca_excecao_quando_instancia_passada_nao_tem_a_mesma_assinatura_do_nome_de_classe_informado()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        CoreExt_EntityStub::addClassToStorage('fooBar', new stdClass);
    }

    public function test_lazy_load_para_referencia_numerica_a_instancia_entity()
    {
        $data = [
            'nome' => 'fooBar',
            'filho' => 1,
        ];

        $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

        $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
        $filhoMapper->expects($this->once())
            ->method('find')
            ->with(1)
            ->will($this->returnValue($expected));

        $parent = new CoreExt_ParentEntityStub($data);
        $parent->setReference('filho', ['value' => 1, 'class' => $filhoMapper]);

        $this->assertInstanceOf('CoreExt_ChildEntityStub', $parent->filho);
        $this->assertEquals(1, $parent->filho->id);
    }

    /**
     * @group LazyLoad
     * @group CoreExt_Validate_Validatable
     */
    public function test_lazy_load_nao_carrega_instancia_entity_para_validacao()
    {
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
    }

    public function test_lazy_load_para_referencia_objeto_a_instancia_entity_nao_causa_lazy_load()
    {
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
    }

    public function test_lazy_load_nao_e_realizado_quando_referencia_igual_a0_ou_nulo()
    {
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
    }

    public function test_lazy_load_parametro_que_e_referencia_lanca_excecao_quando_nao_e_do_tipo_integer_ou_coreext_entity()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $expected = new stdClass;

        $data = [
            'id' => 1,
            'nome' => 'fooBar',
            'filho' => $expected,
        ];

        $parent = new CoreExt_ParentEntityStub($data);
    }

    public function test_lazy_load_de_referencia_a_um_tipo_coreext_enum()
    {
        $enum = CoreExt_EnumSexStub::getInstance();

        $child1 = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);
        $child2 = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

        $child1->setReference('sexo', ['value' => 1, 'class' => $enum]);
        $this->assertEquals('masculino', $child1->sexo);

        $child2->setReference('sexo', ['value' => 2, 'class' => $enum]);
        $this->assertEquals('feminino', $child2->sexo);
    }
}
