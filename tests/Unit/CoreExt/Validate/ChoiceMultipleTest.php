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
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Validate/ChoiceMultiple.php';

/**
 * CoreExt_Validate_ChoiceMultipleTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Validate_ChoiceMultipleTest extends PHPUnit\Framework\TestCase
{
  protected $_validator = NULL;

  protected $_choices = array(
    'bit' => array(0, 1),
    'various' => array('sim', 'não', 'nda')
  );

  protected function setUp(): void
  {
    $this->_validator = new CoreExt_Validate_ChoiceMultiple();
  }

  public function testEscolhaMultiplaValida()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    $this->assertTrue($this->_validator->isValid(array(0, 1)));

    // Testa com valor igual, mas tipo de dado diferente
    $this->assertTrue($this->_validator->isValid(array('0', '1')));
  }

  public function testEscolhaMultiplaInvalidaLancaExcecao()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    try {
      $this->_validator->isValid(array(0, 2, 3));
      $this->fail("CoreExt_Validate_ChoiceMultiple deveria ter lançado exceção.");
    }
    catch (Exception $e) {
      $this->assertEquals('As opções "2, 3" não existem.', $e->getMessage());
    }

    // 'a' e '0a' normalmente seriam avaliados como '0' e '1' mas não queremos
    // esse tipo de comportamento.
    try {
      $this->_validator->isValid(array(0, 'a', '1a'));
      $this->fail("CoreExt_Validate_ChoiceMultiple deveria ter lançado exceção.");
    }
    catch (Exception $e) {
      $this->assertEquals('As opções "a, 1a" não existem.', $e->getMessage());
    }
  }
}
