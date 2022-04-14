<?php

class TabelaArredondamento_Model_Tabela extends CoreExt_Entity
{
    protected $_data = [
        'instituicao' => null,
        'nome' => null,
        'tipoNota' => null,
        'arredondarNota' => null,
    ];

    protected $_references = [
        'tipoNota' => [
            'value' => 1,
            'class' => 'RegraAvaliacao_Model_Nota_TipoValor',
            'file' => 'RegraAvaliacao/Model/Nota/TipoValor.php'
        ]
    ];

    /**
     * Precisão decimal do valor da nota.
     *
     * @var int
     */
    protected $_precision = 3;

    /**
     * @var array
     */
    protected $_tabelaValores = [];

    /**
     * @see CoreExt_Entity::getDataMapper()
     */
    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            $this->setDataMapper(new TabelaArredondamento_Model_TabelaDataMapper());
        }

        return parent::getDataMapper();
    }

    /**
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

        // Tipo nota
        $tipoNota = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
        $tipoNotas = $tipoNota->getKeys();

        // Remove "nenhum" das opções.
        unset($tipoNotas[RegraAvaliacao_Model_Nota_TipoValor::NENHUM]);

        return [
            'instituicao' => new CoreExt_Validate_Choice(['choices' => $instituicoes]),
            'nome' => new CoreExt_Validate_String(['min' => 5, 'max' => 50]),
            'tipoNota' => new CoreExt_Validate_Choice(['choices' => $tipoNotas])
        ];
    }

    /**
     * Arredonda a nota de acordo com a tabela de valores da instância atual.
     *
     * @param $value
     *
     * @return mixed
     */
    public function round($value, $tipoNota)
    {
        // carrega tabela de arredondamento, caso ainda não tenha sido carregada.
        if (0 == count($this->_tabelaValores)) {
            $this->_tabelaValores = $this->getDataMapper()->findTabelaValor($this);
        }

        $return = Portabilis_Utils_Float::limitDecimal($value, ['limit' => 1]);

        // Se não houver tabela com valores de arredondamento irá retornar o valor
        if (!count($this->_tabelaValores) > 0) {
            return $return;
        }

        // Se o tipo de nota não for uma média final e o tipo de nota for
        // numérica retorna o valor (não arredonda).  Porém caso o campo
        // "Arredondar nota da etapa" seja "Sim", irá arredondar notas da etapa
        // também.

        if (
            $this->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
            && $tipoNota == 1
            && empty($this->get('arredondarNota'))
        ) {
            return $return;
        }

        if ($this->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL) {
            // Multiplicador para transformar os números em uma escala inteira.
            $scale = pow(10, $this->_precision);

            // Escala o valor para se tornar comparável
            $value = floatval($value) * $scale;
            $value = floor($value);

            $return = 0;

            foreach ($this->_tabelaValores as $tabelaValor) {
                $min = $tabelaValor->valorMinimo * $scale;
                $max = $tabelaValor->valorMaximo * $scale;

                if ($value >= $min && $value <= $max) {
                    $return = $tabelaValor->nome;
                    break;
                }

                $return = $tabelaValor->nome;
            }
        } elseif ($this->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
            foreach ($this->_tabelaValores as $tabelaValor) {
                $notaString = strval($return);
                $notaString = explode('.', $return);

                $notaInteira = $notaString[0];
                $casaDecimalNota = $notaString[1];

                if ($casaDecimalNota == $tabelaValor->nome) {
                    switch ($tabelaValor->get('acao')) {
                        case TabelaArredondamento_Model_TipoArredondamentoMedia::ARREDONDAR_PARA_NOTA_INFERIOR:
                            $return = floor($return);
                            break;

                        case TabelaArredondamento_Model_TipoArredondamentoMedia::ARREDONDAR_PARA_NOTA_SUPERIOR:
                            $return = ceil($return);
                            break;

                        case TabelaArredondamento_Model_TipoArredondamentoMedia::ARREDONDAR_PARA_NOTA_ESPECIFICA:
                            $casaDecimalExata = strval($tabelaValor->get('casaDecimalExata'));
                            $return = floatval($notaInteira . '.' . $casaDecimalExata);
                            break;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Prevê em qual range de arredondamento de acordo com um valor esperado. A
     * definição do valor a retornar é dada por uma instância de
     * FormulaMedia_Model_Formula e um array com valores para as tokens da fórmula
     * e o valor da variável a prever. Exemplo:
     *
     * <code>
     * <?php
     * // Passa valores para as tokens disponíveis de FormulaMedia_Model_Formula
     * // e espera que o resultado do cálculo dê 6, usando como referência a
     * // variável "Rc"
     * $data = array(
     *   'formulaValues' => array(
     *     'Se' => 16,
     *     'Et' => 4,
     *     'E1' => 4,
     *     'E2' => 4,
     *     'E3' => 4,
     *     'E4' => 4,
     *     'Rc' => NULL
     *   ),
     *   'expected' => array(
     *     'var' => 'Rc',
     *     'value' => 6
     *   )
     * );
     * </code>
     *
     * @param FormulaMedia_Model_Formula $formula
     * @param array                      $data
     *
     * @return TabelaArredondamento_Model_TabelaValor|NULL Retorna NULL caso
     *                                                     nenhuma instância de TabelaArredondamento_Model_TabelaValor corresponda
     *                                                     ao valor esperado
     *
     * @todo Considerar o atributo valorMaximo da instância para o cálculo da
     *   fórmula. Pode ser útil para os casos de notas conceituais (Exemplo: uma
     *   nota "EP" que tem o range 5.25 a 7, sendo 6 a média de aprovação. Nesse
     *   caso somente o próximo range (se houvesse) daria o valor esperado para
     *   alcançar a média 6).
     */
    public function predictValue(FormulaMedia_Model_Formula $formula, array $data)
    {
        $values = $data['formulaValues'];
        $scale = pow(10, $this->_precision);
        $return = null;

        if (0 == count($this->_tabelaValores)) {
            $this->_tabelaValores = $this->getDataMapper()->findTabelaValor($this);
        }

        $i = 0;
        $total = count($this->_tabelaValores);

        foreach ($this->_tabelaValores as $tabelaValor) {
            $process = [];
            $values[$data['expected']['var']] = $tabelaValor->valorMinimo;
            $process[] = $values;

            // Se for o último item, verifica se a nota máxima também
            if (++$i == $total) {
                $values[$data['expected']['var']] = $tabelaValor->valorMaximo;
                $process[] = $values;
            }

            $valueRounded = $this->_getBestResultFromValuesArray($formula, $process);

            if ($valueRounded * $scale >= ($data['expected']['value'] * $scale)) {
                $return = $tabelaValor;
                break;
            }
        }

        return $return;
    }

    /**
     * @param FormulaMedia_Model_Formula $formula
     * @param array                      $values
     *
     * @return TabelaArredondamento_Model_TabelaValor|NULL
     */
    protected function _getBestResultFromValuesArray(FormulaMedia_Model_Formula $formula, array $values)
    {
        $best = null;

        foreach ($values as $value) {
            $value = $formula->execFormulaMedia($value);
            // FIXME #parameters
            $rounded = $this->round($value, null);

            if (is_null($best)) {
                $best = $rounded;
                continue;
            }

            if ($best < $rounded) {
                $best = $rounded;
            }
        }

        return $rounded;
    }

    /**
     * Método finder para TabelaArredondamento_Model_TabelaValor. Wrapper simples
     * para o mesmo método de TabelaArredondamento_Model_TabelaDataMapper.
     *
     * @return array
     */
    public function findTabelaValor()
    {
        if (0 == count($this->_tabelaValores)) {
            $this->_tabelaValores = $this->getDataMapper()->findTabelaValor($this);
        }

        return $this->_tabelaValores;
    }

    /**
     * @see CoreExt_Entity::__toString()
     */
    public function __toString()
    {
        return $this->nome;
    }

    public function deleteAllValues()
    {
        $tabelaArredondamentoId = $this->_data['id'];

        if ($tabelaArredondamentoId) {
            $db = new clsBanco();
            $db->Consulta('DELETE FROM modules.tabela_arredondamento_valor '
                . 'WHERE tabela_arredondamento_id = ' . $tabelaArredondamentoId);

            return true;
        }

        return false;
    }
}
