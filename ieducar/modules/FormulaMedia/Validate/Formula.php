<?php

class FormulaMedia_Validate_Formula extends CoreExt_Validate_Abstract
{
    /**
     * Referência para instância da classe FormulaMedia_Model_Formula do model.
     *
     * @var FormulaMedia_Model_Formula
     */
    protected static $_model = null;

    /**
     * Por padrão, exclui o tokens de nota de recuperação.
     *
     * @see CoreExt_Validate_Abstract#_getDefaultOptions()
     */
    protected function _getDefaultOptions()
    {
        return ['excludeToken' => ['Rc']];
    }

    /**
     * @see CoreExt_Validate_Abstract#_validate()
     *
     * @throws Exception|FormulaMedia_Validate_Exception
     */
    protected function _validate($value)
    {
        // Instancia
        if (is_null(self::$_model)) {
            self::$_model = new FormulaMedia_Model_Formula();
        }

        // Adiciona espaços entre os parênteses
        $value = self::$_model->replaceAliasTokens($value);

        $tokensAvailable = $this->_getTokens();
        $valueTokens     = explode(' ', $value);
        $missingTokens   = [];
        $numericTokens   = [];

        // Verifica se alguma token não permitida foi utilizada
        foreach ($valueTokens as $tk) {
            if ('' == ($tk = trim($tk))) {
                continue;
            }

            if (!in_array($tk, $tokensAvailable)) {
                if (!is_numeric($tk)) {
                    $missingTokens[] = $tk;
                }
            } elseif (self::$_model->isNumericToken($tk)) {
                // Se for uma token numérica, atribui um número 1 para usar na fórmula
                // e avaliar se não lança um erro no PHP
                $numericTokens[$tk] = 1;
            }
        }

        if (0 < count($missingTokens)) {
            throw new Exception('As variáveis ou símbolos não são permitidos: ' . implode(', ', $missingTokens));
        }

        // Verifica se a fórmula é parseada corretamente pelo PHP
        $formula = self::$_model->replaceTokens($value, $numericTokens);

        /*
         * Eval, com surpressão de erro para evitar interrupção do script. Se
         * retornar algum valor diferente de NULL, assume como erro de sintaxe.
         */
        $evaled = @eval('?><?php $result = ' . $formula . '; ?>');
        if (!is_null($evaled)) {
            throw new FormulaMedia_Validate_Exception('A fórmula apresenta erros.'
                . ' Verifique algum parêntese faltante ou um sinal de operação'
                . ' matemática sem um operando.');
        }

        return true;
    }

    /**
     * Retorna as tokens disponíveis para o validador. Uma token pode ser
     * excluída usando a opção excludeToken.
     *
     * @return array
     */
    protected function _getTokens()
    {
        $tokens = self::$_model->getTokens();
        $tokensAvailable = [];

        if ($this->_hasOption('excludeToken') &&
        is_array($this->getOption('excludeToken')) &&
        0 < count($this->getOption('excludeToken'))
    ) {
            $excludeToken = $this->getOption('excludeToken');
            foreach ($tokens as $token) {
                if (!in_array($token, $excludeToken)) {
                    $tokensAvailable[] = $token;
                }
            }
        } else {
            $tokensAvailable = $tokens;
        }

        return $tokensAvailable;
    }
}
