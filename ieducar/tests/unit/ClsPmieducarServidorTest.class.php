<?php

/**
 * ClsPmieducarServidorTest class.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @version $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/UnitBaseTest.class.php';
require_once 'include/pmieducar/clsPmieducarServidor.inc.php';

class ClsPmieducarServidorTest extends UnitBaseTest {

  private
    $codServidor    = NULL,
    $codInstituicao = NULL;

  protected function setUp() {
    $db = new clsBanco();
    $sql = 'SELECT cod_servidor, ref_cod_instituicao FROM pmieducar.servidor WHERE ativo = 1 LIMIT 1';

    $db->Consulta($sql);
    $db->ProximoRegistro();
    list($this->codServidor, $this->codInstituicao) = $db->Tupla();
  }

  public function testPmieducarServidorExists() {
    $codServidor    = $this->codServidor;
    $codInstituicao = $this->codInstituicao;

    $servidor = new clsPmieducarServidor(
      $codServidor, NULL, NULL, NULL, NULL, NULL, 1, $codInstituicao);

    $this->assertTrue((boolean) $servidor->existe());
  }

}