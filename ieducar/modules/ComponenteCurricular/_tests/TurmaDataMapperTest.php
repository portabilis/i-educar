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
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

/**
 * TurmDataMapperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class TurmaDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  public function testBulkUpdate()
  {
    $returnValue = array(
      array(
        'componente_curricular_id' => 1,
        'ano_escolar_id'           => 1,
        'escola_id'                => 1,
        'turma_id'                 => 1,
        'carga_horaria'            => NULL
      ),
      array(
        'componente_curricular_id' => 3,
        'ano_escolar_id'           => 1,
        'escola_id'                => 1,
        'turma_id'                 => 1,
        'carga_horaria'            => 100
      )
    );

    $componentes = array(
      array(
        'id' => 1,
        'cargaHoraria' => 100
      ),
      array(
        'id' => 2,
        'cargaHoraria' => NULL
      )
    );

    $mock = $this->getDbMock();

    // 1 SELECT, 1 DELETE, 1 INSERT e 1 UPDATE
    $mock->expects($this->exactly(4))
         ->method('Consulta');

    $mock->expects($this->exactly(3))
         ->method('ProximoRegistro')
         ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $mock->expects($this->exactly(2))
         ->method('Tupla')
         ->will($this->onConsecutiveCalls($returnValue[0], $returnValue[1]));

    $mapper = new ComponenteCurricular_Model_TurmaDataMapper($mock);
    $mapper->bulkUpdate(1, 1, 1, $componentes);
  }
}