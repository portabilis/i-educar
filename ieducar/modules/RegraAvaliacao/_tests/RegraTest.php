<?php

require_once 'RegraAvaliacao/Model/Regra.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

class RegraTest extends UnitBaseTest
{

    protected $_entity = null;

    protected function setUp()
    {
        $this->_entity = new RegraAvaliacao_Model_Regra();
    }

    public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
    {
        $this->assertType(
            'RegraAvaliacao_Model_RegraDataMapper',
            $this->_entity->getDataMapper()
        );
    }

    public function testEntityValidators()
    {
        // Valores de retorno
        $returnFormulaValue = [
            new FormulaMedia_Model_Formula(['nome' => '1º ao 3º ano']),
            new FormulaMedia_Model_Formula(['nome' => 'Recuperação geral'])
        ];

        $returnTabelaValue = [
            new TabelaArredondamento_Model_Tabela([
                'instituicao' => 1,
                'nome' => 'Tabela genérica de notas numéricas',
                'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
            ])
        ];

        $returnValue = [['cod_instituicao' => 1, 'nm_instituicao' => 'Instituição']];

        // Mock para fórmula de média
        $mockFormula = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
        $mockFormula->expects($this->any())
            ->method('findAll')
            ->will(
                $this->onConsecutiveCalls(
                $returnFormulaValue[0],
                $returnFormulaValue[1]
            ));

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

        $this->assertType('CoreExt_Validate_String', $validators['nome']);
        $this->assertType('CoreExt_Validate_Choice', $validators['instituicao']);
        $this->assertType('CoreExt_Validate_Choice', $validators['formulaMedia']);
        $this->assertType('CoreExt_Validate_Choice', $validators['formulaRecuperacao']);
        $this->assertType('CoreExt_Validate_Numeric', $validators['media']);
        $this->assertType('CoreExt_Validate_Choice', $validators['tabelaArredondamento']);
        $this->assertType('CoreExt_Validate_Numeric', $validators['porcentagemPresenca']);
        $this->assertType('CoreExt_Validate_Choice', $validators['tipoNota']);
        $this->assertType('CoreExt_Validate_Choice', $validators['tipoProgressao']);
        $this->assertType('CoreExt_Validate_Choice', $validators['parecerDescritivo']);
        $this->assertType('CoreExt_Validate_Choice', $validators['tipoPresenca']);
    }
}
