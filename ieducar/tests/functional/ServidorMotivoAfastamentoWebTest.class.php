<?php

/**
 * ServidorMotivoAfastamentoWebTest class.
 *
 * Testa as ações de atualizar e apagar um motivo de afastamento, como
 * verificação da correção do bug #20.
 *
 * Esse teste precisa ser executado com o banco de dados distribuído na
 * versão 1.0.0.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @since   1.0.1
 * @version SVN: $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/FunctionalBaseTest.class.php';

class ServidorMotivoAfastamentoWebTest extends FunctionalBaseTest {

  /**
   * Os testes a seguir cobrem o bug report #20.
   *
   * @link http://svn.softwarepublico.gov.br/trac/ieducar/ticket/20 Bug report #20
   * @{
   */
  public function createNewEntry() {
    $this->open("/intranet/educar_motivo_afastamento_lst.php");
    $this->clickAndWait("//input[@value=' Novo ']");
    $this->select("ref_cod_instituicao", "label=COBRA Tecnologia");
    $this->type("nm_motivo", "Motivo teste");
    $this->clickAndWait("btn_enviar");
  }

  public function testMotivoAfastamentoUpdate() {
    $this->doLogin();
    $this->createNewEntry();

    $this->open("/intranet/educar_motivo_afastamento_cad.php?cod_motivo_afastamento=1");
    $this->select("ref_cod_instituicao", "label=COBRA Tecnologia");
    $this->type("nm_motivo", "Motivo teste");
    $this->clickAndWait("btn_enviar");

    $statusMessage = $this->isTextPresent("Edição não realizada.");
    $this->assertTrue(!$statusMessage);
    $this->doLogout();
  }

  public function testMotivoAfastamentoDelete() {
    $this->doLogin();

    $this->open("/intranet/educar_motivo_afastamento_lst.php");
    $this->clickAndWait("link=Motivo teste");
    $this->clickAndWait("//input[@value=' Editar ']");
    $this->clickAndWait("//input[@value=' Excluir ']");
    $this->assertTrue((bool)preg_match('/^Excluir registro[\s\S]$/',$this->getConfirmation()));
    $statusMessage = $this->isTextPresent("Exclusão não realizada.");
    $this->assertTrue(!$statusMessage);

    $this->doLogout();
  }
  /**
   * }@
   */

}