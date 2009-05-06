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
 */

/**
 * ServidorCategoriaNivelWebTest class.
 *
 * Testa as ações de atualizar um nível de categoria, como verificação
 * da correção do bug #21.
 *
 * Esse teste precisa ser executado com o banco de dados distribuído na
 * versão 1.0.0.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Test
 * @subpackage  FunctionalTest
 * @since       Classe disponível desde a versão 1.0.1
 * @version     $Id$
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