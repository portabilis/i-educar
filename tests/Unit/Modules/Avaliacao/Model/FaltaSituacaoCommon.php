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
 * Avaliacao_Service_FaltaSituacaoCommon class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaSituacaoCommon extends Avaliacao_Service_TestCommon
{
  protected function _setUpFaltaAbstractDataMapperMock(
    Avaliacao_Model_FaltaAluno $faltaAluno, array $faltas)
  {
    // Configura mock para notas
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaAbstractDataMapper');

    $mock->expects($this->any())
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($faltas));

    $this->_setFaltaAbstractDataMapperMock($mock);
  }

  protected function _getExpectedSituacaoFaltas()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    // Valores retornados pelas instâncias de classes legadas
    $cursoHoraFalta    = $this->_getConfigOption('curso', 'hora_falta');
    $serieCargaHoraria = $this->_getConfigOption('serie', 'carga_horaria');

    // Porcentagem configurada na regra
    $porcentagemPresenca = $this->_getRegraOption('porcentagemPresenca');

    $expected = new stdClass();
    $expected->situacao                 = 0;
    $expected->tipoFalta                = 0;
    $expected->cargaHoraria             = 0;
    $expected->cursoHoraFalta           = 0;
    $expected->totalFaltas              = 0;
    $expected->horasFaltas              = 0;
    $expected->porcentagemFalta         = 0;
    $expected->porcentagemPresenca      = 100;
    $expected->porcentagemPresencaRegra = 0;
    $expected->componentesCurriculares  = array();

    $expected->tipoFalta                = $faltaAluno->get('tipoFalta');
    $expected->cursoHoraFalta           = $cursoHoraFalta;
    $expected->porcentagemPresencaRegra = $porcentagemPresenca;
    $expected->cargaHoraria             = $serieCargaHoraria;
    $expected->diasLetivos              = null;

    return $expected;
  }
}