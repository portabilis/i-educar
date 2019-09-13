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
 * Avaliacao_Service_ParecerDescritivoAlunoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_ParecerDescritivoAlunoTest extends Avaliacao_Service_TestCommon
{
  protected function setUp(): void
  {
    $this->_setRegraOption('parecerDescritivo', RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);
    parent::setUp();
  }

  public function testCriaNovaInstanciaDeParecerDescritivoAluno()
  {
    $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

    $parecerSave  = clone $parecerAluno;
    $parecerSave->id = NULL;

    // Configura mock para Avaliacao_Model_ParecerDescritivoAlunoDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_ParecerDescritivoAlunoDataMapper');
    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('matricula' => $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($parecerSave)
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('findAll')
         ->with(array(), array('matricula' => $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue(array($parecerAluno)));

    $this->_setParecerDescritivoAlunoDataMapperMock($mock);

    $service = $this->_getServiceInstance();
  }
}
