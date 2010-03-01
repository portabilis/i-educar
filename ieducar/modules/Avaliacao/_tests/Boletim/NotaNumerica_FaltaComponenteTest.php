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
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Avaliacao/_tests/Boletim/Common.php';

/**
 * NotaNumerica_FaltaComponente class.
 *
 * Configura mocks para todas as dependências de Avaliacao_Service_Boletim.
 *
 * A configuração é baseada nos mocks criados para para App_Model_IedFinder,
 * onde é retornado os componentes curriculares 1 (matemática) e 3 (ciências),
 * já que o aluno estaria dispensado dos outros dois componentes.
 *
 * - RegraAvaliacao_Model_Regra: nota numérica, progressiva, falta
 * contabilizada por componente curricular, média 6, tabela de arredondamento,
 * fórmula de média (aritmética simples)
 *
 * - FormulaMedia_Model_Formula: aritmética simples (Se / Et)
 *
 * - ComponenteCurricular_Model_ComponenteDataMapper: retorna componentes
 * em que o aluno estaria cursando, conforme retornado por
 * App_Model_IedFinder::getComponentesPorMatricula
 *
 * - Avaliacao_Model_NotaAlunoDataMapper: retorna uma instância de
 * Avaliacao_Model_NotaAluno, para testar inicialização do service
 *
 * - Avaliacao_Model_NotaComponenteDataMapper: retorna uma instância de
 * Avaliacao_Model_NotaComponente, para testar inicialização do service
 *
 * - Avaliacao_Model_FaltaAlunoDataMapper: retorna uma instância de
 * Avaliacao_Model_FaltaAluno, para testar inicialização do service
 *
 * - Avaliacao_Model_FaltaAbstractDataMapper: retorna uma instância de
 * Avaliacao_Model_FaltaComponente, para testa inicialização do service
 * e casos para o qual a regra de avaliação registre faltas por componentes
 * curriculares
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Boletim_NotaNumerica_FaltaComponenteTest extends Boletim_Common
{
  /**
   * @see Boletim_Common#_setUpRegraAvaliacao()
   */
  protected function _setUpRegraAvaliacao()
  {
    $formulaMedia = new FormulaMedia_Model_Formula(array(
      'id' => 1,
      'nome' => 'Média aritmética',
      'formulaMedia' => 'Se / Et'
    ));

    $formulaRecuperacao = new FormulaMedia_Model_Formula(array(
      'id' => 2,
      'nome' => 'Média Ponderada',
      'formulaMedia' => 'Rc * 0.4 + Se / Et * 0.6'
    ));

    // Retorno para mock RegraAvaliacao
    $expected = new RegraAvaliacao_Model_Regra(array(
      'id'                   => 1,
      'nome'                 => 'Regra geral',
      'tipoNota'             => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
      'tipoProgressao'       => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
      'tipoPresenca'         => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
      'media'                => 6,
      'tabelaArredondamento' => $this->_getTabelaArredondamentoNumerica(),
      'formulaMedia'         => $formulaMedia,
      'formulaRecuperacao'   => $formulaRecuperacao,
      'porcentagemPresenca'  => 75
    ));

    $regraMock = $this->getCleanMock('RegraAvaliacao_Model_RegraDataMapper');
    $regraMock->expects($this->once())
              ->method('find')
              ->with(1)
              ->will($this->returnValue($expected));

    return $regraMock;
  }

  /**
   * @see Boletim_Common#_setUpNotaAlunoDataMapper()
   */
  protected function _setUpNotaAlunoDataMapper()
  {
    $notaAluno = $this->_setUpNotaAluno();

    // Configura expectativas do mock de NotaAluno, que irá retornar vazio
    // (aluno ainda não possui notas lançadas)
    $notaAlunoMock = $this->getCleanMock('Avaliacao_Model_NotaAlunoDataMapper');
    $notaAlunoMock->expects($this->any())
                  ->method('findAll')
                  ->with(array(), array('matricula' => 1))
                  ->will($this->onConsecutiveCalls(array(), array($notaAluno)));

    $notaAlunoMock->expects($this->any())
                  ->method('save')
                  ->will($this->returnValue(TRUE));

    return $notaAlunoMock;
  }

  protected function _setUpNotaAluno()
  {
    $notaAluno = new Avaliacao_Model_NotaAluno(array('id' => 1, 'matricula' => 1));

    // Guarda na instância
    $this->_notaAlunoExpected = $notaAluno;

    return $notaAluno;
  }

  /**
   * @see Boletim_Common#_setUpNotaComponenteDataMapper()
   */
  protected function _setUpNotaComponenteDataMapper()
  {
    $notaComponente = array(new Avaliacao_Model_NotaComponente(array(
      'id' => 1,
      'notaAluno' => 1,
      'componenteCurricular' => 1,
      'nota' => 7.5,
      'notaArredondada' => 7,
      'etapa' => 2
    )));

    $notaComponenteMock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');
    $notaComponenteMock->expects($this->at(0))
                       ->method('findAll')
                       ->with(array(), array('notaAluno' => 1))
                       ->will($this->returnValue($notaComponente));

    return $notaComponenteMock;
  }

  /**
   * @see Boletim_Common#_setUpNotaComponenteMediaDataMapper()
   */
  protected function _setUpNotaComponenteMediaDataMapper()
  {
    $notaComponenteMedia = array(new Avaliacao_Model_NotaComponenteMedia(array(
      'id' => 1,
      'notaAluno' => 1,
      'componenteCurricular' => 1,
      'media' => (7.5 + 6.5) / 4,
      'mediaArredondada' => 3.5,
      'etapa' => 2
    )));

    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->with(array(), array('notaAluno' => 1))
         ->will($this->returnValue($notaComponenteMedia));

    return $mock;
  }

  /**
   * @see Boletim_Common#_setUpFaltaAlunoDataMapperMock()
   */
  protected function _setUpFaltaAlunoDataMapperMock()
  {
    $faltaAluno = $this->_setUpFaltaAluno();

    $faltaAlunoMock = $this->getCleanMock('Avaliacao_Model_FaltaAlunoDataMapper');
    $faltaAlunoMock->expects($this->any())
                  ->method('findAll')
                  ->with(array(), array('matricula' => 1))
                  ->will($this->onConsecutiveCalls(array(), array($faltaAluno)));

    $faltaAlunoMock->expects($this->any())
                   ->method('save')
                   ->will($this->returnValue(TRUE));

    return $faltaAlunoMock;
  }

  /**
   * @see Boletim_Common#_setUpFaltaAluno()
   */
  protected function _setUpFaltaAluno()
  {
    $faltaAluno = new Avaliacao_Model_FaltaAluno(array(
      'id' => 1,
      'matricula' => 1,
      'tipoFalta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE
    ));

    // Guarda na instância
    $this->_faltaAlunoExpected = $faltaAluno;

    return $faltaAluno;
  }

  /**
   * @see Boletim_Common#_setUpFaltaAbstractDataMapperMock()
   */
  protected function _setUpFaltaAbstractDataMapperMock()
  {
      // Retorno para mock de falta componentes
    $faltaComponente = array(new Avaliacao_Model_FaltaComponente(array(
      'id' => 1,
      'faltaAluno' => 1,
      'componenteCurricular' => 1,
      'quantidade' => 4,
      'etapa' => 2
    )));

    // Configura expectativas do mock
    $faltaComponenteMock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');
    $faltaComponenteMock->expects($this->at(0))
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltaComponente));

    return $faltaComponenteMock;
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testInstanciaLancaExcecaoCasoNumeroDeMatriculaNaoSejaInformado()
  {
    $service = new Avaliacao_Service_Boletim();
  }

  public function testInstanciaRegraDeAvaliacaoAtravesDeUmNumeroDeMatricula()
  {
    $this->assertType('RegraAvaliacao_Model_Regra', $this->_service->getRegra());
  }

  public function testInstanciaComponenteCurriculaAtravesDeUmNumeroDeMatricula()
  {
    $this->assertType('TabelaArredondamento_Model_Tabela', $this->_service->getTabelaArredondamento());
  }

  public function testInstanciaDeNotaEAdicionadaApenasUmaVez()
  {
    $data = array(
      new Avaliacao_Model_NotaComponente(
        array('componenteCurricular' => 1, 'nota' => 5)
      ),
      new Avaliacao_Model_NotaComponente(
        array('componenteCurricular' => 3, 'nota' => 7.25)
      )
    );

    $this->_service->addNota($data[0])
                   ->addNota($data[1])
                   ->addNota($data[0]);

    $notas = $this->_service->getNotas();
    $notas = array(
      array_shift($notas),
      array_shift($notas)
    );

    $this->assertEquals(2, count($notas));
    $this->assertEquals($data[0], $notas[0]);
    $this->assertEquals($data[1], $notas[1]);

    $data[0]->nota = 6;
    $this->_service->addNota($data[0]);

    $this->assertEquals(2, count($this->_service->getNotas()));
  }

  public function testValidacaoDeNotaNoBoletim()
  {
    // O aluno de matrícula "1" terá uma nota em matemática e nenhuma em ciências
    $expected = array(
      new Avaliacao_Model_NotaComponente(array('nota' => 6, 'componenteCurricular' => 1, 'etapa' => 1))
    );

    // Não existe etapa 7, a máxima é 4 e Rc (recuperação).
    $nota = new Avaliacao_Model_NotaComponente(array('componenteCurricular' => 1, 'nota' => 5.75, 'etapa' => 7));
    $this->_service->addNota($nota);

    // O aluno não cursa o componente 9
    $nota = new Avaliacao_Model_NotaComponente(array('componenteCurricular' => 9, 'nota' => 5.75, 'etapa' => 7));
    $this->_service->addNota($nota);

    // Notas validáveis
    $validatable = $this->_service->getNotas();

    // Inválido, irá atribuir a maior etapa possível
    $nota = array_shift($validatable);
    $this->assertEquals(3, $nota->etapa);


    // Inválido, irá atribuir a maior etapa possível
    $nota = array_shift($validatable);
    $this->assertEquals(1, $nota->etapa);
  }

  public function testValidacaoDeFaltaPorComponenteNoBoletim()
  {
    // Não existe etapa 7, a máxima é 4 e Rc (recuperação).
    $falta = new Avaliacao_Model_FaltaComponente(array('componenteCurricular' => 1, 'quantidade' => 5, 'etapa' => 7));
    $this->_service->addFalta($falta);

    // O aluno não cursa o componente 9
    $falta = new Avaliacao_Model_FaltaComponente(array('componenteCurricular' => 9, 'quantidade' => 5, 'etapa' => 7));
    $this->_service->addFalta($falta);

    // Faltas validáveis
    $validatable = $this->_service->getFaltas();

    // Inválido, irá atribuir a maior etapa possível
    $falta = array_shift($validatable);
    $this->assertEquals(3, $falta->etapa);

    // Inválido, irá atribuir a maior etapa possível
    $falta = array_shift($validatable);
    $this->assertEquals(1, $falta->etapa);
  }

  public function testArredondamentoDeNota()
  {
    $nota = new Avaliacao_Model_NotaComponente(array('nota' => 7.5));
    $this->assertEquals(7, $this->_service->arredondaNota($nota));

    $nota = new Avaliacao_Model_NotaComponente(array('nota' => 8));
    $this->assertEquals(7, $this->_service->arredondaNota($nota));
  }

  public function testSalvaAsNotasNoBoletim()
  {
    $data = array(
      new Avaliacao_Model_NotaComponente(
        array('componenteCurricular' => 1, 'nota' => 5)
      ),
      new Avaliacao_Model_NotaComponente(
        array('componenteCurricular' => 3, 'nota' => 7.25, 'etapa' => 1)
      )
    );

    // Adiciona mocks na instância
    $this->_service->addNotas($data);

    // Configura o mock de NotaComponente, que irá persistir objetos
    // Avaliacao_Model_NotaComponente
    $notas = $this->_service->getNotas();
    $toSave[0] = array_shift($notas);
    $toSave[1] = array_shift($notas);
    $toSave[0]->notaAluno = $this->_notaAlunoExpected;
    $toSave[1]->notaAluno = $this->_notaAlunoExpected;

    // Usa o mesmo mock que foi criado em setUp()
    $notaComponenteMock = $this->_service->getNotaComponenteDataMapper();

    // Depois de salvar as notas, retornaremos um conjunto completo, inclusive
    // com a nota da primeira etapa que não tem em setUp().
    $notasComponentes = array(
      new Avaliacao_Model_NotaComponente(array(
        'id' => 1,
        'notaAluno' => 1,
        'componenteCurricular' => 1,
        'nota' => 6.5,
        'notaArredondada' => 6,
        'etapa' => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id' => 2,
        'notaAluno' => 1,
        'componenteCurricular' => 1,
        'nota' => 7.5,
        'notaArredondada' => 7,
        'etapa' => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id' => 3,
        'notaAluno' => 1,
        'componenteCurricular' => 1,
        'nota' => 5,
        'notaArredondada' => 4,
        'etapa' => 3
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id' => 4,
        'notaAluno' => 1,
        'componenteCurricular' => 3,
        'nota' => 7.25,
        'notaArredondada' => 7,
        'etapa' => 1
      ))
    );

    $notaComponenteMock->expects($this->at(0))
                       ->method('save')
                       ->with($toSave[0]);

    $notaComponenteMock->expects($this->at(1))
                       ->method('save')
                       ->with($toSave[1]);

    $notaComponenteMock->expects($this->at(2))
                       ->method('findAll')
                       ->with(array(), array('notaAluno' => 1))
                       ->will($this->returnValue($notasComponentes));

    // Valores de retorno para NotaComponenteMedia
    $notasComponentesMedia = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1,
        'componenteCurricular' => 1,
        'media' => 3.5,
        'mediaArredondada' => 3
      ))
    );

    $notaComponenteMediaMock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');
    $notaComponenteMediaMock->expects($this->at(0))
                            ->method('find')
                            ->with(array(1, 1))
                            ->will($this->returnValue($notasComponentesMedia[0]));

    $notaComponenteMediaMock->expects($this->at(2))
                            ->method('find')
                            ->with(array(1, 3))
                            ->will($this->returnValue(NULL));

    $notaComponenteMediaMock->expects($this->at(1))
                            ->method('save');

    $notaComponenteMediaMock->expects($this->at(3))
                            ->method('save');

    // Atribui mock a instância
    $this->_service->setNotaComponenteDataMapper($notaComponenteMock);
    $this->_service->setNotaComponenteMediaDataMapper($notaComponenteMediaMock);

    // Salva as notas
    $this->_service->saveNotas();

    // Verifica a etapa das notas
    $notas = $this->_service->getNotas();

    $nota1 = array_shift($notas);
    $nota2 = array_shift($notas);

    // Quantidade de etapas
    $this->assertEquals(3, $nota1->etapa);
    $this->assertEquals(1, $nota2->etapa);
  }

  public function testSalvaAsFaltasNoBoletim()
  {
    $data = array(
      new Avaliacao_Model_FaltaComponente(array(
        'componenteCurricular' => 1, 'quantidade' => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'componenteCurricular' => 3, 'quantidade' => 4
      ))
    );

    // Adiciona faltas
    $this->_service->addFaltas($data);

    // Adiciona a instância de falta aluno
    $faltas = $this->_service->getFaltas();
    $toSave = array();
    $toSave[0] = array_shift($faltas);
    $toSave[1] = array_shift($faltas);

    $toSave[0]->faltaAluno = $this->_faltaAlunoExpected;
    $toSave[1]->faltaAluno = $this->_faltaAlunoExpected;

    // Usa o mesmo mock que foi criado em setUp()
    $faltaComponenteMock = $this->_service->getFaltaAbstractDataMapper();

    $faltaComponenteMock->expects($this->at(0))
                        ->method('save')
                        ->with($toSave[0]);

    $faltaComponenteMock->expects($this->at(1))
                        ->method('save')
                        ->with($toSave[1]);

    // Salva as faltas
    $this->_service->saveFaltas();
  }

  public function testSituacaoAlunoComponentesCurricularesEmExame()
  {
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

    $returnValue = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 1, 'media' => 5.65, 'mediaArredondada' => 5.5, 'etapa' => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 3, 'media' => 6, 'mediaArredondada' => 6, 'etapa' => 'Rc'
      ))
    );

    $mediaMock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');
    $mediaMock->expects($this->once())
              ->method('findAll')
              ->with(array(), array('notaAluno' => 1))
              ->will($this->returnValue($returnValue));

    $this->_service->setNotaComponenteMediaDataMapper($mediaMock);
    $this->assertEquals($expected, $this->_service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoAlunoComponentesCurricularesReprovado()
  {
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::REPROVADO;
    $expected->componentesCurriculares = array();
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::REPROVADO;
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::REPROVADO;

    $returnValue = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 1, 'media' => 5.65, 'mediaArredondada' => 5.5, 'etapa' => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 3, 'media' => 5.5, 'mediaArredondada' => 5.5, 'etapa' => 'Rc'
      ))
    );

    $mediaMock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');
    $mediaMock->expects($this->once())
              ->method('findAll')
              ->with(array(), array('notaAluno' => 1))
              ->will($this->returnValue($returnValue));

    $this->_service->setNotaComponenteMediaDataMapper($mediaMock);
    $this->assertEquals($expected, $this->_service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoAlunoComponentesCurricularesEmAndamento()
  {
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares = array();
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $returnValue = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 1, 'media' => 5.65, 'mediaArredondada' => 5.5, 'etapa' => 3
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 3, 'media' => 6, 'mediaArredondada' => 6, 'etapa' => 4
      ))
    );

    $mediaMock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');
    $mediaMock->expects($this->once())
              ->method('findAll')
              ->with(array(), array('notaAluno' => 1))
              ->will($this->returnValue($returnValue));

    $this->_service->setNotaComponenteMediaDataMapper($mediaMock);
    $this->assertEquals($expected, $this->_service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoAlunoComponentesCurricularesAprovado()
  {
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares = array();
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $returnValue = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 1, 'media' => 7.65, 'mediaArredondada' => 7.5, 'etapa' => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => 1, 'componenteCurricular' => 3, 'media' => 6, 'mediaArredondada' => 6, 'etapa' => 4
      ))
    );

    $mediaMock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');
    $mediaMock->expects($this->once())
              ->method('findAll')
              ->with(array(), array('notaAluno' => 1))
              ->will($this->returnValue($returnValue));

    $this->_service->setNotaComponenteMediaDataMapper($mediaMock);
    $this->assertEquals($expected, $this->_service->getSituacaoComponentesCurriculares());
  }

/**
   * @depends testSalvaAsFaltasNoBoletim
   * @see Boletim_Common#_configuraDadosDisciplina():255 (carga horária do
   *   componente de id 3 - Ciências)
   */
  public function testSituacaoAlunoFaltaComponenteCurricularEmAndamento()
  {
    $faltasComponentes = array(
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 1,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 0,
        'etapa' => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 2,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 7,
        'etapa' => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 3,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 1,
        'etapa' => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 4,
        'faltaAluno' => 1,
        'componenteCurricular' => 3,
        'quantidade' => 4,
        'etapa' => 1
      ))
    );

    $faltaComponenteMock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');
    $faltaComponenteMock->expects($this->once())
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltasComponentes));

    $this->_service->setFaltaAbstractDataMapper($faltaComponenteMock);

    // Objeto de retorno esperado
    $expected = new stdClass();
    $expected->situacao                 = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->tipoFalta                = RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;
    $expected->cargaHoraria             = 800;
    $expected->cursoHoraFalta           = (50 / 60);
    $expected->totalFaltas              = 12;
    $expected->horasFaltas              = (12 * $expected->cursoHoraFalta);
    $expected->porcentagemFalta         = (($expected->horasFaltas / $expected->cargaHoraria) * 100);
    $expected->porcentagemPresenca      = 100 - $expected->porcentagemFalta;
    $expected->porcentagemPresencaRegra = 75;
    $expected->componentesCurriculares  = array();
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[3] = new stdClass();

    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares[1]->horasFaltas = (8 * $expected->cursoHoraFalta);
    $expected->componentesCurriculares[1]->porcentagemFalta = ((8 * $expected->cursoHoraFalta) / 100) * 100;
    $expected->componentesCurriculares[1]->porcentagemPresenca = 100 - (((8 * $expected->cursoHoraFalta) / 100) * 100);

    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares[3]->horasFaltas = (4 * $expected->cursoHoraFalta);
    $expected->componentesCurriculares[3]->porcentagemFalta = ((4 * $expected->cursoHoraFalta) / 70) * 100;
    $expected->componentesCurriculares[3]->porcentagemPresenca = 100 - (((4 * $expected->cursoHoraFalta) / 70) * 100);

    $presenca = $this->_service->getSituacaoFaltas();
    $this->assertEquals($expected, $presenca);
  }

  /**
   * @depends testSalvaAsFaltasNoBoletim
   * @see Boletim_Common#_configuraDadosDisciplina():255 (carga horária do
   *   componente de id 3 - Ciências)
   */
  public function testSituacaoAlunoFaltaComponenteCurricularAprovado()
  {
    $faltasComponentes = array(
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 1,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 0,
        'etapa' => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 2,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 7,
        'etapa' => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 3,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 1,
        'etapa' => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 4,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 4,
        'etapa' => 4
      ))
    );

    $faltaComponenteMock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');
    $faltaComponenteMock->expects($this->once())
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltasComponentes));

    $this->_service->setFaltaAbstractDataMapper($faltaComponenteMock);

    // Objeto de retorno esperado
    $expected = new stdClass();
    $expected->situacao                 = App_Model_MatriculaSituacao::APROVADO;
    $expected->tipoFalta                = RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;
    $expected->cargaHoraria             = 800;
    $expected->cursoHoraFalta           = (50 / 60);
    $expected->totalFaltas              = 12;
    $expected->horasFaltas              = (12 * $expected->cursoHoraFalta);
    $expected->porcentagemFalta         = (($expected->horasFaltas / $expected->cargaHoraria) * 100);
    $expected->porcentagemPresenca      = 100 - $expected->porcentagemFalta;
    $expected->porcentagemPresencaRegra = 75;
    $expected->componentesCurriculares  = array();
    $expected->componentesCurriculares[1] = new stdClass();

    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[1]->horasFaltas = (12 * $expected->cursoHoraFalta);
    $expected->componentesCurriculares[1]->porcentagemFalta = ((12 * $expected->cursoHoraFalta) / 100) * 100;
    $expected->componentesCurriculares[1]->porcentagemPresenca = 100 - (((12 * $expected->cursoHoraFalta) / 100) * 100);

    $presenca = $this->_service->getSituacaoFaltas();
    $this->assertEquals($expected, $presenca);
  }

/**
   * @depends testSalvaAsFaltasNoBoletim
   * @see Boletim_Common#_configuraDadosDisciplina():255 (carga horária do
   *   componente de id 3 - Ciências)
   */
  public function testSituacaoAlunoFaltaComponenteCurricularReprovado()
  {
    $faltasComponentes = array(
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 1,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 125,
        'etapa' => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 2,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 125,
        'etapa' => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 3,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 125,
        'etapa' => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id' => 4,
        'faltaAluno' => 1,
        'componenteCurricular' => 1,
        'quantidade' => 125,
        'etapa' => 4
      ))
    );

    $faltaComponenteMock = $this->getCleanMock('Avaliacao_Model_FaltaComponenteDataMapper');
    $faltaComponenteMock->expects($this->once())
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltasComponentes));

    $this->_service->setFaltaAbstractDataMapper($faltaComponenteMock);

    // Objeto de retorno esperado
    $expected = new stdClass();
    $expected->situacao                 = App_Model_MatriculaSituacao::REPROVADO;
    $expected->tipoFalta                = RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;
    $expected->cargaHoraria             = 800;
    $expected->cursoHoraFalta           = (50 / 60);
    $expected->totalFaltas              = 500;
    $expected->horasFaltas              = (500 * $expected->cursoHoraFalta);
    $expected->porcentagemFalta         = (($expected->horasFaltas / $expected->cargaHoraria) * 100);
    $expected->porcentagemPresenca      = 100 - $expected->porcentagemFalta;
    $expected->porcentagemPresencaRegra = 75;
    $expected->componentesCurriculares  = array();
    $expected->componentesCurriculares[1] = new stdClass();

    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::REPROVADO;
    $expected->componentesCurriculares[1]->horasFaltas = (500 * $expected->cursoHoraFalta);
    $expected->componentesCurriculares[1]->porcentagemFalta = ((500 * $expected->cursoHoraFalta) / 100) * 100;
    $expected->componentesCurriculares[1]->porcentagemPresenca = 100 - (((500 * $expected->cursoHoraFalta) / 100) * 100);

    $presenca = $this->_service->getSituacaoFaltas();
    $this->assertEquals($expected, $presenca);
  }

  public function testSituacaoAluno()
  {
    $notaSituacoes = array(
      1 => App_Model_MatriculaSituacao::APROVADO,
      2 => App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
      3 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
      4 => App_Model_MatriculaSituacao::EM_EXAME,
      5 => App_Model_MatriculaSituacao::REPROVADO
    );

    $faltaSituacoes = array(
      1 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
      2 => App_Model_MatriculaSituacao::APROVADO,
      3 => App_Model_MatriculaSituacao::REPROVADO
    );

    // Possibilidades
    $expected = array(
      1 => array(
        1 => array(FALSE, TRUE, FALSE, FALSE),
        2 => array(TRUE, FALSE, FALSE, FALSE),
        3 => array(FALSE, FALSE, TRUE, FALSE)
      ),
      2 => array(
        1 => array(FALSE, TRUE, FALSE, TRUE),
        2 => array(TRUE, FALSE, FALSE, TRUE),
        3 => array(FALSE, FALSE, TRUE, TRUE)
      ),
      3 => array(
        1 => array(FALSE, TRUE, FALSE, FALSE),
        2 => array(FALSE, TRUE, FALSE, FALSE),
        3 => array(FALSE, TRUE, TRUE, FALSE)
      ),
      4 => array(
        1 => array(FALSE, TRUE, FALSE, TRUE),
        2 => array(FALSE, TRUE, FALSE, TRUE),
        3 => array(FALSE, TRUE, TRUE, TRUE)
      ),
      5 => array(
        1 => array(FALSE, TRUE, FALSE, TRUE),
        2 => array(FALSE, FALSE, FALSE, TRUE),
        3 => array(FALSE, FALSE, TRUE, TRUE)
      )
    );

    foreach ($notaSituacoes as $i => $notaSituacao) {
      $nota = new stdClass();
      $nota->situacao = $notaSituacao;

      foreach ($faltaSituacoes as $ii => $faltaSituacao) {
        $service = $this->setExcludedMethods(array('getSituacaoAluno'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

        $falta = new stdClass();
        $falta->situacao = $faltaSituacao;

        $service->expects($this->once())
                ->method('getSituacaoComponentesCurriculares')
                ->will($this->returnValue($nota));

        $service->expects($this->once())
                ->method('getSituacaoFaltas')
                ->will($this->returnValue($falta));

        // Testa
        $situacao = $service->getSituacaoAluno();

        $this->assertEquals($expected[$i][$ii][0], $situacao->aprovado, "Aprovado, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][1], $situacao->andamento, "Andamento, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][2], $situacao->retidoFalta, "Retido por falta, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][3], $situacao->recuperacao, "Recuperação, caso $i - $ii");
      }
    }
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverLancaExcecaoComSituacaoEmAndamento()
  {
    $situacao = new stdClass();
    $situacao->aprovado = FALSE;
    $situacao->andamento = TRUE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->promover();
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverMatriculaJaPromovidaLancaExcecao()
  {
    $situacao = new stdClass();
    $situacao->aprovado  = FALSE;
    $situacao->andamento = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->once())
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(1));

    $service->promover();
  }

  public function testPromoverComProgressaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = TRUE;
    $situacao->recuperacao = TRUE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::CONTINUADA)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->any())
            ->method('getOption')
            ->will($this->onConsecutiveCalls(3, 1, 1));

    $service->expects($this->once())
            ->method('_updateMatricula')
            ->with(1, 1, TRUE);

    $service->promover();
  }

  public function testPromoverComProgressaoNaoContinuadaMediaPresencaAprovado()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = FALSE;
    $situacao->recuperacao = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->any())
            ->method('getOption')
            ->will($this->onConsecutiveCalls(3, 1, 1));

    $service->expects($this->once())
            ->method('_updateMatricula')
            ->with(1, 1, TRUE);

    $service->promover();
  }

  public function testPromoverComProgressaoNaoContinuadaMediaPresencaReprovado()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = TRUE;
    $situacao->recuperacao = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->any())
            ->method('getOption')
            ->will($this->onConsecutiveCalls(3, 1, 1));

    $service->expects($this->once())
            ->method('_updateMatricula')
            ->with(1, 1, FALSE);

    $service->promover();
  }

  public function testPromoverComProgressaoNaoContinuadaMediaAprovado()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = TRUE;
    $situacao->recuperacao = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_SOMENTE_MEDIA)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->any())
            ->method('getOption')
            ->will($this->onConsecutiveCalls(3, 1, 1));

    $service->expects($this->once())
            ->method('_updateMatricula')
            ->with(1, 1, TRUE);

    $service->promover();
  }

  public function testPromoverComProgressaoNaoContinuadaMediaReprovado()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = FALSE;
    $situacao->recuperacao = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_SOMENTE_MEDIA)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->any())
            ->method('getOption')
            ->will($this->onConsecutiveCalls(3, 1, 1));

    $service->expects($this->once())
            ->method('_updateMatricula')
            ->with(1, 1, FALSE);

    $service->promover();
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverComProgressaoNaoContinuadaManualNaoConfirmadaLancaExcecao()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = FALSE;
    $situacao->recuperacao = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->once())
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(3));

    $service->promover();
  }

  public function testPromoverComProgressaoNaoContinuadaManualConfirmada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = FALSE;
    $situacao->retidoFalta = FALSE;
    $situacao->recuperacao = FALSE;

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->once())
            ->method('getRegra')
            ->will($this->returnValue(new RegraAvaliacao_Model_Regra(
              array('tipoProgressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL)
            )));

    $service->expects($this->once())
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->any())
            ->method('getOption')
            ->will($this->onConsecutiveCalls(3, 1, 1));

    $service->expects($this->once())
            ->method('_updateMatricula')
            ->with(1, 1, TRUE);

    $service->promover(TRUE);
  }
}