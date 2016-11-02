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
 * @package     Core
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.0.1
 * @version     $Id$
 */

require_once 'include/pmieducar/clsPmieducarClienteSuspensao.inc.php';

/**
 * clsBancoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Core
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.0.1
 * @todo        Subclassificar classe como IntegrationBaseTest
 * @version     @@package_version@@
 */
class ClsBancoTest extends UnitBaseTest
{
  public function testDoCountFromObj()
  {
    $db = new clsBanco();
    $db->Conecta();

    $obj = new clsPmieducarClienteSuspensao();
    $this->assertNotEquals(TRUE, is_null($db->doCountFromObj($obj)));
  }

  public function testConexao()
  {
    $db = new clsBanco();
    $db->Conecta();

    $this->assertTrue((bool) $db->bLink_ID);
  }

  public function testFormatacaoDeValoresBooleanos()
  {
    $data = array(
      'id' => 1,
      'hasChild' => TRUE
    );

    $db = new clsBanco();
    $formatted = $db->formatValues($data);
    $this->assertSame('t', $formatted['hasChild']);

    $data['hasChild'] = FALSE;
    $formatted = $db->formatValues($data);
    $this->assertSame('f', $formatted['hasChild']);
  }

  public function testOpcaoDeLancamentoDeExcecaoEFalsePorPadrao()
  {
    $db = new clsBanco();
    $this->assertFalse($db->getThrowException());
  }

  public function testConfiguracaoDeOpcaoDeLancamentoDeExcecao()
  {
    $db = new clsBanco();
    $db->setThrowException(TRUE);
    $this->assertTrue($db->getThrowException());
  }

  public function testFetchTipoArrayDeResultadosDeUmaQuery()
  {
    $db = new clsBanco();

    $db->Consulta("SELECT spcname, spcowner, spclocation, spcacl FROM pg_tablespace");
    $row = $db->ProximoRegistro();
    $row = $db->Tupla();
    $this->assertNotNull($row[0]);
    $this->assertNotNull($row['spcname']);
  }

  public function testFetchTipoAssocDeResultadosDeUmaQuery()
  {
    $db = new clsBanco(array('fetchMode' => clsBanco::FETCH_ASSOC));

    $db->Consulta("SELECT spcname, spcowner, spclocation, spcacl FROM pg_tablespace");
    $row = $db->ProximoRegistro();
    $row = $db->Tupla();
    $this->assertFalse(array_key_exists(0, $row));
    $this->assertNotNull($row['spcname']);
  }
}