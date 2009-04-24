<?php

/**
 * clsBancoTest class.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @version SVN: $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/UnitBaseTest.class.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/clsPmieducarClienteSuspensao.inc.php';

class ClsBancoTest extends UnitBaseTest {

  public function testDoCountFromObj() {
    $db = new clsBanco();
    $db->Conecta();

    $obj = new clsPmieducarClienteSuspensao();
    $this->assertNotEquals(TRUE, is_null($db->doCountFromObj($obj)));
  }

}