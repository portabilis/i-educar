<?php

class CoreExt_Validate_Email extends CoreExt_Validate_Abstract
{
    /**
     * @see CoreExt_Validate_Abstract#_getDefaultOptions()
     */
    protected function _getDefaultOptions()
    {
        return [
            'invalid' => 'Email inválido.'
        ];
    }

    /**
     * @see CoreExt_DataMapper#_getFindStatment($pkey) Sobre a conversão com floatval()
     * @see CoreExt_Validate_Abstract#_validate($value)
     */
    protected function _validate($value)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Exception($this->_getErrorMessage('invalid'));
        }

        return true;
    }

    /**
     * Mensagem padrão para erros de valor obrigatório.
     *
     * @var string
     */
    protected $_requiredMessage = 'Informe um email válido.';
}
