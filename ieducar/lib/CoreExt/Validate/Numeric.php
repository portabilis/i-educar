<?php

class CoreExt_Validate_Numeric extends CoreExt_Validate_Abstract
{
    /**
     * @see CoreExt_Validate_Abstract::_getDefaultOptions()
     */
    protected function _getDefaultOptions()
    {
        return [
            'min' => null,
            'max' => null,
            'trim' => false,
            'invalid' => 'O valor "@value" não é um tipo numérico',
            'min_error' => '"@value" é menor que o valor mínimo permitido (@min)',
            'max_error' => '"@value" é maior que o valor máximo permitido (@max)',
        ];
    }

    /**
     * @see CoreExt_DataMapper::_getFindStatment($pkey) Sobre a conversão com floatval()
     * @see CoreExt_Validate_Abstract::_validate($value)
     */
    protected function _validate($value)
    {
        if (false === $this->getOption('required') && is_null($value)) {
            return true;
        }

        if (!is_numeric($value)) {
            throw new Exception($this->_getErrorMessage('invalid', ['@value' => $value]));
        }

        $value = floatval($value);

        if ($this->_hasOption('min') &&
            $value < floatval($this->getOption('min'))) {
            throw new Exception($this->_getErrorMessage('min_error', [
                '@value' => $value, '@min' => $this->getOption('min')
            ]));
        }

        if ($this->_hasOption('max') &&
            $value > floatval($this->getOption('max'))) {
            throw new Exception($this->_getErrorMessage('max_error', [
                '@value' => $value, '@max' => $this->getOption('max')
            ]));
        }

        return true;
    }

    /**
     * Realiza um sanitização de acordo com o locale, para permitir que valores
     * flutuantes ou números de precisão arbitrária utilizem a pontuação sem
     * localização.
     *
     * @see CoreExt_Validate_Abstract::_sanitize($value)
     */
    protected function _sanitize($value)
    {
        // Verifica se possui o ponto decimal e substitui para o
        // padrão do locale en_US (ponto ".")
        if (str_contains($value, ',')) {
            $value = strtr($value, ',', '.');
            $value = floatval($value);
        }

        return parent::_sanitize($value);
    }
}
