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
 * Avaliacao_Service_FaltaCommon abstract class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Service_FaltaCommon extends Avaliacao_Service_TestCommon
{
  /**
   * @return Avaliacao_Model_FaltaComponente
   */
  protected abstract function _getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim();

  /**
   * @return Avaliacao_Model_FaltaComponente
   */
  protected abstract function _getFaltaTestAdicionaFaltaNoBoletim();

  /**
   * Realiza asserções específicas para os validadores de uma instância de
   * Avaliacao_Model_FaltaAbstract
   */
  protected abstract function _testAdicionaFaltaNoBoletimVerificaValidadores(Avaliacao_Model_FaltaAbstract $falta);

  /**
   * @see Avaliacao_Service_FaltaCommon#_getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
   */
  public function testInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
  {
    $service = $this->_getServiceInstance();

    $falta = $this->_getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim();

    // Atribuição simples
    $service->addFalta($falta)
            ->addFalta($falta);

    $this->assertEquals(1, count($service->getFaltas()));

    // Via atribuição em lote
    $falta = clone $falta;
    $service->addFaltas(array($falta, $falta, $falta));

    $this->assertEquals(2, count($service->getFaltas()));
  }

  /**
   * @see Avaliacao_Service_FaltaCommon#_getFaltaTestAdicionaFaltaNoBoletim()
   * @see Avaliacao_Service_FaltaCommon#_testAdicionaFaltaNoBoletimVerificaValidadores()
   */
  public function testAdicionaFaltaNoBoletim()
  {
    $service = $this->_getServiceInstance();

    $falta = $this->_getFaltaTestAdicionaFaltaNoBoletim();

    $faltaOriginal = clone $falta;
    $service->addFalta($falta);

    $faltas = $service->getFaltas();
    $serviceFalta = array_shift($faltas);

    // Valores declarados explicitamente, verificação explícita
    $this->assertEquals($faltaOriginal->quantidade, $serviceFalta->quantidade);

    // Valores populados pelo service
    $this->assertNotEquals($faltaOriginal->etapa, $serviceFalta->etapa);

    // Validadores injetados no objeto
    $this->_testAdicionaFaltaNoBoletimVerificaValidadores($serviceFalta);
  }
}