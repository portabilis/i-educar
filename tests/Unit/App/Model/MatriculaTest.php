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
 * @version     $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/tests/unit/App/Model/IedFinderTest.php 1046 2009-12-21T17:30:49.663282Z eriksencosta  $
 */

require_once 'App/Model/Matricula.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';

/**
 * App_Model_MatriculaTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     App_Model
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class App_Model_MatriculaTest extends UnitBaseTest
{
  public function testAtualizaMatricula()
  {
    $matricula = $this->getCleanMock('clsPmieducarMatricula');
    $matricula->expects($this->once())
              ->method('edita')
              ->will($this->returnValue(TRUE));

    // Guarda no repositório estático de classes
      CoreExt_Entity::addClassToStorage('clsPmieducarMatricula', $matricula,
          NULL, TRUE);

    App_Model_Matricula::atualizaMatricula(1, 1, TRUE);
  }
}
