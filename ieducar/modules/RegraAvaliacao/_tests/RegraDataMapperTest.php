<?php

require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';

class RegraDataMapperTest extends UnitBaseTest
{

    protected $_mapper = null;

    protected function setUp()
    {
        $this->_mapper = new RegraAvaliacao_Model_RegraDataMapper();
    }

    public function testGetterDeFormulaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
    {
        $this->assertType(
            'FormulaMedia_Model_FormulaDataMapper',
            $this->_mapper->getFormulaDataMapper()
        );
    }

    public function testGetterDeTabelaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
    {
        $this->assertType(
            'TabelaArredondamento_Model_TabelaDataMapper',
            $this->_mapper->getTabelaDataMapper()
        );
    }

    public function testFindFormulaMediaFinalDataMapper()
    {
        // Valores de retorno
        $returnValue = [new FormulaMedia_Model_Formula([
            'id' => 1,
            'nome' => '1º ao 3º ano',
            'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_FINAL
        ])];

        // Mock para área de conhecimento
        $mock = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
        $mock->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($returnValue));

        // Substitui o data mapper padrão pelo mock
        $this->_mapper->setFormulaDataMapper($mock);
        $formulas = $this->_mapper->findFormulaMediaFinal();

        $this->assertEquals($returnValue, $formulas);
    }

    public function testFindFormulaMediaRecuperacaoDataMapper()
    {
        // Valores de retorno
        $returnValue = [new FormulaMedia_Model_Formula([
            'id' => 1,
            'nome' => '1º ao 3º ano',
            'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
        ])];

        // Mock para área de conhecimento
        $mock = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
        $mock->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($returnValue));

        // Substitui o data mapper padrão pelo mock
        $this->_mapper->setFormulaDataMapper($mock);
        $formulas = $this->_mapper->findFormulaMediaRecuperacao();

        $this->assertEquals($returnValue, $formulas);
    }

    public function testFindTabelaArredondamento()
    {
        // Instância de RegraAvaliacao_Model_Regra
        $instance = new RegraAvaliacao_Model_Regra(['instituicao' => 1]);

        // Valores de retorno
        $returnValue = [new TabelaArredondamento_Model_Tabela([
            'id' => 1,
            'instituicao' => 1,
            'nome' => 'Tabela geral de notas numéricas',
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
        ])];

        // Mock para tabela de arredondamento
        $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaDataMapper');
        $mock->expects($this->once())
            ->method('findAll')
            ->with([], [
                'instituicao' => 1,
                'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
            ])
            ->will($this->returnValue($returnValue));

        // Substitui o data mapper padrão pelo mock
        $this->_mapper->setTabelaDataMapper($mock);
        $tabelas = $this->_mapper->findTabelaArredondamento($instance);
    }
}
