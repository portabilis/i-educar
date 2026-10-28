<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyInstitutionFactory;
use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(UnitBaseTest::class, DatabaseTransactions::class);

beforeEach(function () {
    /**
     * @todo Refatorar método para uma classe stub, no diretório do módulo
     *   TabelaArredondamento
     * @todo Está copiado em modules/Avaliacao/_tests/BoletimTest.php
     */
    $this->_getTabelaArredondamento = function () {
        $data = [
            'tabelaArredondamento' => 1,
            'nome' => null,
            'descricao' => null,
            'valorMinimo' => -1,
            'valorMaximo' => 0,
        ];

        $tabelaValores = [];
        for ($i = 0; $i <= 10; $i++) {
            $data['nome'] = $i;
            $data['valorMinimo'] += 1;
            $data['valorMaximo'] += 1;

            if ($i == 10) {
                $data['valorMinimo'] = 9;
                $data['valorMaximo'] = 10;
            }

            $tabelaValores[$i] = new TabelaArredondamento_Model_TabelaValor($data);
        }

        $mapperMock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
        $mapperMock->expects($this->any())
            ->method('findAll')
            ->willReturn($tabelaValores);

        $tabelaDataMapper = new TabelaArredondamento_Model_TabelaDataMapper;
        $tabelaDataMapper->setTabelaValorDataMapper($mapperMock);

        $tabela = new TabelaArredondamento_Model_Tabela(['nome' => 'Numéricas']);
        $tabela->setDataMapper($tabelaDataMapper);

        return $tabela;
    };

    /**
     * Configura mocks para ComponenteCurricular_Model_ComponenteDataMapper e
     * ComponenteCurricular_Model_TurmaDataMapper para o método getComponentesTurma().
     *
     * @return array ('componenteMock', 'turmaMock', 'expected')
     */
    $this->_getComponentesTurmaMock = function () {
        $returnComponenteMock = [
            1 => new ComponenteCurricular_Model_Componente(
                ['id' => 1, 'nome' => 'Matemática', 'cargaHoraria' => 100]
            ),
            2 => new ComponenteCurricular_Model_Componente(
                ['id' => 2, 'nome' => 'Português', 'cargaHoraria' => 100]
            ),
        ];

        $expected = $returnComponenteMock;

        $componenteMock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
        $componenteMock->expects($this->exactly(2))
            ->method('findComponenteCurricularAnoEscolar')
            ->willReturnOnConsecutiveCalls(
                $returnComponenteMock[1],
                $returnComponenteMock[2]
            );

        $returnTurmaMock = [
            new ComponenteCurricular_Model_Turma(
                ['componenteCurricular' => 1, 'cargaHoraria' => 200]
            ),
            new ComponenteCurricular_Model_Turma(
                ['componenteCurricular' => 2, 'cargaHoraria' => null]
            ),
        ];

        $turmaMock = $this->getCleanMock('ComponenteCurricular_Model_TurmaDataMapper');
        $turmaMock->expects($this->once())
            ->method('findAll')
            ->with([], ['turma' => 1])
            ->willReturn($returnTurmaMock);

        // O primeiro componente tem carga horária definida na turma, o segundo usa o padrão do componente
        $expected[1] = clone $expected[1];
        $expected[1]->cargaHoraria = 200;

        return [
            'componenteMock' => $componenteMock,
            'turmaMock' => $turmaMock,
            'expected' => $expected,
        ];
    };
});

test('get curso', function () {
    $curso = LegacyCourseFactory::new()->create([
        'nm_curso' => 'Ensino Fundamental',
        'descricao' => null,
    ]);

    $this->assertEquals(
        'Ensino Fundamental',
        App_Model_IedFinder::getCurso($curso->getKey()),
        '::getCurso() retorna o nome do curso através de uma busca pelo código.'
    );

    $cursoComDescricao = LegacyCourseFactory::new()->create([
        'nm_curso' => 'Ensino Médio',
        'descricao' => 'Integrado',
    ]);

    $this->assertEquals(
        'Ensino Médio (Integrado)',
        App_Model_IedFinder::getCurso($cursoComDescricao->getKey()),
        '::getCurso() concatena a descrição ao nome do curso.'
    );

    $this->assertNull(
        App_Model_IedFinder::getCurso(0),
        '::getCurso() retorna nulo quando o curso não existe.'
    );
});

test('get instituicoes', function () {
    $returnValue = [['cod_instituicao' => 1, 'nm_instituicao' => 'Instituição']];
    $expected = [1 => 'INSTITUIÇÃO'];

    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->once())
        ->method('lista')
        ->willReturn($returnValue);

    // Registra a instância no repositório de classes de CoreExt_Entity
    $instance = App_Model_IedFinder::addClassToStorage(
        'clsPmieducarInstituicao',
        $mock
    );

    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->assertEquals(
        $expected,
        $instituicoes,
        '::getInstituicoes() retorna todas as instituições cadastradas.'
    );
});

test('get series', function () {
    $instituicao = LegacyInstitutionFactory::new()->create();
    $outraInstituicao = LegacyInstitutionFactory::new()->create();

    $serie = LegacyGradeFactory::new()->create([
        'nm_serie' => 'pré',
        'descricao' => '',
        'ref_cod_curso' => LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $instituicao,
        ]),
    ]);

    $serieDeOutraInstituicao = LegacyGradeFactory::new()->create([
        'nm_serie' => 'ser',
        'descricao' => '',
        'ref_cod_curso' => LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $outraInstituicao,
        ]),
    ]);

    $series = App_Model_IedFinder::getSeries();

    $this->assertSame(
        'PRÉ',
        $series[$serie->getKey()],
        '::getSeries() retorna o nome da série em caixa alta.'
    );
    $this->assertArrayHasKey(
        $serieDeOutraInstituicao->getKey(),
        $series,
        '::getSeries() sem filtro retorna as séries de todas as instituições.'
    );

    $seriesDaInstituicao = App_Model_IedFinder::getSeries($instituicao->getKey());

    $this->assertArrayHasKey(
        $serie->getKey(),
        $seriesDaInstituicao,
        '::getSeries() filtra pela instituição informada.'
    );
    $this->assertArrayNotHasKey(
        $serieDeOutraInstituicao->getKey(),
        $seriesDaInstituicao,
        '::getSeries() não retorna série de outra instituição.'
    );
});

test('get turmas', function () {
    $returnValue = [1 => ['cod_turma' => 1, 'nm_turma' => 'Primeiro ano', 'ano' => null]];
    $expected = [1 => 'Primeiro ano - Sem ano'];

    $mock = $this->getCleanMock('clsPmieducarTurma');
    $mock->expects($this->once())
        ->method('lista')
        ->with(null, null, null, null, 1)
        ->willReturn($returnValue);

    $instance = CoreExt_Entity::addClassToStorage(
        'clsPmieducarTurma',
        $mock,
        null,
        true
    );

    $turmas = App_Model_IedFinder::getTurmas(1);
    $this->assertEquals(
        $expected,
        $turmas,
        '::getTurmas() retorna todas as turmas de uma escola.'
    );
});

test('get escola serie disciplina', function () {
    // Método foi alterado. Terá que ser escrito um novo teste
    $this->markTestSkipped();
});

test('get componentes turma', function () {
    // Método foi alterado. Terá que ser escrito um novo teste
    $this->markTestSkipped();
    $mocks = ($this->_getComponentesTurmaMock)();

    $componentes = App_Model_IedFinder::getComponentesTurma(
        1,
        1,
        1,
        $mocks['turmaMock'],
        $mocks['componenteMock']
    );

    $this->assertEquals(
        $mocks['expected'],
        $componentes,
        '::getComponentesTurma() retorna os componentes de uma turma.'
    );
});

test('get matricula aluno nao enturmado', function () {
    $this->expectException(StudentNotEnrolledInSchoolClass::class);
    $this->expectExceptionMessage('Aluno não enturmado.');
    App_Model_IedFinder::getMatricula(1);
});

test('get regra avaliacao por matricula', function () {
    $expected = new RegraAvaliacao_Model_Regra([
        'id' => 1,
        'nome' => 'Regra geral',
        'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
        'tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
        'tipoPresenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        'media' => 6,
        'tabelaArredondamento' => ($this->_getTabelaArredondamento)(),
    ]);

    // Marca como "old", para indicar que foi recuperado via CoreExt_DataMapper
    $expected->markOld();

    // Retorna para matrícula
    $returnMatricula = [
        'cod_matricula' => 1,
        'ref_ref_cod_escola' => 1,
        'ref_ref_cod_serie' => 1,
        'ref_cod_curso' => 1,
        'aprovado' => 1,
        'serie_regra_avaliacao_id' => 1,
        'ref_cod_aluno' => 1,
        'escola_utiliza_regra_diferenciada' => null,
    ];

    // Mock para RegraAvaliacao_Model_DataMapper
    $mapperMock = $this->getCleanMock('RegraAvaliacao_Model_RegraDataMapper');
    $mapperMock->expects($this->once())
        ->method('find')
        ->with(1)
        ->willReturn($expected);

    $regraAvaliacao = App_Model_IedFinder::getRegraAvaliacaoPorMatricula(1, $mapperMock, $returnMatricula);
    $this->assertEquals(
        $expected,
        $regraAvaliacao,
        '::getRegraAvaliacaoPorMatricula() retorna a regra de avaliação de uma matrícula.'
    );
});

test('get componentes por matricula', function () {
    // Método foi alterado. Terá que ser escrito um novo teste
    $this->markTestSkipped();
})->depends('get regra avaliacao por matricula');
