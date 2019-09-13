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

require_once 'CoreExt/Validate/Choice.php';

/**
 * CoreExt_Validate_ChoiceTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Validate_ChoiceTest extends PHPUnit\Framework\TestCase
{
  protected $_validator = NULL;

  protected $_choices = array(
    'bit' => array(0, 1),
    'various' => array('sim', 'não', 'nda')
  );

  protected function setUp(): void
  {
    $this->_validator = new CoreExt_Validate_Choice();
  }

  public function testValidaSeNenhumaOpcaoPadraoForInformada()
  {
    $this->assertTrue($this->_validator->isValid(0));
  }

  public function testEscolhaValida()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    $this->assertTrue($this->_validator->isValid(0), 'Falhou na asserção "0" numérico.');
    $this->assertTrue($this->_validator->isValid(1), 'Falhou na asserção "1" numérico.');

    // Teste para verificar como reage a tipos diferentes
    $this->assertTrue($this->_validator->isValid('0'), 'Falhou na asserção "0" string.');
    $this->assertTrue($this->_validator->isValid('1'), 'Falhou na asserção "1" string.');

    $this->_validator->setOptions(array('choices' => $this->_choices['various']));
    $this->assertTrue($this->_validator->isValid('sim'));
    $this->assertTrue($this->_validator->isValid('não'));
    $this->assertTrue($this->_validator->isValid('nda'));
  }

  public function testEscolhaInvalidaLancaExcecao()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    try {
      $this->_validator->isValid(2);
      $this->fail("CoreExt_Validate_Choice deveria ter lançado exceção.");
    }
    catch (Exception $e) {
      $this->assertEquals('A opção "2" não existe.', $e->getMessage());
    }

    // 'a' normalmente seria avaliado como 0, mas queremos garantir que isso
    // não ocorra, por isso transformamos tudo em string em _validate().
    try {
      $this->_validator->isValid('a');
      $this->fail("CoreExt_Validate_Choice deveria ter lançado exceção.");
    }
    catch (Exception $e) {
      $this->assertEquals('A opção "a" não existe.', $e->getMessage());
    }

    try {
      $this->_validator->isValid('0a');
      $this->fail("CoreExt_Validate_Choice deveria ter lançado exceção.");
    }
    catch (Exception $e) {
      $this->assertEquals('A opção "0a" não existe.', $e->getMessage());
    }
  }
}
