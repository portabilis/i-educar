<?php

/**
 * ServidorCategoriaNivelWebTest class.
 *
 * Testa as ações de atualizar um nível de categoria, como verificação
 * da correção do bug #21.
 *
 * Esse teste precisa ser executado com o banco de dados distribuído na
 * versão 1.0.0.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @since   1.0.1
 * @version SVN: $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/FunctionalBaseTest.class.php';

class ServidorCategoriaNivelWebTest extends FunctionalBaseTest {

  private $slStringTest = 'Selenese Nivel Teste';

  protected function tearDown() {
    require_once 'include/pmieducar/clsPmieducarCategoriaNivel.inc.php';

    $db = new clsBanco();
    $categoriaNivel = new clsPmieducarCategoriaNivel();
    $sql = sprintf('DELETE FROM %s WHERE %s = \'%s\'',
      $categoriaNivel->_tabela, 'nm_categoria_nivel', $this->slStringTest);

    $db->Consulta($sql);
  }

  private function createNewEntry() {
    $this->open("/intranet/educar_categoria_nivel_lst.php");
    $this->clickAndWait("//input[@value=' Novo ']");
    $this->type("nm_categoria_nivel", $this->slStringTest);
    $this->clickAndWait("btn_enviar");
  }

  public function testCategoriaNivelDelete() {
    $this->doLogin();
    $this->createNewEntry();

    $this->clickAndWait("link=" . $this->slStringTest);
    $this->clickAndWait("//input[@value=' Editar ']");
    $this->clickAndWait("//input[@value=' Excluir ']");
    $this->assertTrue((bool) preg_match('/^Excluir registro[\s\S]$/', $this->getConfirmation()));
    $this->assertTrue(!$this->isTextPresent($this->slStringTest));
  }
}