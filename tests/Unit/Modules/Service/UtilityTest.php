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
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once __DIR__.'/TestCommon.php';

/**
 * Avaliacao_Service_UtilityTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_UtilityTest extends Avaliacao_Service_TestCommon
{
  public function testArredondaNotaLancaExcecaoSeParametroNaoForNumerico()
  {
    $service = $this->_getServiceInstance();

        $this->expectException('CoreExt_Exception_InvalidArgumentException');
        $this->expectExceptionMessage('O parâmetro $nota ("") não é um valor numérico.');
        $service->arredondaNota(new Avaliacao_Model_NotaComponente());
  }

  public function testArredondaNotaNumerica()
  {
    $service = $this->_getServiceInstance();

    $nota = new Avaliacao_Model_NotaComponente([
        'nota' => 5.85
    ]);
    $this->assertEquals(5.8, $service->arredondaNota($nota));
  }

  public function testArredondaNotaConceitual()
  {
    // Valores padrão dos atributos de TabelaArredondamento_Model_TabelaValor
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => -1,
      'valorMaximo'          => 0
    );

    $tabelaValores = array();

    // I
    $tabelaValores[0] = new TabelaArredondamento_Model_TabelaValor($data);
    $tabelaValores[0]->nome        = 'I';
    $tabelaValores[0]->descricao   = 'Incompleto';
    $tabelaValores[0]->valorMinimo = 0;
    $tabelaValores[0]->valorMaximo = 5.50;

    // S
    $tabelaValores[1] = new TabelaArredondamento_Model_TabelaValor($data);
    $tabelaValores[1]->nome        = 'S';
    $tabelaValores[1]->descricao   = 'Suficiente';
    $tabelaValores[1]->valorMinimo = 5.51;
    $tabelaValores[1]->valorMaximo = 8;

    // O
    $tabelaValores[2] = new TabelaArredondamento_Model_TabelaValor($data);
    $tabelaValores[2]->nome        = 'O';
    $tabelaValores[2]->descricao   = 'Ótimo';
    $tabelaValores[2]->valorMinimo = 8.01;
    $tabelaValores[2]->valorMaximo = 10.0;

    $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
    $mock->expects($this->any())
         ->method('findAll')
         ->will($this->returnValue($tabelaValores));

    $tabelaDataMapper = new TabelaArredondamento_Model_TabelaDataMapper();
    $tabelaDataMapper->setTabelaValorDataMapper($mock);

    $tabela = new TabelaArredondamento_Model_Tabela(array(
        'nome' => 'Conceituais',
        'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL
    ));
    $tabela->setDataMapper($tabelaDataMapper);

    $this->_setRegraOption('tabelaArredondamentoConceitual', $tabela);

    $this->_setRegraOption('tipoNota', RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL);
    $service = $this->_getServiceInstance();
    $nota = new Avaliacao_Model_NotaComponente([
        'nota' => 5.49
    ]);
    $nota->componenteCurricular = RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL;
    $this->mockDbPreparedQuery([['tipo_nota' => ComponenteSerie_Model_TipoNota::CONCEITUAL]]);
    $this->assertEquals('I', $service->arredondaNota($nota));

    $nota = new Avaliacao_Model_NotaComponente([
        'nota' => 6.50
    ]);
    $nota->componenteCurricular = RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL;
    $this->mockDbPreparedQuery([['tipo_nota' => ComponenteSerie_Model_TipoNota::CONCEITUAL]]);
    $this->assertEquals('S', $service->arredondaNota($nota));

    $nota = new Avaliacao_Model_NotaComponente([
        'nota' => 9.15
    ]);
    $nota->componenteCurricular = RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL;
    $this->mockDbPreparedQuery([['tipo_nota' => ComponenteSerie_Model_TipoNota::CONCEITUAL]]);
    $this->assertEquals('O', $service->arredondaNota($nota));
  }

  public function testPreverNotaParaRecuperacao()
  {
    $this->markTestSkipped();

    // Define as notas do aluno
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = array(
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 4
      )),
    );

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notas));

    $this->_setNotaComponenteDataMapperMock($mock);

    $service = $this->_getServiceInstance();

    $ret = $service->preverNotaRecuperacao(1);
    $this->assertEquals(4.0, $ret);
  }

  public function tearDown(): void
  {
    Portabilis_Utils_Database::$_db = null;
  }
}
