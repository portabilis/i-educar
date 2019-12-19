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

require_once __DIR__.'/FaltaSituacaoCommon.php';

/**
 * Avaliacao_Service_FaltaGeralSituacaoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaGeralSituacaoTest extends Avaliacao_Service_FaltaSituacaoCommon
{
  protected function setUp(): void
  {
    $this->_setRegraOption('tipoPresenca', RegraAvaliacao_Model_TipoPresenca::GERAL);
    parent::setUp();
  }

  public function testSituacaoFaltasEmAndamento()
  {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, array());

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->porcentagemPresenca = 100;
    $expected->diasLetivos         = 960;

    $service = $this->_getServiceInstance();
    $actual = $service->getSituacaoFaltas();

    $this->assertEquals($expected, $actual);
  }

  public function testSituacaoFaltasAprovado()
  {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'quantidade' => 5,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 3,
        'quantidade' => 5,
        'etapa'      => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 4,
        'quantidade' => 5,
        'etapa'      => 4
      )),
    );

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::APROVADO;

    $expected->totalFaltas         = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas         = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta    = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;
    $expected->diasLetivos         = 960;

    $service = $this->_getServiceInstance();
    $actual = $service->getSituacaoFaltas();

    $this->assertEquals($expected, $actual);
  }

  public function testSituacaoFaltasReprovado()
  {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'quantidade' => 180,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'quantidade' => 180,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 3,
        'quantidade' => 180,
        'etapa'      => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 4,
        'quantidade' => 180,
        'etapa'      => 4
      )),
    );

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::REPROVADO;

    $expected->totalFaltas         = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas         = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta    = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;
    $expected->diasLetivos         = 960;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }
}
