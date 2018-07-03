<?php

require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';

class TabelaDataMapperTest extends UnitBaseTest
{
    protected $_mapper = null;

    protected function setUp()
    {
        $this->_mapper = new TabelaArredondamento_Model_TabelaDataMapper();
    }

    public function testGetterDeValorDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
    {
        $this->assertType(
            'TabelaArredondamento_Model_TabelaValorDataMapper',
            $this->_mapper->getTabelaValorDataMapper()
        );
    }

    public function testFinderTabelaValor()
    {
        // Instância de Tabela
        $instance = new TabelaArredondamento_Model_Tabela([
            'id' => 1,
            'instituicao' => 1,
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
        ]);

        // Prepara dados para o mock
        $data = [
            'tabelaArredondamento' => 1,
            'nome' => null,
            'descricao' => null,
            'valorMinimo' => 0,
            'valorMaximo' => 1
        ];

        // Dados de retorno, popula para mock
        $returnValue = [];

        for ($i = 1; $i < 4; $i++) {
            $data['nome'] = $i;
            $data['descricao'] = '';
            $returnValue[] = new TabelaArredondamento_Model_TabelaValor($data);
            $data['valorMinimo'] = $data['valorMinimo'] + 1;
            $data['valorMaximo'] = $data['valorMaximo'] + 1;
        }

        // Expectativa do mock
        $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');

        $mock->expects($this->once())
            ->method('findAll')
            ->with([], ['tabelaArredondamento' => 1])
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
