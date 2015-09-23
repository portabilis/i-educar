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
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';

/**
 * TabelaDataMapperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new TabelaArredondamento_Model_TabelaDataMapper();
  }

  public function testGetterDeValorDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaValorDataMapper', $this->_mapper->getTabelaValorDataMapper());
  }

  public function testFinderTabelaValor()
  {
    // Instância de Tabela
    $instance = new TabelaArredondamento_Model_Tabela(array(
      'id' => 1, 'instituicao' => 1, 'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
    ));

    // Prepara dados para o mock
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => 0,
      'valorMaximo'          => 1
    );

    // Dados de retorno, popula para mock
    $returnValue = array();
    for ($i = 1; $i < 4; $i++) {
      $data['nome']      = $i;
      $data['descricao'] = '';
      $returnValue[] = new TabelaArredondamento_Model_TabelaValor($data);
      $data['valorMinimo'] = $data['valorMinimo'] + 1;
      $data['valorMaximo'] = $data['valorMaximo'] + 1;
    }

    // Expectativa do mock
    $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->with(array(), array('tabelaArredondamento' => 1))
         ->will($this->returnValue($returnValue));

     // Chama o método finder
     $this->_mapper->setTabelaValorDataMapper($mock);
     $returned = $this->_mapper->findTabelaValor($instance);

     // Asserções
     $this->assertEquals($returnValue[0], $returned[0]);
     $this->assertEquals($returnValue[1], $returned[1]);
     $this->assertEquals($returnValue[2], $returned[2]);
  }
}