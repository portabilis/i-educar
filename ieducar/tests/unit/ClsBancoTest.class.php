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
 * clsBancoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Test
 * @subpackage  UnitTest
 * @since       Classe disponível desde a versão 1.0.1
 * @version     $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/UnitBaseTest.class.php';
require_once 'include/pmieducar/clsPmieducarClienteSuspensao.inc.php';

class ClsBancoTest extends UnitBaseTest {

  public function testDoCountFromObj() {
    $db = new clsBanco();
    $db->Conecta();

    $obj = new clsPmieducarClienteSuspensao();
    $this->assertNotEquals(TRUE, is_null($db->doCountFromObj($obj)));
  }

  public function testConexao() {
    $string = 'host=localhost dbname=ieducardb user=ieducaruser password=password port=5432';

    $db = new clsBanco();
    $db->setHost('localhost');
    $db->setDbname('ieducardb');
    $db->setUser('ieducaruser');
    $db->setPassword('password');
    $db->setPort('5432');

    $db->FraseConexao();
    $stringCompare = $db->getFraseConexao();
    $this->assertEquals($string, $stringCompare);

    $db->Conecta();
    $this->assertTrue((bool)$db->bLink_ID);
  }

}