<?php

class FormulaMedia_Model_Formula extends CoreExt_Entity
{
    /**
     * Tokens válidos para ser utilizado pela fórmula.
     *
     * - Se: soma das notas de todas as etapas
     * - Et: total de etapas
     * - E1 a E10: nota na etapa En (fica limitado a 10 etapas)
     * - /: divisão
     * - *: multiplicação
     * - x: alias para *
     * - (: abre parêntese
     * - ): fecha parêntese
     *
     * @var array
     */
    protected $_tokens = [
        'Se', 'Et', 'Rc',
        'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10',
        'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10',
        'RSP1', 'RSP2', 'RSP3', 'RSP4', 'RSP5', 'RSP6', 'RSP7', 'RSP8', 'RSP9', 'RSP10',
        'RSPS1', 'RSPS2', 'RSPS3', 'RSPS4', 'RSPS5', 'RSPS6', 'RSPS7', 'RSPS8', 'RSPS9', 'RSPS10',
        'RSPM1', 'RSPM2', 'RSPM3', 'RSPM4', 'RSPM5', 'RSPM6', 'RSPM7', 'RSPM8', 'RSPM9', 'RSPM10',
        '/', '*', 'x', '+',
        '(', ')',
        '?', ':',
        '>', '<'
    ];

    /**
     * Tokens que podem ser substituídos por valores numéricos.
     *
     * @var array
     */
    protected $_tokenNumerics = [
        'Se', 'Et', 'Rc',
        'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10',
        'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10',
        'RSP1', 'RSP2', 'RSP3', 'RSP4', 'RSP5', 'RSP6', 'RSP7', 'RSP8', 'RSP9', 'RSP10',
        'RSPS1', 'RSPS2', 'RSPS3', 'RSPS4', 'RSPS5', 'RSPS6', 'RSPS7', 'RSPS8', 'RSPS9', 'RSPS10',
        'RSPM1', 'RSPM2', 'RSPM3', 'RSPM4', 'RSPM5', 'RSPM6', 'RSPM7', 'RSPM8', 'RSPM9', 'RSPM10'
    ];

    /**
     * Tokens que podem ser substituídos pelo parâmetro substituiMenorNotaRc.
     *
     * @var array
     */
    protected $_tokenEtapas = [
        'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10'
    ];

    /**
     * Atributos do model.
     *
     * @var array
     */
    protected $_data = [
        'instituicao' => null,
        'nome' => null,
        'formulaMedia' => null,
        'tipoFormula' => null,
        'substituiMenorNotaRc' => null,
    ];

    /**
     * Referências.
     *
     * @var array
     */
    protected $_references = [
        'tipoFormula' => [
            'value' => FormulaMedia_Model_TipoFormula::MEDIA_FINAL,
            'class' => 'FormulaMedia_Model_TipoFormula',
            'file' => 'FormulaMedia/Model/TipoFormula.php'
        ]
    ];

    /**
     * Verifica se um token pode receber um valor numérico.
     *
     * @param string $token
     *
     * @return bool
     */
    private function isEtapaToken($token)
    {
        return in_array($token, $this->_tokenEtapas);
    }

    /**
     * @param array $values
     *
     * @return array
     */
    private function substituiMenorNotaPorRecuperacao($values)
    {
        $menorEtapa = null;

        foreach (array_reverse($values) as $key => $value) {
            if ($this->isEtapaToken($key)) {
                if (empty($menorEtapa)) {
                    $menorEtapa = $key;
                } elseif ($value < $values[$menorEtapa]) {
                    $menorEtapa = $key;
                }
            }
        }
        if ($values['Rc'] > $values[$menorEtapa]) {
            $values[$menorEtapa] = $values['Rc'];
        }

        return $values;
    }

    /**
     * Executa o código da fórmula usando eval.
     *
     * @param string $code
     *
     * @return float|null
     */
    protected function _exec($code)
    {
        $result = null;

        eval('?><?php $result = ' . $code . '; ?>');

        return $result;
    }

    /**
     * Retorna os tokens permitidos para uma fórmula.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->_tokens;
    }

    /**
     * Verifica se um token pode receber um valor numérico.
     *
     * @param string $token
     *
     * @return bool
     */
    public function isNumericToken($token)
    {
        return in_array($token, $this->_tokenNumerics);
    }

    /**
     * Substitui os tokens numéricos de uma fórmula, através de um array
     * associativo.
     *
     * <code>
     * <?php
     * $values = array(
     *   'E1' => 5,
     *   'E2' => 7,
     *   'E3' => 8,
     *   'E4' => 10,
     *   'Et' => 4,
     *   'Rc' => 0,
     *   'Se' => 30
     * );
     *
     * $formula = $formulaModel->replaceTokens($formulaModel->formulaMedia, $values);
     * </code>
     *
     * @param string $formula
     * @param array  $values
     *
     * @return string
     */
    public function replaceTokens($formula, $values = [])
    {
        $formula = $this->replaceAliasTokens($formula);

        if ($this->substituiMenorNotaRc && is_numeric($values['Rc'])) {
            $values = $this->substituiMenorNotaPorRecuperacao($values);
        }

        $patterns = [];
        foreach ($values as $key => $value) {
            if ($this->isNumericToken($key)) {
                // Usa @ como delimitador para evitar problemas com o sinal de divisão
                $patterns[$key] = '@' . $key . '@';
            }
        }

        // Substitui os tokens
        $replaced = preg_replace($patterns, $values, $formula);

        // Zera os parâmetros faltantes para não dar erro na formular
        return $this->fixedUndefinedParams($replaced);
    }

    /**
     * Troca os tokens de alias pelos usados durante a execução da fórmula.
     *
     * @param string $formula
     *
     * @return string
     */
    public function replaceAliasTokens($formula)
    {
        return preg_replace(['/\(/', '/\)/', '/x/'], [' ( ', ' ) ', '*'], $formula);
    }

    /**
     *
     * @param array $values
     *
     * @return float|null
     */
    public function execFormulaMedia(array $values = [])
    {
        $formula = $this->replaceTokens($this->formulaMedia, $values);

        return $this->_exec($formula);
    }

    /**
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
     *
     * @return array
     */
    public function getDefaultValidatorCollection()
    {
        $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());
        $tipoFormula = FormulaMedia_Model_TipoFormula::getInstance();

        // Se for de recuperação, inclui o token "Rc" como permitido.

        $formulaValidatorOptions = [];

        if (FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO == $this->get('tipoFormula')) {
            $formulaValidatorOptions = ['excludeToken' => null];
        }

        return [
            'instituicao' => new CoreExt_Validate_Choice(['choices' => $instituicoes]),
            'nome' => new CoreExt_Validate_String(['min' => 5, 'max' => 50]),
            'formulaMedia' => new FormulaMedia_Validate_Formula($formulaValidatorOptions),
            'tipoFormula' => new CoreExt_Validate_Choice(['choices' => $tipoFormula->getKeys()])
        ];
    }

    private function fixedUndefinedParams($replaced) :?string
    {
        if (null === $replaced) {
            return null;
        }

        $patterns = [];
        $zeroValue = [];
        foreach ($this->_tokenNumerics as $key => $value) {
            $zeroValue[] = 0;
            $patterns[$key] = '@' . $value . '@';
        }

        return preg_replace($patterns, $zeroValue, $replaced);
    }

    /**
     * @see CoreExt_Entity::__toString()
     */
    public function __toString()
    {
        return $this->nome . ': <br />' . $this->formulaMedia;
    }
}
