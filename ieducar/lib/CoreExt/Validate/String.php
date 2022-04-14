<?php

class CoreExt_Validate_String extends CoreExt_Validate_Abstract
{
    /**
     * @see CoreExt_Validate_Abstract#_getDefaultOptions()
     */
    protected function _getDefaultOptions()
    {
        return [
            'min' => null,
            'max' => null,
            'min_error' => '"@value" é muito curto (@min caracteres no mínimo)',
            'max_error' => '"@value" é muito longo (@max caracteres no máximo)',
        ];
    }

    /**
     * @see CoreExt_Validate_Abstract#_validate($value)
     */
    protected function _validate($value)
    {
        $length = strlen($value);

        if ($this->_hasOption('min') && $length < $this->getOption('min')) {
            throw new Exception(
                $this->_getErrorMessage(
                    'min_error',
                    ['@value' => $this->getSanitizedValue(), '@min' => $this->getOption('min')]
                )
            );
        }

        if ($this->_hasOption('max') && $length > $this->getOption('max')) {
            throw new Exception(
                $this->_getErrorMessage(
                    'max_error',
                    ['@value' => $this->getSanitizedValue(), '@max' => $this->getOption('max')]
                )
            );
        }

        return true;
    }

    /**
     * @see CoreExt_Validate_Abstract#_sanitize($value)
     */
    protected function _sanitize($value)
    {
        return (string) parent::_sanitize($value);
    }
}
