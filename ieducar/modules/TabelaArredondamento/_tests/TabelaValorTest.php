<?php

require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'TabelaArredondamento/Model/Tabela.php';
require_once 'RegraAvaliacao/Model/Nota/TipoValor.php';

class TabelaValorTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp()
    {
        $this->_entity = new TabelaArredondamento_Model_TabelaValor();
    }

    public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
    {
        $this->assertType(
            'TabelaArredondamento_Model_TabelaValorDataMapper',
            $this->_entity->getDataMapper()
        );
    }

    public function testEntityValidators()
    {
        $tabelaNumerica = new TabelaArredondamento_Model_Tabela([
            'nome' => 'foo',
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
        ]);

        $tabelaConceitual = new TabelaArredondamento_Model_Tabela([
            'nome' => 'bar',
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL
        ]);

        // Usa a instância recém criaca
        $this->_entity->tabelaArredondamento = $tabelaNumerica;

        // Asserção para nota numérica
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertType('CoreExt_Validate_Numeric', $validators['nome']);
        $this->assertType('CoreExt_Validate_Numeric', $validators['valorMinimo']);
        $this->assertType('CoreExt_Validate_Numeric', $validators['valorMaximo']);
        $this->assertTrue(!isset($validators['descricao']));

        // Asserção para nota conceitual
        $this->_entity->tabelaArredondamento = $tabelaConceitual;
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertType('CoreExt_Validate_String', $validators['nome']);
        $this->assertType('CoreExt_Validate_String', $validators['descricao']);
        $this->assertType('CoreExt_Validate_Numeric', $validators['valorMinimo']);
        $this->assertType('CoreExt_Validate_Numeric', $validators['valorMaximo']);
    }
}
