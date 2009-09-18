<?php

/*
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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   FunctionalTests
 * @since     Classe disponível desde a versão 1.0.1
 * @version   $Id$
 */

/**
 * ServidorMotivoAfastamentoWebTest class.
 *
 * Testa as ações de atualizar e apagar um motivo de afastamento, como
 * verificação da correção do bug #20.
 *
 * Esse teste precisa ser executado com o banco de dados distribuído na
 * versão 1.0.0.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://svn.softwarepublico.gov.br/trac/ieducar/ticket/20
 * @package   FunctionalTests
 * @since     Classe disponível desde a versão 1.0.1
 * @version   @@package_version@@
 */
class ServidorMotivoAfastamentoWebTest extends FunctionalBaseTest
{
  public function createNewEntry()
  {
    $this->open("/intranet/educar_motivo_afastamento_lst.php");
    $this->clickAndWait("//input[@value=' Novo ']");
    $this->select("ref_cod_instituicao", "label=i-Educar");
    $this->type("nm_motivo", "Motivo teste");
    $this->clickAndWait("btn_enviar");
  }

  public function testMotivoAfastamentoUpdate()
  {
    $this->doLogin();
    $this->createNewEntry();

    $this->open("/intranet/educar_motivo_afastamento_cad.php?cod_motivo_afastamento=1");
    $this->select("ref_cod_instituicao", "label=i-Educar");
    $this->type("nm_motivo", "Motivo teste");
    $this->clickAndWait("btn_enviar");

    $statusMessage = $this->isTextPresent("Edição não realizada.");
    $this->assertTrue(!$statusMessage);
    $this->doLogout();
  }

  public function testMotivoAfastamentoDelete()
  {
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
}