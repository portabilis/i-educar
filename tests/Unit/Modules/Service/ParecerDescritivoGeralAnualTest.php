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

require_once __DIR__.'/ParecerDescritivoCommon.php';

/**
 * Avaliacao_Service_ParecerDescritivoGeralAnualTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_ParecerDescritivoGeralAnualTest extends Avaliacao_Service_ParecerDescritivoCommon
{
  protected function setUp(): void
  {
    $this->_setRegraOption('parecerDescritivo', RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL);
    parent::setUp();
  }

  protected function _getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
  {
    return new Avaliacao_Model_ParecerDescritivoGeral(array(
      'parecer' => 'Ok.'
    ));
  }

  protected function _getTestAdicionaParecerNoBoletim()
  {
    return new Avaliacao_Model_ParecerDescritivoGeral(array(
      'parecer' => 'N/D.'
    ));
  }

  protected function _getTestSalvarPareceresNoBoletimInstanciasDePareceres()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoGeral(array(
        'parecer' => 'N/D.',
        'etapa'   => 'An'
      ))
    );
  }

  protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoGeral(array(
        'parecer' => 'N/D.',
        'etapa'   => 'An'
      ))
    );
  }

  protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoGeral(array(
        'id'      => 1,
        'parecer' => 'N/D.',
        'etapa'   => 'An'
      ))
    );
  }

  protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoGeral(array(
        'parecer' => 'N/D.'
      ))
    );
  }

  protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoGeral(array(
        'id'      => 1,
        'parecer' => 'N/D.',
        'etapa'   => 'An'
      ))
    );
  }

  protected function _testAdicionaParecerNoBoletimVerificaValidadores(Avaliacao_Model_ParecerDescritivoAbstract $parecer)
  {
      $this->markTestSkipped();
    $this->assertEquals(1, $parecer->etapa);
    $this->assertEquals('N/D.', $parecer->parecer);

    $validators = $parecer->getValidatorCollection();

    $this->assertEquals($this->_getEtapasPossiveisParecer(), $validators['etapa']->getOption('choices'));
    $this->assertFalse(isset($validators['componenteCurricular']));
  }
  public function testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
  {
      $this->markTestSkipped();
  }
}
