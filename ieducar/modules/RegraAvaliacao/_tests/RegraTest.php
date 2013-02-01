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
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'RegraAvaliacao/Model/Regra.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

/**
 * RegraTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class RegraTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new RegraAvaliacao_Model_Regra();
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('RegraAvaliacao_Model_RegraDataMapper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    // Valores de retorno
    $returnFormulaValue = array(
      new FormulaMedia_Model_Formula(array('nome' => '1º ao 3º ano')),
      new FormulaMedia_Model_Formula(array('nome' => 'Recuperação geral'))
    );

    $returnTabelaValue = array(
      new TabelaArredondamento_Model_Tabela(array(
        'instituicao' => 1,
        'nome'        => 'Tabela genérica de notas numéricas',
        'tipoNota'    => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
      ))
    );

    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Instituição'));

    // Mock para fórmula de média
    $mockFormula = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
    $mockFormula->expects($this->any())
                ->method('findAll')
                ->will($this->onConsecutiveCalls(
                    $returnFormulaValue[0], $returnFormulaValue[1])
                  );

    // Mock para tabela de arredondamento
    $mockTabela = $this->getCleanMock('TabelaArredondamento_Model_TabelaDataMapper');
    $mockTabela->expects($this->any())
               ->method('findAll')
               ->will($this->returnValue($returnTabelaValue));

    // Mock para instituição
    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->any())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Registra a instância no repositório de classes de CoreExt_Entity
    $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

    // Substitui o data mapper de fórmula padrão pelo mock
    $this->_entity->getDataMapper()->setFormulaDataMapper($mockFormula);

    // Substitui o data mapper de tabela padrão pelo mock
    $this->_entity->getDataMapper()->setTabelaDataMapper($mockTabela);

    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_String',  $validators['nome']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['instituicao']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['formulaMedia']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['formulaRecuperacao']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['media']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tabelaArredondamento']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['porcentagemPresenca']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoNota']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoProgressao']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['parecerDescritivo']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoPresenca']);
  }
}