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

require_once __DIR__.'/TestCommon.php';

/**
 * Avaliacao_Service_NotaSituacaoCommon abstract class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Service_NotaSituacaoCommon extends Avaliacao_Service_TestCommon
{
  protected function _setUpNotaComponenteMediaDataMapperMock(
    Avaliacao_Model_NotaAluno $notaAluno, array $medias)
  {
    // Configura mock para notas
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

    $mock->expects($this->any())
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id))
         ->will($this->returnValue($medias));

    $this->_setNotaComponenteMediaDataMapperMock($mock);
  }

  /**
   * Um componente em exame, já que por padrão a regra de avaliação define uma
   * fórmula de recuperação. Quatro médias lançadas, 3 aprovadas.
   */
  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosAprovados()
  {
      $this->markTestSkipped();
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();

    // Matemática estará em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma média lançada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosDoisAprovadosUmAndamento()
  {
      $this->markTestSkipped();
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares = array();

    // Matemática estará em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma média lançada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 5.75,
        'mediaArredondada'     => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosUmAprovadoAposExameEDoisAprovados()
  {
      $this->markTestSkipped();
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();

    // Matemática estará em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma média lançada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6.5,
        'mediaArredondada'     => 6,
        'etapa'                => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosUmAprovadoAposExameUmReprovadoEOutroAprovado()
  {
      $this->markTestSkipped();
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();

    // Matemática estará em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::REPROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma média lançada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6.5,
        'mediaArredondada'     => 6,
        'etapa'                => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

/**
   * Um componente reprovado, com uma regra sem recuperação. Quatro médias
   * lançadas, 3 aprovadas.
   */
  public function testSituacaoComponentesCurricularesUmComponenteLancadoReprovadoUmComponenteAbaixoDaMedia()
  {
      $this->markTestSkipped();
    $this->_setRegraOption('formulaRecuperacao', NULL);

    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::REPROVADO;
    $expected->componentesCurriculares = array();

    // Matemática estará em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::REPROVADO;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma média lançada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }
}