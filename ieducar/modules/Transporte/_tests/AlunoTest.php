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
 * @package     Transporte
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'Transporte/Model/Aluno.php';
require_once 'Transporte/Model/Responsavel.php';

/**
 * Transporte_Model_AlunoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Transporte
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Transporte_Model_AlunoTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new Transporte_Model_Aluno();
  }

  public function testEntityValidators()
  {
    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Numeric', $validators['aluno']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['responsavel']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['user']);

    // Verifica se a opção 'choices' corresponde aos valores do Enum Responsavel.
    $responsavel = Transporte_Model_Responsavel::getInstance();
    $this->assertEquals(
      $responsavel->getKeys(), $validators['responsavel']->getOption('choices')
    );
  }
}