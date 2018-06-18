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
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Entity
 * @subpackage  UnitTests
 *
 * @since       Arquivo disponível desde a versão 1.1.0
 *
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'CoreExt/_stub/Entity.php';
require_once 'CoreExt/_stub/EntityDataMapper.php';
require_once 'CoreExt/_stub/ParentEntity.php';
require_once 'CoreExt/_stub/ChildEntity.php';
require_once 'CoreExt/_stub/ChildEntityDataMapper.php';
require_once 'CoreExt/_stub/EnumSex.php';

/**
 * CoreExt_EntityTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Entity
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_EntityTest extends UnitBaseTest
{
    public function testNovaInstanciaEMarcadaComoNew()
    {
        $entity = new CoreExt_EntityStub();
        $this->assertTrue($entity->isNew());
    }

    public function testSetaAtributoDoComponente()
    {
        $esperado = 'fooBar';
        $entity = new CoreExt_EntityStub();
        $entity->nome = $esperado;
        $this->assertEquals($esperado, $entity->nome);
    }

    public function testPrintDaInstancia()
    {
        $this->outputBuffer(true);
        print new CoreExt_EntityStub();
        $output = $this->outputBuffer(false);
        $this->assertEquals('CoreExt_EntityStub', $output);
    }

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testSetarAtributoQueNaoExisteLancaExcecaoNaoVerificada()
    {
        $entity = new CoreExt_EntityStub();
        $entity->foo = 'bar';
    }

    public function testAtributoEstaSetado()
    {
        $entity = new CoreExt_EntityStub();
        $this->assertFalse(isset($entity->nome));
        $entity->nome = 'fooBar';
        $this->assertTrue(isset($entity->nome));
    }

    public function testAtributoFoiDestruido()
    {
        $entity = new CoreExt_EntityStub();
        $entity->nome = 'fooBar';
        $this->assertTrue(isset($entity->nome));
        unset($entity->nome);
        $this->assertFalse(isset($entity->nome));
    }

    public function testCampoIdentidadeEConsideradoNuloParaNaoNumericos()
    {
        $entity = new CoreExt_EntityStub();
        $entity->id = '';
        $this->assertNull($entity->id);
        $entity->id = '0';
        $this->assertNull($entity->id);
        $entity->id = 0;
        $this->assertNull($entity->id);
        $entity->id = null;
        $this->assertNull($entity->id);
    }

    public function testAutoConversaoParaBooleano()
    {
        $entity = new CoreExt_EntityStub();

        $entity->doador = 'f';
        $this->assertSame(false, $entity->doador);
        $entity->doador = 0;
        $this->assertSame(false, $entity->doador);

        $entity->doador = 't';
        $this->assertSame(true, $entity->doador);
        $entity->doador = 1;
        $this->assertSame(true, $entity->doador);
    }

    public function testNovaInstanciaENula()
    {
        $entity = new CoreExt_EntityStub();
        $this->assertTrue($entity->isNull());
    }

    public function testInstanciaComAlgumAtributoNaoNuloTornaseNaoNula()
    {
        $entity = new CoreExt_EntityStub();
        $entity->nome = 'fooBar';
        $this->assertFalse($entity->isNull());
    }

    /**
     * @group CoreExt_Locale
     */
    public function testAutoConversaoTiposNumericos()
    {
        $entity = new CoreExt_ChildEntityStub();
        $entity->peso = '12,5';
        $this->assertType('float', $entity->peso);
    }

    public function testSetaAtributosNaInstanciacao()
    {
        $data = [
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => true
    ];
        $entity = new CoreExt_EntityStub($data);
        $this->assertEquals($data, $entity->toArray());
    }

    public function testSetaDataMapper()
    {
        $expected = new CoreExt_EntityDataMapperStub();

        $entity = new CoreExt_EntityStub();
        $entity->setDataMapper($expected);

        $this->assertSame($expected, $entity->getDataMapper());
    }

    /**
     * @group CoreExt_Locale
     */
    public function testInstanciaRetornaObjetoCoreextLocalePorPadrao()
    {
        $entity = new CoreExt_EntityStub();
        $this->assertType('CoreExt_Locale', $entity->getLocale());
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function testConfiguraValidadorParaAtributo()
    {
        $entity = new CoreExt_EntityStub();
        $entity->setValidator('estadoCivil', new CoreExt_Validate_String());
        $this->assertType('CoreExt_Validate_String', $entity->getValidator('estadoCivil'));
    }

    /**
     * @expectedException Exception
     * @group CoreExt_Validate_Validatable
     */
    public function testConfigurarValidadorParaAtributoInexistenteLancaExcecao()
    {
        $entity = new CoreExt_EntityStub();
        $entity->setValidator('fooAttr', new CoreExt_Validate_String());
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function testAtributosDaInstanciaSaoValidos()
    {
        $data = [
      'nome' => 'fooBar'
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
    public function testValidacaoGeralRetornaFalseSeUmAtributoForInvalido()
    {
        $data = [
      'nome' => ''
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
     *
     * @see   testSetterDeReferenciaOverloadAtribuiValorParaAReferenciaENoAtributoQuandoUmaInstanciaDeCoreExtEntityComFieldIdentityEPassada
     */
    public function testValidacaoSanitizaValorDeAtributoComReferenciasDataMapper()
    {
        $data = [
      'id'   => 1,
      'nome' => ' FooBar Jr ',
      'sexo' => 1,
      'tipoSanguineo' => 1,
      'peso' => '12,5'
    ];
        $child = new CoreExt_ChildEntityStub($data);

        $data = [
      'nome' => ' FooBar ',
      'filho' => $child
    ];
        $entity = new CoreExt_ParentEntityStub($data);

        // Atribui validadores para os atributos
        $entity->setValidator('nome', new CoreExt_Validate_String());
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
    public function testValidacaoSanitizaValorDeAtributoComReferenciasEnum()
    {
        $data = [
      'nome' => 'fooBar ',
      'sexo' => 1,
      'tipoSanguineo' => 1,
      'peso' => '12,5'
    ];
        $entity = new CoreExt_ChildEntityStub($data);

        // Atribui validadores para os atributos
        $entity->setValidator('nome', new CoreExt_Validate_String());
        $entity->setValidator('sexo', new CoreExt_Validate_Choice(['choices' => [1, 2]]));
        $entity->setValidator('tipoSanguineo', new CoreExt_Validate_Choice(['choices' => [1, 2]]));
        $entity->setValidator('peso', new CoreExt_Validate_Numeric());

        // Valida e verifica pelos valores
        $this->assertTrue($entity->isValid());
        $this->assertEquals('fooBar', $entity->nome);
        $this->assertType('float', $entity->peso);
    }

    /**
     * @group CoreExt_Validate_Validatable
     */
    public function testCriaUmValidadorDependendoDoValorDeUmAtribudoDaInstancia()
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
     * @expectedException CoreExt_Exception_InvalidArgumentException
     * @group CoreExt_Validate_Validatable
     */
    public function testMetodoDeCriacaoDeValidadorSensivelAoCasoLancaExcecaoQuandoClasseNaoESubclasseDeCoreextValidateAbstract()
    {
        $entity = new CoreExt_EntityStub();
        $entity->validateIfEquals(
      'nome',
        '',
        'CoreExt_Validate_Abstract',
        [],
        []
    );
    }

    public function testTransformaEntityEmArrayDeValores()
    {
        $data = [
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => true
    ];
        $entity = new CoreExt_EntityStub($data);
        $array = $entity->filterAttr('id');
        $this->assertEquals([1], array_values($array));
    }

    public function testTransformaEntityEmArrayAssociativo()
    {
        $data = [
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => true
    ];
        $entity = new CoreExt_EntityStub($data);

        // Interface de instância
        $array = $entity->filterAttr('id', 'nome');
        $this->assertEquals([1 => 'fooBar'], $array);

        // Interface estática
        $array = CoreExt_Entity::entityFilterAttr($entity, 'id', 'nome');
        $this->assertEquals([1 => 'fooBar'], $array);
    }

    public function testTransformaEntitiesEmArrayAssociativo()
    {
        $data1 = $data2 = [
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => true
    ];

        $data2['id'] = 2;
        $data2['nome'] = 'barFoo';

        $entity1 = new CoreExt_EntityStub($data1);
        $entity2 = new CoreExt_EntityStub($data2);

        $entities = [$entity1, $entity2];

        $array = CoreExt_EntityStub::entityFilterAttr($entities, 'id', 'nome');

        $this->assertEquals([1 => 'fooBar', 2 => 'barFoo'], $array);
    }

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testSetterDeReferenciaParaAtributoInexistenteLancaExcecao()
    {
        $expected = new CoreExt_ChildEntityStub();
        $expected->setReference('foo', []);
    }

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testSetterDeReferenciaLancaExcecaoCasoArrayDeDefinicaoContenhaConfiguracaoInexistente()
    {
        $expected = new CoreExt_ChildEntityStub();
        $expected->setReference('nome', ['autoload' => true]);
    }

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testSetterDeReferenciaLancaExcecaoParaClasseCoreextDataMapperInvalida()
    {
        $expected = new CoreExt_ChildEntityStub();
        $expected->setReference('nome', ['class' => new stdClass()]);
    }

    public function testSetterDeReferenciaOverloadAtribuiValorParaAReferenciaQuandoEInteiro()
    {
        $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => 1]);
        $this->assertEquals(1, $entity->get('filho'));
    }

    public function testSetterDeReferenciaOverloadAtribuiValorNullParaAReferenciaQuandoElaENullable()
    {
        $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => null]);
        $this->assertNull($entity->filho);
        $this->assertNull($entity->get('filho'));
    }

    /**
     * @group Overload
     */
    public function testSetterDeReferenciaOverloadAtribuiValorParaAReferenciaENoAtributoQuandoUmaInstanciaDeCoreExtEntityComFieldIdentityEPassada()
    {
        $child = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'FooBar Jr']);
        $entity = new CoreExt_ParentEntityStub(['id' => 1, 'nome' => 'fooBar', 'filho' => $child]);
        $this->assertEquals($child, $entity->filho);
        $this->assertEquals(1, $entity->get('filho'));
    }

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testSetterDeReferenciaOverloadLancaExcecaoParaAtribuicaoDeValorNullEmReferenciaNaoNullable()
    {
        $entity = new CoreExt_ChildEntityStub(['id' => 1, 'sexo' => null]);
    }

    public function testEntityRecuperadaPeloDataMapperEMarcadaComoVelha()
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

    public function testReferenciaInstanciaAClasseAtribuidaAutomaticamente()
    {
        $parent = new CoreExt_ParentEntityStub(['filho' => 1]);
        $child  = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'fooBar', 'sexo' => 1]);

        $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
        $filhoMapper->expects($this->once())
                ->method('find')
                ->with(1)
                ->will($this->returnValue($child));

        $parent->setReferenceClass('filho', $filhoMapper);

        $enum = CoreExt_EnumSexStub::getInstance();
        $expected = $enum[CoreExt_EnumSexStub::MALE];

        $this->assertEquals($child, $parent->filho);
        $this->assertEquals($expected, $child->sexo);
    }

    public function testInstanciaDeClassNaoEspecificaEArmazenadaNoArrayStaticDaClasse()
    {
        CoreExt_EntityStub::addClassToStorage(
        'StdClassExtStub',
      null,
        'CoreExt/_stub/StdClassExt.php'
    );
        $this->assertType('StdClassExtStub', CoreExt_EntityStub::getClassFromStorage('StdClassExtStub'));
    }

    public function testInstanciaDeClassNaoEspecificaArmazenaInstanciaEspecifica()
    {
        $obj = new stdClass();
        $oid = spl_object_hash($obj);
        CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
        $this->assertType('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
        $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
    }

    public function testInstanciaDeClassNaoEspecificaPodeSerRepostaPorNovaInstancia()
    {
        // Só para facilitar.
        for ($i = 0; $i <= 1; $i++) {
            $obj = new stdClass();
            $obj->i = $i;
            $oid = spl_object_hash($obj);
            CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
            $this->assertType('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
            $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
        }
        CoreExt_EntityStub::addClassToStorage('stdClass');
        $this->assertType('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
        $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
    }

    public function testInstanciaDeClassESobresritaPorInstanciasSticky()
    {
        $oid1 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass(), null, true);
        $oid1 = spl_object_hash($oid1);

        $oid2 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass(), null, true);
        $oid2 = spl_object_hash($oid2);

        $this->assertNotEquals($oid2, $oid1);
    }

    public function testInstanciaDeClassNaoEspecificaRetornaNullQuandoNaoEncontraUmaInstanciaDaClasse()
    {
        $this->assertNull(CoreExt_EntityStub::getClassFromStorage('fooBar'));
    }

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testInstanciaDeClassNaoEspecificaLancaExcecaoQuandoInstanciaPassadaNaoTemAMesmaAssinaturaDoNomeDeClasseInformado()
    {
        CoreExt_EntityStub::addClassToStorage('fooBar', new stdClass());
    }

    public function testLazyLoadParaReferenciaNumericaAInstanciaEntity()
    {
        $data = [
      'nome' => 'fooBar',
      'filho' => 1
    ];

        $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

        $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
        $filhoMapper->expects($this->once())
                ->method('find')
                ->with(1)
                ->will($this->returnValue($expected));

        $parent = new CoreExt_ParentEntityStub($data);
        $parent->setReference('filho', ['value' => 1, 'class' => $filhoMapper]);

        $this->assertType('CoreExt_ChildEntityStub', $parent->filho);
        $this->assertEquals(1, $parent->filho->id);
    }

    /**
     * @group LazyLoad
     * @group CoreExt_Validate_Validatable
     */
    public function testLazyLoadNaoCarregaInstanciaEntityParaValidacao()
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

    public function testLazyLoadParaReferenciaObjetoAInstanciaEntityNaoCausaLazyLoad()
    {
        $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

        $data = [
      'id'    => 1,
      'nome'  => 'fooBar',
      'filho' => $expected
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

        $this->assertType('CoreExt_ChildEntityStub', $parent->filho);
        $this->assertEquals(1, $parent->filho->id);
        $this->assertEquals('barFoo', $parent->filho->nome);

        // toArray() causa chamadas lazy load. Garantindo que não ocorra.
        $this->assertEquals($data, $parent->toArray());

        // toDataArray() retorna sempre a referência numérica.
        $data['filho'] = 1;
        $this->assertEquals($data, $parent->toDataArray());
    }

    public function testLazyLoadNaoERealizadoQuandoReferenciaIgualA0OuNulo()
    {
        $expected = new CoreExt_ChildEntityStub(['id' => 1, 'nome' => 'barFoo']);

        $data = [
      'id'    => 1,
      'nome'  => 'fooBar'
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

    /**
     * @expectedException CoreExt_Exception_InvalidArgumentException
     */
    public function testLazyLoadParametroQueEReferenciaLancaExcecaoQuandoNaoEDoTipoIntegerOuCoreextEntity()
    {
        $expected = new stdClass();

        $data = [
      'id'    => 1,
      'nome'  => 'fooBar',
      'filho' => $expected
    ];

        $parent = new CoreExt_ParentEntityStub($data);
    }

    public function testLazyLoadDeReferenciaAUmTipoCoreextEnum()
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
