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

require_once 'Avaliacao/_tests/Boletim/NotaNumerica_FaltaComponenteTest.php';
require_once 'Avaliacao/Model/FaltaGeralDataMapper.php';

/**
 * NotaNumerica_FaltaGeral class.
 *
 * Realiza testes no service Avaliacao_Service_Boletim para casos onde a regra
 * de avaliação contabiliza faltas no geral.
 *
 * Subclassifica classe de teste para casos de faltas por componentes,
 * desativando ou modificando o comportamento por herança.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Boletim_NotaNumerica_FaltaGeralTest extends Boletim_NotaNumerica_FaltaComponenteTest
{
  /**
   * @see Boletim_NotaNumerica_FaltaComponenteTest#_setUpFaltaAluno()
   */
  protected function _setUpFaltaAluno()
  {
    $faltaAluno = new Avaliacao_Model_FaltaAluno(array(
      'id' => 1,
      'matricula' => 1,
      'tipoFalta' => RegraAvaliacao_Model_TipoPresenca::GERAL
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
    $faltaComponente = array(new Avaliacao_Model_FaltaGeral(array(
      'id' => 1,
      'faltaAluno' => 1,
      'quantidade' => 4,
      'etapa' => 2
    )));

    // Configura expectativas do mock
    $faltaComponenteMock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');
    $faltaComponenteMock->expects($this->at(0))
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltaComponente));

    return $faltaComponenteMock;
  }

  public function testInstanciaLancaExcecaoCasoNumeroDeMatriculaNaoSejaInformado()
  {
  }

  public function testInstanciaRegraDeAvaliacaoAtravesDeUmNumeroDeMatricula()
  {
  }

  public function testInstanciaComponenteCurriculaAtravesDeUmNumeroDeMatricula()
  {
  }

  public function testInstanciaDeNotaEAdicionadaApenasUmaVez()
  {
  }

  public function testValidacaoDeNotaNoBoletim()
  {
  }

  public function testValidacaoDeFaltaPorComponenteNoBoletim()
  {
  }

  public function testValidacaoDeFaltaGeralNoBoletim()
  {
    // Não existe etapa 7, a máxima é 4 e Rc (recuperação).
    $falta = new Avaliacao_Model_FaltaGeral(array('quantidade' => 5, 'etapa' => 7));
    $this->_service->addFalta($falta);

    // O aluno não cursa o componente 9
    $falta = new Avaliacao_Model_FaltaGeral(array('quantidade' => 5, 'etapa' => 7));
    $this->_service->addFalta($falta);

    // Faltas validáveis
    $validatable = $this->_service->getFaltas();

    // Inválido, irá atribuir a maior etapa possível
    $falta = array_shift($validatable);
    $this->assertEquals(3, $falta->etapa);

    // Inválido, irá atribuir a maior etapa possível
    $falta = array_shift($validatable);
    $this->assertEquals(3, $falta->etapa, 'Segunda');
  }

  public function testArredondamentoDeNota()
  {
  }

  public function testSalvaAsNotasNoBoletim()
  {
  }

  public function testSalvaAsFaltasNoBoletim()
  {
    $data = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 4
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
  }

  public function testSituacaoAlunoComponentesCurricularesReprovado()
  {
  }

  public function testSituacaoAlunoComponentesCurricularesEmAndamento()
  {
  }

  public function testSituacaoAlunoComponentesCurricularesAprovado()
  {
  }

  public function testSituacaoAlunoFaltaComponenteCurricularReprovado()
  {
  }

  public function testSituacaoAlunoFaltaComponenteCurricularEmAndamento()
  {
  }

  public function testSituacaoAlunoFaltaComponenteCurricularAprovado()
  {
  }

  /**
   * @depends testSalvaAsFaltasNoBoletim
   */
  public function testSituacaoAlunoFaltaGeralEmAndamento()
  {
    $faltasGerais = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 1,
        'faltaAluno' => 1,
        'quantidade' => 1,
        'etapa' => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 2,
        'faltaAluno' => 1,
        'quantidade' => 4,
        'etapa' => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 3,
        'faltaAluno' => 1,
        'quantidade' => 5,
        'etapa' => 3
      ))
    );

    $faltaGeralMock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');
    $faltaGeralMock->expects($this->once())
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltasGerais));

    $this->_service->setFaltaAbstractDataMapper($faltaGeralMock);

    // Objeto de retorno esperado
    $expected = new stdClass();
    $expected->situacao                 = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->tipoFalta                = RegraAvaliacao_Model_TipoPresenca::GERAL;
    $expected->cargaHoraria             = 800;
    $expected->cursoHoraFalta           = (50 / 60);
    $expected->totalFaltas              = 10;
    $expected->horasFaltas              = (10 * $expected->cursoHoraFalta);
    $expected->porcentagemFalta         = (($expected->horasFaltas / $expected->cargaHoraria) * 100);
    $expected->porcentagemPresenca      = 100 - $expected->porcentagemFalta;
    $expected->porcentagemPresencaRegra = 75;
    $expected->componentesCurriculares  = array();

    $presenca = $this->_service->getSituacaoFaltas();
    $this->assertEquals($expected, $presenca);
  }

  /**
   * @depends testSalvaAsFaltasNoBoletim
   */
  public function testSituacaoAlunoFaltaGeralReprovado()
  {
    $faltasGerais = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 1,
        'faltaAluno' => 1,
        'quantidade' => 125,
        'etapa' => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 2,
        'faltaAluno' => 1,
        'quantidade' => 125,
        'etapa' => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 3,
        'faltaAluno' => 1,
        'quantidade' => 125,
        'etapa' => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 4,
        'faltaAluno' => 1,
        'quantidade' => 125,
        'etapa' => 4
      ))
    );

    $faltaGeralMock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');
    $faltaGeralMock->expects($this->once())
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltasGerais));

    $this->_service->setFaltaAbstractDataMapper($faltaGeralMock);

    // Objeto de retorno esperado
    $expected = new stdClass();
    $expected->situacao                 = App_Model_MatriculaSituacao::REPROVADO;
    $expected->tipoFalta                = RegraAvaliacao_Model_TipoPresenca::GERAL;
    $expected->cargaHoraria             = 800;
    $expected->cursoHoraFalta           = (50 / 60);
    $expected->totalFaltas              = 500;
    $expected->horasFaltas              = (500 * $expected->cursoHoraFalta);
    $expected->porcentagemFalta         = (($expected->horasFaltas / $expected->cargaHoraria) * 100);
    $expected->porcentagemPresenca      = 100 - $expected->porcentagemFalta;
    $expected->porcentagemPresencaRegra = 75;
    $expected->componentesCurriculares  = array();

    $presenca = $this->_service->getSituacaoFaltas();
    $this->assertEquals($expected, $presenca);
  }

  /**
   * @depends testSalvaAsFaltasNoBoletim
   */
  public function testSituacaoAlunoFaltaGeralAprovado()
  {
    $faltasGerais = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 1,
        'faltaAluno' => 1,
        'quantidade' => 1,
        'etapa' => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 2,
        'faltaAluno' => 1,
        'quantidade' => 4,
        'etapa' => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 3,
        'faltaAluno' => 1,
        'quantidade' => 5,
        'etapa' => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id' => 4,
        'faltaAluno' => 1,
        'quantidade' => 0,
        'etapa' => 4
      ))
    );

    $faltaGeralMock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');
    $faltaGeralMock->expects($this->once())
                        ->method('findAll')
                        ->with(array(), array('faltaAluno' => 1))
                        ->will($this->returnValue($faltasGerais));

    $this->_service->setFaltaAbstractDataMapper($faltaGeralMock);

    // Objeto de retorno esperado
    $expected = new stdClass();
    $expected->situacao                 = App_Model_MatriculaSituacao::APROVADO;
    $expected->tipoFalta                = RegraAvaliacao_Model_TipoPresenca::GERAL;
    $expected->cargaHoraria             = 800;
    $expected->cursoHoraFalta           = (50 / 60);
    $expected->totalFaltas              = 10;
    $expected->horasFaltas              = (10 * $expected->cursoHoraFalta);
    $expected->porcentagemFalta         = (($expected->horasFaltas / $expected->cargaHoraria) * 100);
    $expected->porcentagemPresenca      = 100 - $expected->porcentagemFalta;
    $expected->porcentagemPresencaRegra = 75;
    $expected->componentesCurriculares  = array();

    $presenca = $this->_service->getSituacaoFaltas();
    $this->assertEquals($expected, $presenca);
  }

  public function testSituacaoAluno()
  {
  }

  public function testPromoverLancaExcecaoComSituacaoEmAndamento()
  {
  }

  public function testPromoverComProgressaoContinuada()
  {
  }

  public function testPromoverComProgressaoNaoContinuadaMediaPresencaAprovado()
  {
  }

  public function testPromoverComProgressaoNaoContinuadaMediaPresencaReprovado()
  {
  }

  public function testPromoverComProgressaoNaoContinuadaMediaAprovado()
  {
  }

  public function testPromoverComProgressaoNaoContinuadaMediaReprovado()
  {
  }

  public function testPromoverComProgressaoNaoContinuadaManualNaoConfirmadaLancaExcecao()
  {
  }

  public function testPromoverComProgressaoNaoContinuadaManualConfirmada()
  {
  }
}