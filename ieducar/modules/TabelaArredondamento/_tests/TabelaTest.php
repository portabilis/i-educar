<?php

require_once 'TabelaArredondamento/Model/Tabela.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'FormulaMedia/Model/Formula.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

class TabelaTest extends UnitBaseTest
{
    protected $_entity = null;
    protected $_tabelaValores = [];

    protected function setUp()
    {
        $this->_entity = new TabelaArredondamento_Model_Tabela();

        // Cria uma tabela de arredondamento numérica
        $data = [
            'tabelaArredondamento' => 1,
            'nome' => null,
            'descricao' => null,
            'valorMinimo' => -1,
            'valorMaximo' => 0
        ];

        $tabelaValores = [];

        $range = range(0, 10, 0.5);
        $minValue = 0.249;
        $maxValue = 0.250;

        foreach ($range as $offset) {
            $nome = $offset;

            $min = $nome - $minValue;
            $max = $nome + $maxValue;

            if ($offset == 0) {
                $min = 0;
            } elseif ($offset == 10) {
                $max = 10;
            }

            $data['nome'] = $nome;
            $data['valorMinimo'] = $min;
            $data['valorMaximo'] = $max;

            $tabelaValores[] = new TabelaArredondamento_Model_TabelaValor($data);
        }

        $this->_tabelaValores = $tabelaValores;
    }

    protected function _getMockTabelaValor()
    {
        // Configura um
        $mapperMock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');

        $mapperMock->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($this->_tabelaValores));

        return $mapperMock;
    }

    public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
    {
        $this->assertType(
            'TabelaArredondamento_Model_TabelaDataMapper',
            $this->_entity->getDataMapper()
        );
    }

    public function testEntityValidators()
    {
        // Valores de retorno
        $returnValue = [[
            'cod_instituicao' => 1,
            'nm_instituicao' => 'Instituição'
        ]];

        // Mock para instituição
        $mock = $this->getCleanMock('clsPmieducarInstituicao');
        $mock->expects($this->any())
            ->method('lista')
            ->will($this->returnValue($returnValue));

        // Registra a instância no repositório de classes de CoreExt_Entity
        $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertType('CoreExt_Validate_String', $validators['nome']);
        $this->assertType('CoreExt_Validate_Choice', $validators['instituicao']);
        $this->assertType('CoreExt_Validate_Choice', $validators['tipoNota']);
    }

    public function testArredondamentoDeNota()
    {
        $this->_entity
            ->getDataMapper()
            ->setTabelaValorDataMapper($this->_getMockTabelaValor());

        $this->assertEquals(5, $this->_entity->round(5));
        $this->assertEquals(7, $this->_entity->round(7.250));

        try {
            $this->_entity->round(11);
            $this->fail('Método round() deveria ter lançado uma exceção.');
        } catch (CoreExt_Exception_InvalidArgumentException $e) {
            //...
        }
    }

    public function testCalculoDeNotaNecessariaParaMedia()
    {
        $this->_entity
            ->getDataMapper()
            ->setTabelaValorDataMapper($this->_getMockTabelaValor());

        $formula = new FormulaMedia_Model_Formula([
            'formulaMedia' => '(Se / Et * 0.6) + (Rc * 0.4)',
            'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
        ]);

        $expected = new TabelaArredondamento_Model_TabelaValor([
            'nome' => 10,
            'valorMinimo' => 9.751,
            'valorMaximo' => 10
        ]);

        $data = [
            'formulaValues' => [
                'Se' => 13.334,
                'Et' => 4,
                'Rc' => null
            ],
            'expected' => [
                'var' => 'Rc',
                'value' => 6
            ]
        ];

        $ret = $this->_entity->predictValue($formula, $data);

        $this->assertEquals(
            [
                $expected->nome,
                $expected->valorMinimo,
                $expected->valorMaximo
            ],
            [
                $ret->nome,
                $ret->valorMinimo,
                $ret->valorMaximo
            ]
        );

        $expected = new TabelaArredondamento_Model_TabelaValor([
            'nome' => 9,
            'valorMinimo' => 8.751,
            'valorMaximo' => 9.250
        ]);

        $data = [
            'formulaValues' => [
                'Se' => 16,
                'Et' => 4,
                'Rc' => null
            ],
            'expected' => [
                'var' => 'Rc',
                'value' => 6
            ]
        ];

        $ret = $this->_entity->predictValue($formula, $data);
        $this->assertEquals(
            [
                $expected->nome,
                $expected->valorMinimo,
                $expected->valorMaximo
            ],
            [
                $ret->nome,
                $ret->valorMinimo,
                $ret->valorMaximo
            ]
        );

        $formula = new FormulaMedia_Model_Formula([
            'formulaMedia' => '((E1 + E2 + E3 + E4) / 4 * 0.6) + (Rc * 0.4)',
            'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
        ]);

        $expected = new TabelaArredondamento_Model_TabelaValor([
            'nome' => 9,
            'valorMinimo' => 8.751,
            'valorMaximo' => 9.250
        ]);

        $data = [
            'formulaValues' => [
                'Se' => null,
                'Et' => null,
                'E1' => 4,
                'E2' => 4,
                'E3' => 4,
                'E4' => 4,
                'Rc' => null
            ],
            'expected' => [
                'var' => 'Rc',
                'value' => 6
            ]
        ];

        $ret = $this->_entity->predictValue($formula, $data);

        $this->assertEquals(
            [
                $expected->nome,
                $expected->valorMinimo,
                $expected->valorMaximo
            ],
            [
                $ret->nome,
                $ret->valorMinimo,
                $ret->valorMaximo
            ]
        );
    }

    /**
     * @group CoreExt_Locale
     */
    public function testArredondamentoDeNotaComLocaleDiferenteDoPadrao()
    {
        $this->_entity
            ->getDataMapper()
            ->setTabelaValorDataMapper($this->_getMockTabelaValor());

        $this->assertEquals(5, $this->_entity->round('5,005'));

        $locale = CoreExt_Locale::getInstance();
        $locale->setLocale('pt_BR');
        $this->assertEquals(8, $this->_entity->round('8,250'));
    }
}
