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
 * @package     App_Model
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;

require_once 'App/Model/IedFinder.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';
require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaSerieDisciplina.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaAnoLetivo.inc.php';
require_once 'include/pmieducar/clsPmieducarAnoLetivoModulo.inc.php';
require_once 'include/pmieducar/clsPmieducarTurma.inc.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';

/**
 * App_Model_IedFinderTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     App_Model
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class App_Model_IedFinderTest extends UnitBaseTest
{
  /**
   * @todo Refatorar método para uma classe stub, no diretório do módulo
   *   TabelaArredondamento
   * @todo Está copiado em modules/Avaliacao/_tests/BoletimTest.php
   */
  protected function _getTabelaArredondamento()
  {
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => -1,
      'valorMaximo'          => 0
    );

    $tabelaValores = array();
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
               ->will($this->returnValue($tabelaValores));

    $tabelaDataMapper = new TabelaArredondamento_Model_TabelaDataMapper();
    $tabelaDataMapper->setTabelaValorDataMapper($mapperMock);

    $tabela = new TabelaArredondamento_Model_Tabela(array('nome' => 'Numéricas'));
    $tabela->setDataMapper($tabelaDataMapper);
    return $tabela;
  }

  /**
   * Configura mocks para ComponenteCurricular_Model_ComponenteDataMapper e
   * ComponenteCurricular_Model_TurmaDataMapper para o método getComponentesTurma().
   *
   * @return array ('componenteMock', 'turmaMock', 'expected')
   */
  protected function _getComponentesTurmaMock()
  {
    $returnComponenteMock = array(
      1 => new ComponenteCurricular_Model_Componente(
        array('id' => 1, 'nome' => 'Matemática', 'cargaHoraria' => 100)
      ),
      2 => new ComponenteCurricular_Model_Componente(
        array('id' => 2, 'nome' => 'Português', 'cargaHoraria' => 100)
      )
    );

    $expected = $returnComponenteMock;

    $componenteMock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $componenteMock->expects($this->exactly(2))
                   ->method('findComponenteCurricularAnoEscolar')
                   ->will($this->onConsecutiveCalls(
                     $returnComponenteMock[1], $returnComponenteMock[2]
                   ));

    $returnTurmaMock = array(
      new ComponenteCurricular_Model_Turma(
        array('componenteCurricular' => 1, 'cargaHoraria' => 200)
      ),
      new ComponenteCurricular_Model_Turma(
        array('componenteCurricular' => 2, 'cargaHoraria' => NULL)
      )
    );

    $turmaMock = $this->getCleanMock('ComponenteCurricular_Model_TurmaDataMapper');
    $turmaMock->expects($this->once())
              ->method('findAll')
              ->with(array(), array('turma' => 1))
              ->will($this->returnValue($returnTurmaMock));

    // O primeiro componente tem carga horária definida na turma, o segundo usa o padrão do componente
    $expected[1] = clone $expected[1];
    $expected[1]->cargaHoraria = 200;

    return array(
      'componenteMock' => $componenteMock,
      'turmaMock'      => $turmaMock,
      'expected'       => $expected
    );
  }

  public function testGetCurso()
  {
    $returnValue = array(
      'nm_curso' => 'Ensino Fundamental'
    );

    $mock = $this->getCleanMock('clsPmieducarCurso');
    $mock->expects($this->once())
         ->method('detalhe')
         ->will($this->returnValue($returnValue));

    // Registra a instância no repositório de classes de CoreExt_Entity
    $instance = App_Model_IedFinder::addClassToStorage(
      'clsPmieducarCurso', $mock, NULL, TRUE);

    $curso = App_Model_IedFinder::getCurso(1);
    $this->assertEquals(
      $returnValue['nm_curso'], $curso,
      '::getCurso() retorna o nome do curso através de uma busca pelo código.'
    );
  }

  public function testGetInstituicoes()
  {
    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Instituição'));
    $expected = array(1 => 'INSTITUIÇÃO');

    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->once())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Registra a instância no repositório de classes de CoreExt_Entity
    $instance = App_Model_IedFinder::addClassToStorage(
      'clsPmieducarInstituicao', $mock);

    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->assertEquals(
      $expected, $instituicoes,
      '::getInstituicoes() retorna todas as instituições cadastradas.'
    );
  }

  public function testGetSeries()
  {
    $returnValue = array(
      1 => array('cod_serie' => 1, 'ref_ref_cod_instituicao' => 1, 'nm_serie' => 'PRÉ'),
      2 => array('cod_serie' => 2, 'ref_ref_cod_instituicao' => 2, 'nm_serie' => 'SER')
    );

    $mock = $this->getCleanMock('clsPmieducarSerie');
    $mock->expects($this->exactly(2))
         ->method('lista')
         ->will($this->onConsecutiveCalls($returnValue, array($returnValue[1])));

    // Registra a instância no repositório de classes de CoreExt_Entity
    $instance = CoreExt_Entity::addClassToStorage(
      'clsPmieducarSerie', $mock, NULL, TRUE);

    $series = App_Model_IedFinder::getSeries();
    $this->assertEquals(
    array(
        1 => 'PRÉ',
        2 => 'SER'
      ), $series,
      '::getSeries() retorna todas as séries cadastradas.'
    );

    $series = App_Model_IedFinder::getSeries(1);
    $this->assertEquals(
      array(1 => 'PRÉ'), $series,
      '::getSeries() retorna todas as séries de uma instituição.'
    );
  }

  public function testGetTurmas()
  {
    $returnValue = array(1 => array('cod_turma' => 1, 'nm_turma' => 'Primeiro ano', 'ano' => null));
    $expected = array(1 => 'Primeiro ano - Sem ano');

    $mock = $this->getCleanMock('clsPmieducarTurma');
    $mock->expects($this->once())
         ->method('lista')
         ->with(NULL, NULL, NULL, NULL, 1)
         ->will($this->returnValue($returnValue));

    $instance = CoreExt_Entity::addClassToStorage(
      'clsPmieducarTurma', $mock, NULL, TRUE);

    $turmas = App_Model_IedFinder::getTurmas(1);
    $this->assertEquals(
      $expected, $turmas,
      '::getTurmas() retorna todas as turmas de uma escola.'
    );
  }

  public function testGetEscolaSerieDisciplina()
  {
      //Método foi alterado. Terá que ser escrito um novo teste
      $this->markTestSkipped();
    $returnAnoEscolar = array(
      1 => new ComponenteCurricular_Model_Componente(
        array('id' => 1, 'nome' => 'Matemática', 'cargaHoraria' => 100)
      ),
      2 => new ComponenteCurricular_Model_Componente(
        array('id' => 2, 'nome' => 'Português', 'cargaHoraria' => 100)
      ),
      3 => new ComponenteCurricular_Model_Componente(
        array('id' => 3, 'nome' => 'Ciências', 'cargaHoraria' => 60)
      ),
      4 => new ComponenteCurricular_Model_Componente(
        array('id' => 4, 'nome' => 'Física', 'cargaHoraria' => 60)
      )
    );

    $expected = $returnAnoEscolar;

    $anoEscolarMock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $anoEscolarMock->expects($this->exactly(4))
                   ->method('findComponenteCurricularAnoEscolar')
                   ->will($this->onConsecutiveCalls(
                     $returnAnoEscolar[1], $returnAnoEscolar[2], $returnAnoEscolar[3], $returnAnoEscolar[4]
                   ));

    // Retorna para clsPmieducarEscolaSerieDisciplina
    $returnEscolaSerieDisciplina = array(
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 1, 'carga_horaria' => 80),
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 2, 'carga_horaria' => NULL),
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 3, 'carga_horaria' => NULL),
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 4, 'carga_horaria' => NULL),
    );

    // Mock para clsPmieducarEscolaSerieDisciplina
    $escolaMock = $this->getCleanMock('clsPmieducarEscolaSerieDisciplina');
    $escolaMock->expects($this->any())
               ->method('lista')
               ->with(1, 1, NULL, 1)
               ->will($this->returnValue($returnEscolaSerieDisciplina));

    App_Model_IedFinder::addClassToStorage('clsPmieducarEscolaSerieDisciplina', $escolaMock, NULL, TRUE);

    // O primeiro componente tem uma carga horária definida em escola-série.
    $expected[1] = clone $returnAnoEscolar[1];
    $expected[1]->cargaHoraria = 80;

    $componentes = App_Model_IedFinder::getEscolaSerieDisciplina(1, 1, $anoEscolarMock);
    $this->assertEquals(
      $expected, $componentes,
      '::getEscolaSerieDisciplina() retorna os componentes de um escola-série.'
    );
  }

  public function testGetComponentesTurma()
  {
      //Método foi alterado. Terá que ser escrito um novo teste
      $this->markTestSkipped();
    $mocks = $this->_getComponentesTurmaMock();

    $componentes = App_Model_IedFinder::getComponentesTurma(
      1, 1, 1, $mocks['turmaMock'], $mocks['componenteMock']
    );

    $this->assertEquals(
      $mocks['expected'], $componentes,
      '::getComponentesTurma() retorna os componentes de uma turma.'
    );
  }

  public function testGetMatriculaAlunoNaoEnturmado()
  {
    $this->expectException(StudentNotEnrolledInSchoolClass::class);
    $this->expectExceptionMessage('Aluno não enturmado.');
    App_Model_IedFinder::getMatricula(1);
  }

  public function testGetRegraAvaliacaoPorMatricula()
  {
    $expected = new RegraAvaliacao_Model_Regra(array(
      'id'                   => 1,
      'nome'                 => 'Regra geral',
      'tipoNota'             => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
      'tipoProgressao'       => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
      'tipoPresenca'         => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
      'media'                => 6,
      'tabelaArredondamento' => $this->_getTabelaArredondamento()
    ));

    // Marca como "old", para indicar que foi recuperado via CoreExt_DataMapper
    $expected->markOld();

    // Retorna para matrícula
    $returnMatricula = array(
      'cod_matricula'      => 1,
      'ref_ref_cod_escola' => 1,
      'ref_ref_cod_serie'  => 1,
      'ref_cod_curso'      => 1,
      'aprovado'           => 1,
      'serie_regra_avaliacao_id' => 1,
      'ref_cod_aluno'      => 1,
      'escola_utiliza_regra_diferenciada' => null
    );

    // Mock para RegraAvaliacao_Model_DataMapper
    $mapperMock = $this->getCleanMock('RegraAvaliacao_Model_RegraDataMapper');
    $mapperMock->expects($this->once())
               ->method('find')
               ->with(1)
               ->will($this->returnValue($expected));

    $regraAvaliacao = App_Model_IedFinder::getRegraAvaliacaoPorMatricula(1, $mapperMock, $returnMatricula);
    $this->assertEquals(
      $expected, $regraAvaliacao,
      '::getRegraAvaliacaoPorMatricula() retorna a regra de avaliação de uma matrícula.'
    );
  }

  /**
   * @depends App_Model_IedFinderTest::testGetRegraAvaliacaoPorMatricula
   */
  public function testGetComponentesPorMatricula()
  {
      //Método foi alterado. Terá que ser escrito um novo teste
      $this->markTestSkipped();
    // A turma possui apenas 2 componentes, com os ids: 1 e 2
        $mocks = $this->_getComponentesTurmaMock();

    // Retorna para clsPmieducarDispensaDisciplina
    $returnDispensa = array(
      array('ref_cod_matricula' => 1, 'ref_cod_disciplina' => 2)
    );

    // Mock para clsPmieducarDispensaDisciplina
    $dispensaMock = $this->getCleanMock('clsPmieducarDispensaDisciplina');
     $dispensaMock->expects($this->once())
                 ->method('disciplinaDispensadaEtapa')
                 ->with(1, 1, 1)
                 ->will($this->returnValue($returnDispensa));

    CoreExt_Entity::addClassToStorage('clsPmieducarDispensaDisciplina',
      $dispensaMock, NULL, TRUE);

    $matricula = [
        'ref_ref_cod_serie'  => 1,
        'ref_ref_cod_escola' => 1,
        'ref_cod_turma'      => 1,
        'ano'                => null,
        'dependencia'        => null
    ];

    $componentes = App_Model_IedFinder::getComponentesPorMatricula(
      1, $mocks['componenteMock'], $mocks['turmaMock'], null, null, null, $matricula
    );

    $expected = $mocks['expected'];
    $expected = array(1 => clone $expected[1]);

    $this->assertEquals(
      $expected, $componentes,
      '::getComponentesPorMatricula() retorna os componentes curriculares de uma matrícula, descartando aqueles em regime de dispensa (dispensa de componente)'
    );
  }

  /**
   * @depends App_Model_IedFinderTest::testGetRegraAvaliacaoPorMatricula
   */
  public function testGetQuantidadeDeModulosMatricula()
  {
    $this->markTestSkipped();

    $returnEscolaAno = array(
      array('ref_cod_escola' => 1, 'ano' => 2009, 'andamento' => 1, 'ativo' => 1)
    );

    $returnAnoLetivo = array(
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 1, 'ref_cod_modulo' => 1),
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 2, 'ref_cod_modulo' => 1),
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 3, 'ref_cod_modulo' => 1),
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 4, 'ref_cod_modulo' => 1)
    );

    $returnMatriculaTurma = array(
      array('ref_cod_matricula' => 1, 'ref_cod_turma' => 1)
    );

    $returnModulo = array('cod_modulo' => 1, 'nm_tipo' => 'Bimestre');

    // Mock para escola ano letivo (ano letivo em andamento)
    $escolaAnoMock = $this->getCleanMock('clsPmieducarEscolaAnoLetivo');
    $escolaAnoMock->expects($this->any())
                  ->method('lista')
                  ->with(1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1)
                  ->will($this->returnValue($returnEscolaAno));

    // Mock para o ano letivo (módulos do ano)
    $anoLetivoMock = $this->getCleanMock('clsPmieducarAnoLetivoModulo');
    $anoLetivoMock->expects($this->any())
                  ->method('lista')
                  ->with(2009, 1)
                  ->will($this->returnValue($returnAnoLetivo));

    $matriculaTurmaMock = $this->getCleanMock('clsPmieducarMatriculaTurma');
    $matriculaTurmaMock->expects($this->any())
                       ->method('lista')
                       ->with(1)
                       ->will($this->onConsecutiveCalls($returnMatriculaTurma, $returnMatriculaTurma));

    $moduloMock = $this->getCleanMock('clsPmieducarModulo');
    $moduloMock->expects($this->any())
               ->method('detalhe')
               ->will($this->onConsecutiveCalls($returnModulo, $returnModulo));

    $returnCurso = array('cod_curso' => 1, 'carga_horaria' => 800, 'hora_falta' => (50 / 60), 'padrao_ano_escolar' => 0);
    $cursoMock = $this->getCleanMock('clsPmieducarCurso');
    $cursoMock->expects($this->any())
               ->method('detalhe')
               ->will($this->returnValue($returnCurso));
   $returnTurmaModulo = array(
       array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 1),
       array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 2),
       array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 3),
       array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 4)
   );
   $turmaModuloMock = $this->getCleanMock('clsPmieducarTurmaModulo');
   $turmaModuloMock->expects($this->at(0))
               ->method('lista')
               ->with(1)
               ->will($this->returnValue($returnTurmaModulo));

    // Adiciona mocks ao repositório estático
    App_Model_IedFinder::addClassToStorage('clsPmieducarEscolaAnoLetivo',
      $escolaAnoMock, NULL, TRUE);
    App_Model_IedFinder::addClassToStorage('clsPmieducarAnoLetivoModulo',
      $anoLetivoMock, NULL, TRUE);
    App_Model_IedFinder::addClassToStorage('clsPmieducarMatriculaTurma',
      $matriculaTurmaMock, NULL, TRUE);
    App_Model_IedFinder::addClassToStorage('clsPmieducarModulo',
      $moduloMock, NULL, TRUE);
    App_Model_IedFinder::addClassToStorage('clsPmieducarCurso',
      $cursoMock, NULL, TRUE);
    App_Model_IedFinder::addClassToStorage('clsPmieducarTurmaModulo',
        $turmaModuloMock, NULL, TRUE);

    $matricula = [
        'ref_ref_cod_escola' => 1,
        'ref_cod_curso'      => 1,
        'ref_cod_turma'      => 1,
        'ano'                => 2018
    ];
    $modulos = App_Model_IedFinder::getQuantidadeDeModulosMatricula(1, $matricula);

    $this->assertEquals(
      4, $modulos,
      '::getQuantidadeDeModulosMatricula() retorna a quantidade de módulos para uma matrícula de ano escolar padrão (curso padrão ano escolar).'
    );
  }

  /**
   * @depends App_Model_IedFinderTest::testGetRegraAvaliacaoPorMatricula
   */
  public function testGetQuantidadeDeModulosMatriculaCursoAnoNaoPadrao()
  {
    $this->markTestSkipped();

    // Curso não padrão
    $returnCurso = array('cod_curso' => 1, 'carga_horaria' => 800, 'hora_falta' => (50 / 60), 'padrao_ano_escolar' => 0);

    $cursoMock = $this->getCleanMock('clsPmieducarCurso');
    $cursoMock->expects($this->any())
              ->method('detalhe')
              ->will($this->returnValue($returnCurso));

    CoreExt_Entity::addClassToStorage('clsPmieducarCurso', $cursoMock, NULL, TRUE);

    $returnTurmaModulo = array(
      array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 1),
      array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 2),
      array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 3),
      array('ref_cod_turma' => 1, 'ref_cod_modulo' => 1, 'sequencial' => 4)
    );

    $turmaModuloMock = $this->getCleanMock('clsPmieducarTurmaModulo');
    $turmaModuloMock->expects($this->at(0))
                    ->method('lista')
                    ->with(1)
                    ->will($this->returnValue($returnTurmaModulo));

    App_Model_IedFinder::addClassToStorage('clsPmieducarTurmaModulo',
      $turmaModuloMock, NULL, TRUE);

    $matricula = [
        'ref_ref_cod_escola' => 1,
        'ref_cod_curso'      => 1,
        'ref_cod_turma'      => 1,
        'ano'                => 2018
    ];
    $etapas = App_Model_IedFinder::getQuantidadeDeModulosMatricula(1, $matricula);

    $this->assertEquals(
      4, $etapas,
      '::getQuantidadeDeModulosMatricula() retorna a quantidade de módulos para uma matrícula de um ano escolar não padrão (curso não padrão).'
    );
  }
}
