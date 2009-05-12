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
 * ClsPmieducarServidorTest class
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Test
 * @since    Classe disponível desde a versão 1.0.1
 * @version  $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/UnitBaseTest.class.php';
require_once 'include/pmieducar/clsPmieducarServidor.inc.php';

class ClsPmieducarServidorTest extends UnitBaseTest {

  private
    $codServidor    = NULL,
    $codInstituicao = NULL;

  /**
   * @todo  Testes dependentes de dados existentes. Refatorar o teste para usar
   *        mock objects ou dbunit
   */
  protected function setUp() {
    $db = new clsBanco();
    $sql = 'SELECT cod_servidor, ref_cod_instituicao FROM pmieducar.servidor WHERE ativo = 1 LIMIT 1';

    $db->Consulta($sql);
    $db->ProximoRegistro();
    list($this->codServidor, $this->codInstituicao) = $db->Tupla();
  }


  /**
   * Testa se o servidor criado no método setUp() existe
   */
  public function testPmieducarServidorExists() {
    $codServidor    = $this->codServidor;
    $codInstituicao = $this->codInstituicao;

    $servidor = new clsPmieducarServidor(
      $codServidor, NULL, NULL, NULL, NULL, NULL, 1, $codInstituicao);

    $this->assertTrue((boolean) $servidor->existe());
  }


  /**
   * Testa o método getServidorFuncoes() da classe
   */
  public function testGetServidorFuncoes() {
    $codServidor    = $this->codServidor;
    $codInstituicao = $this->codInstituicao;

    $servidor = new clsPmieducarServidor(
      $codServidor, NULL, NULL, NULL, NULL, NULL, 1, $codInstituicao);

    $funcoes = $servidor->getServidorFuncoes();
    $this->assertTrue(is_array($funcoes));
  }


  /**
   * Testa o método isProfessor()
   */
  public function testIsProfessor() {
    $codServidor    = $this->codServidor;
    $codInstituicao = $this->codInstituicao;

    $servidor = new clsPmieducarServidor(
      $codServidor, NULL, NULL, NULL, NULL, NULL, 1, $codInstituicao);

    $professor = $servidor->isProfessor();

    $this->assertTrue($professor);
  }


  /**
   * Stub test para o método getServidorDisciplinasQuadroHorarioHorarios()
   */
  public function testGetServidorDisciplinasQuadroHorarioHorarios() {
    $stub = $this->getMock('clsPmieducarServidor');

    $stub->expects($this->any())
         ->method('getServidorDisciplinasQuadroHorarioHorarios')
         ->will($this->returnValue(array(2, 6)));

    $this->assertEquals(array(2, 6),
      $stub->getServidorDisciplinasQuadroHorarioHorarios(62, 2));
  }


  /**
   * Stub test para o método getServidorDisciplinas()
   */
  public function testGetServidorDisciplinas() {
    $stub = $this->getMock('clsPmieducarServidor');

    $stub->expects($this->any())
         ->method('getServidorDisciplinas')
         ->will($this->returnValue(array(6)));

    $this->assertEquals(array(6),
      $stub->getServidorDisciplinas(57, 2));
  }

}